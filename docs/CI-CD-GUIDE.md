# msgbox CI/CD 배포 가이드

## 개요

이 문서는 msgbox (addy.io fork) 프로젝트의 CI/CD 자동 배포 시스템을 설명합니다.
GitHub에 코드를 push하면 Oracle Cloud 서버에 자동으로 배포됩니다.

---

## 시스템 구성

### 인프라 구조

```
로컬 (Mac)                    GitHub                     Oracle Cloud
┌──────────────┐         ┌──────────────┐         ┌──────────────────────┐
│   msgbox/    │  push   │  krischoo/   │   SSH   │  168.107.56.217      │
│   코드 편집   │ ──────► │   msgbox     │ ──────► │  Docker 컨테이너      │
└──────────────┘         └──────────────┘         └──────────────────────┘
```

### 서버 디렉토리 구조

| 경로 | 용도 |
|------|------|
| `~/addy-code/` | GitHub 코드 clone (볼륨 마운트 소스) |
| `~/addy/` | Docker Compose 설정, .env 파일 |
| `~/addy/data/` | DB 데이터, DKIM 키, 메일 데이터 |

---

## Docker 아키텍처

### 컨테이너 구성

현재 서버에서 **3개의 Docker 컨테이너**가 실행 중입니다:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        Oracle Cloud 서버                                 │
│                                                                         │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                    Docker Compose 네트워크                        │   │
│  │                                                                   │   │
│  │  ┌─────────────────┐  ┌──────────────┐  ┌──────────────────┐    │   │
│  │  │   addy_app      │  │  addy_db     │  │   addy_redis     │    │   │
│  │  │                 │  │              │  │                  │    │   │
│  │  │  Laravel App    │  │   MariaDB    │  │     Redis        │    │   │
│  │  │  + Postfix      │  │              │  │                  │    │   │
│  │  │  + Nginx        │  │  Port: 3306  │  │   Port: 6379     │    │   │
│  │  │                 │  │  (내부전용)   │  │   (내부전용)      │    │   │
│  │  │  Port: 25(SMTP) │  │              │  │                  │    │   │
│  │  │  Port: 8000(Web)│  └──────────────┘  └──────────────────┘    │   │
│  │  └────────┬────────┘                                             │   │
│  │           │                                                       │   │
│  └───────────┼───────────────────────────────────────────────────────┘   │
│              │ 볼륨 마운트                                               │
│  ┌───────────┴───────────┐                                              │
│  │    ~/addy-code/       │  ← GitHub에서 pull한 코드                     │
│  │    ~/addy/data/       │  ← DKIM, 설정, 스토리지                       │
│  │    ~/addy/db/         │  ← MariaDB 데이터 파일                        │
│  └───────────────────────┘                                              │
└─────────────────────────────────────────────────────────────────────────┘
```

### 컨테이너 상세 정보

| 컨테이너명 | 이미지 | 역할 | 포트 |
|-----------|--------|------|------|
| **addy_app** | `anonaddy/anonaddy:latest` | Laravel 앱 + Nginx + Postfix | 25(SMTP), 8000(Web) |
| **addy_db** | `mariadb:10` | MariaDB 데이터베이스 | 3306 (내부전용) |
| **addy_redis** | `redis:7-alpine` | 캐시/세션/큐 관리 | 6379 (내부전용) |

### docker-compose.yml 전체 구성

```yaml
services:
  # MariaDB 데이터베이스
  db:
    image: mariadb:10
    container_name: addy_db
    restart: always
    command:
      - --character-set-server=utf8mb4
      - --collation-server=utf8mb4_unicode_ci
    volumes:
      - ./db:/var/lib/mysql          # DB 데이터 영속화
    environment:
      - MYSQL_RANDOM_ROOT_PASSWORD=yes
      - MYSQL_DATABASE=anonaddy
      - MYSQL_USER=anonaddy
      - MYSQL_PASSWORD=${DB_PASSWORD}

  # Redis 캐시/세션
  redis:
    image: redis:7-alpine
    container_name: addy_redis
    restart: always

  # 메인 애플리케이션
  addy:
    image: anonaddy/anonaddy:latest
    container_name: addy_app
    restart: always
    depends_on:
      - db
      - redis
    ports:
      - "25:25"                       # SMTP (메일 수신)
      - "127.0.0.1:8000:8000"         # Web (Nginx 프록시)
    volumes:
      - ./data:/data                  # DKIM, 설정, 스토리지
      - /home/ubuntu/addy-code:/var/www/anonaddy  # 코드 마운트
    env_file:
      - .env
    environment:
      - DB_HOST=db
      - DB_DATABASE=anonaddy
      - DB_USERNAME=anonaddy
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
```

### 볼륨 마운트 상세

| 호스트 경로 | 컨테이너 경로 | 용도 |
|------------|--------------|------|
| `~/addy/data/` | `/data` | DKIM 키, Postfix 설정, Laravel 스토리지 |
| `~/addy/db/` | `/var/lib/mysql` | MariaDB 데이터 파일 |
| `~/addy-code/` | `/var/www/anonaddy` | **애플리케이션 코드** (CI/CD 핵심) |

### data 폴더 구조

```
~/addy/data/
├── config/          # 애플리케이션 설정
├── dkim/            # DKIM 서명 키 (메일 인증)
├── postfix/         # Postfix 메일 서버 설정
└── storage/         # Laravel 스토리지 (로그, 캐시, 업로드)
```

### addy_app 컨테이너 내부 구조

`anonaddy/anonaddy:latest` 이미지는 다음을 포함합니다:

```
┌─────────────────────────────────────────────┐
│            addy_app 컨테이너                 │
│                                             │
│  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Nginx     │  │      PHP-FPM        │  │
│  │  (Port 8000)│──│  (Laravel App)      │  │
│  └─────────────┘  └─────────────────────┘  │
│                                             │
│  ┌─────────────┐  ┌─────────────────────┐  │
│  │  Postfix    │  │   Supervisor        │  │
│  │  (Port 25)  │  │   (프로세스 관리)    │  │
│  └─────────────┘  └─────────────────────┘  │
│                                             │
│  /var/www/anonaddy ← ~/addy-code 마운트    │
└─────────────────────────────────────────────┘
```

### Docker 명령어 모음

```bash
# SSH 접속
ssh -i ssh-key-2026-01-05.key ubuntu@168.107.56.217

