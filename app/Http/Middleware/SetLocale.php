<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * 지원하는 locale 목록
     */
    protected array $supportedLocales = ['ko', 'en'];

    /**
     * 기본 locale
     */
    protected string $defaultLocale = 'ko';

    /**
     * Handle an incoming request.
     *
     * 사용자의 locale 설정에 따라 애플리케이션 locale을 동적으로 설정합니다.
     *
     * 우선순위:
     * 1. 로그인된 사용자의 저장된 locale
     * 2. 기본값 'ko'
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);
        App::setLocale($locale);

        return $next($request);
    }

    /**
     * 요청에서 적절한 locale을 결정합니다.
     */
    protected function determineLocale(Request $request): string
    {
        // 로그인된 사용자의 locale 확인
        if ($request->user() && $request->user()->locale) {
            $userLocale = $request->user()->locale;

            if (in_array($userLocale, $this->supportedLocales)) {
                return $userLocale;
            }
        }

        return $this->defaultLocale;
    }
}
