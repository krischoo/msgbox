import { createI18n } from 'vue-i18n'
import ko from './locales/ko.json'
import en from './locales/en.json'

/**
 * 브라우저 언어 감지 및 i18n 초기화
 *
 * 우선순위:
 * 1. 로그인된 사용자의 저장된 locale (Inertia props에서 전달)
 * 2. 브라우저 언어 설정
 * 3. 기본값 'ko'
 */
const browserLocale = navigator.language.split('-')[0]
const supportedLocales = ['ko', 'en']
const defaultLocale = supportedLocales.includes(browserLocale) ? browserLocale : 'ko'

const i18n = createI18n({
  legacy: false, // Composition API 사용
  locale: defaultLocale,
  fallbackLocale: 'en',
  messages: { ko, en },
})

/**
 * 런타임에 locale 변경
 * @param {string} locale - 'ko' 또는 'en'
 */
export function setLocale(locale) {
  if (supportedLocales.includes(locale)) {
    i18n.global.locale.value = locale
  }
}

/**
 * 현재 locale 반환
 * @returns {string}
 */
export function getLocale() {
  return i18n.global.locale.value
}

export default i18n