# 컨테이너 상태 확인
docker ps

# 컨테이너 로그 보기 (실시간)
cd ~/addy
docker compose logs -f addy      # 앱 로그
docker compose logs -f db        # DB 로그

# 컨테이너 재시작
docker compose restart addy      # 앱만 재시작
docker compose restart           # 전체 재시작

# 컨테이너 중지/시작
docker compose stop
docker compose start

# 컨테이너 내부 접속 (디버깅)
docker compose exec addy bash    # 앱 컨테이너 쉘
docker compose exec db bash      # DB 컨테이너 쉘

# Laravel Artisan 명령 실행
docker compose exec addy php artisan migrate      # 마이그레이션
docker compose exec addy php artisan queue:work   # 큐 워커
docker compose exec addy php artisan tinker       # REPL

# 이미지 업데이트 (새 버전 배포 시)
docker compose pull
docker compose up -d
```

### 왜 Docker를 사용하는가?

1. **환경 일관성**: 개발/운영 환경 동일하게 유지
2. **의존성 격리**: PHP, Nginx, Postfix 등 복잡한 설정 이미지로 패키징
3. **쉬운 복구**: 컨테이너 삭제 후 재생성하면 초기화
4. **볼륨 마운트**: 코드만 외부에서 관리 → CI/CD 연동 용이

### CI/CD와 Docker의 관계

```
git push
    │
    ▼
GitHub Actions
    │
    ▼
SSH 접속 → git pull (~/addy-code)
    │
    ▼
볼륨 마운트로 컨테이너 내부 코드 자동 반영
    │
    ▼
php artisan optimize:clear (캐시 초기화)
    │
    ▼
새 코드 적용 완료 (컨테이너 재시작 불필요)
```

---

## 배포 방법

### 기본 배포 (자동)

```bash
# 1. 코드 수정
cd /Users/shkang/Sync/egg-project/msgbox
# ... 코드 편집 ...

# 2. 커밋 & 푸시 → 자동 배포
git add .
git commit -m "변경 내용 설명"
git push origin master
```

### 배포 상태 확인

```bash
# GitHub Actions 실행 결과 확인
gh run list --limit 5

# 특정 실행의 상세 로그
gh run view <run_id> --log
```

### 수동 배포 (긴급 시)

```bash
# SSH로 직접 서버 접속
ssh -i ssh-key-2026-01-05.key ubuntu@168.107.56.217

# 서버에서 수동 배포
cd ~/addy-code
git pull origin master
cd ~/addy
docker compose exec addy php artisan optimize:clear
docker compose exec addy php artisan view:clear
```

---

## GitHub Actions 워크플로우

### 파일 위치
`.github/workflows/deploy.yml`

### 트리거 조건
- `master` 브랜치에 push 시 자동 실행

### 실행 단계

1. **SSH 키 준비**
   - GitHub Secrets에 저장된 base64 인코딩 SSH 키를 디코딩
   - `/tmp/ssh_key` 파일로 저장

2. **서버 접속 및 배포**
   - SSH로 Oracle 서버 접속
   - `git pull`로 최신 코드 동기화
   - Laravel 캐시 초기화
   - Postfix 릴레이 설정 갱신

3. **정리**
   - SSH 키 파일 삭제

### GitHub Secrets 설정

| Secret 이름 | 값 | 설명 |
|-------------|-----|------|
| SERVER_HOST | 168.107.56.217 | Oracle 서버 IP |
| SERVER_USER | ubuntu | SSH 사용자명 |
| SSH_KEY | (base64 인코딩된 키) | SSH 개인 키 |

---

## 볼륨 마운트 구조

### docker-compose.yml 설정

```yaml
services:
  addy:
    image: anonaddy/anonaddy:latest
    volumes:
      - ./data:/data
      - /home/ubuntu/addy-code:/var/www/anonaddy  # 코드 마운트
```

### 작동 원리

```
~/addy-code/ (호스트)
    │
    │ 볼륨 마운트
    ▼
/var/www/anonaddy (컨테이너)
```

- 호스트의 `~/addy-code` 코드가 컨테이너 내부에 마운트됨
- `git pull` 하면 컨테이너 내부 코드도 즉시 반영
- 컨테이너 재시작 없이 코드 변경 적용 가능

---

## 문제 해결

### 배포 실패 시

1. **GitHub Actions 로그 확인**
   ```bash
   gh run view <run_id> --log
   ```

2. **일반적인 오류 및 해결**

   | 오류 | 원인 | 해결 |
   |------|------|------|
   | `ssh: no key found` | SSH 키 형식 문제 | base64 인코딩 키로 재설정 |
   | `Permission denied` | 파일 권한 문제 | `sudo chown -R ubuntu:ubuntu ~/addy-code` |
   | `Connection timeout` | 네트워크/방화벽 | Oracle Cloud 보안 규칙 확인 |

### 서버 상태 확인

```bash
# SSH 접속
ssh -i ssh-key-2026-01-05.key ubuntu@168.107.56.217

# 컨테이너 상태
docker ps

# 로그 확인
cd ~/addy
docker compose logs -f addy
```

### 캐시 문제 해결

```bash
# Laravel 캐시 완전 초기화
docker compose exec addy php artisan cache:clear
docker compose exec addy php artisan config:clear
docker compose exec addy php artisan route:clear
docker compose exec addy php artisan view:clear
docker compose exec addy php artisan optimize:clear
```

---

## 주의사항

### 절대 GitHub에 올리면 안 되는 파일

- `ssh-key-*.key` - SSH 개인 키 (.gitignore에 추가됨)
- `.env` - 서버에만 존재해야 함
- `data/` - DB, DKIM 키 등 민감 데이터

### 서버 리소스 제한

- RAM: 1GB (빌드 작업은 로컬/GitHub에서만)
- 컨테이너 재시작 시 일시적 서비스 중단 가능

### 롤백 방법

```bash
# 특정 커밋으로 롤백
ssh -i ssh-key-2026-01-05.key ubuntu@168.107.56.217
cd ~/addy-code
git log --oneline -10  # 이전 커밋 확인
git checkout <commit_hash>
cd ~/addy
docker compose exec addy php artisan optimize:clear
```

---

## 파일 구조 요약

```
msgbox/                          # 로컬 프로젝트 루트
├── .github/
│   └── workflows/
│       └── deploy.yml           # CI/CD 워크플로우
├── docs/
│   └── CI-CD-GUIDE.md          # 이 문서
├── app/                         # Laravel 백엔드
├── resources/                   # Vue.js 프론트엔드
├── public/
│   └── build/                   # Vite 빌드 결과물
├── vendor/                      # Composer 패키지
├── ssh-key-2026-01-05.key      # SSH 키 (gitignore됨)
└── .gitignore
```

---

## 연락처 및 참고

- **서비스 URL**: https://msgbox.kr
- **GitHub 저장소**: https://github.com/krischoo/msgbox
- **원본 프로젝트**: https://github.com/anonaddy/anonaddy
- **서버**: Oracle Cloud (168.107.56.217)
