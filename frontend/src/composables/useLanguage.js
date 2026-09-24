import { ref, computed } from 'vue'
import en from '../locales/en.js'
import am from '../locales/am.js'
import om from '../locales/om.js'
import ti from '../locales/ti.js'

export const SUPPORTED_LANGUAGES = [
  { code: 'en', name: 'English', nativeName: 'English', flag: '🇺🇸', flagCode: 'us', dir: 'ltr' },
  { code: 'am', name: 'Amharic', nativeName: 'አማርኛ', flag: '🇪🇹', flagCode: 'et', dir: 'ltr' },
  { code: 'om', name: 'Afaan Oromoo', nativeName: 'Afaan Oromoo', flag: '🇪🇹', flagCode: 'et', dir: 'ltr' },
  { code: 'ti', name: 'Tigrinya', nativeName: 'ትግርኛ', flag: '🇪🇹', flagCode: 'et', dir: 'ltr' },
]

const translations = {
  en,
  am,
  om,
  ti
}

// Global shared reactive locale state (singleton across all components)
// Default language is strictly English ('en')
const currentLang = ref('en')

// Helper to look up nested dot notation or flat keys
function getNestedTranslation(obj, path) {
  if (!obj || !path) return undefined
  
  // Direct flat key match
  if (obj[path] !== undefined && typeof obj[path] === 'string') return obj[path]
  
  // Dot notation traversal: 'nav.home' -> obj.nav.home
  const segments = path.split('.')
  let current = obj
  for (const seg of segments) {
    if (current && typeof current === 'object' && seg in current) {
      current = current[seg]
    } else {
      return undefined
    }
  }
  return typeof current === 'string' ? current : undefined
}

// Initialize from localStorage or default strictly to 'en'
function initLanguage() {
  if (typeof window === 'undefined') return
  const saved = localStorage.getItem('betlink_lang')
  if (saved && translations[saved]) {
    currentLang.value = saved
  } else {
    currentLang.value = 'en'
    localStorage.setItem('betlink_lang', 'en')
  }

  if (document && document.documentElement) {
    document.documentElement.lang = currentLang.value
    document.documentElement.setAttribute('dir', 'ltr')
  }
}

initLanguage()

export function useLanguage() {
  function setLanguage(code) {
    if (translations[code]) {
      currentLang.value = code
      if (typeof window !== 'undefined') {
        localStorage.setItem('betlink_lang', code)
        if (document && document.documentElement) {
          document.documentElement.lang = code
          document.documentElement.setAttribute('dir', 'ltr')
        }
      }
    }
  }

  function t(key, fallback = undefined, params = {}) {
    if (!key) return (fallback !== undefined && fallback !== null) ? fallback : ''
    
    // Explicitly read currentLang.value first to establish reactive dependency in computed properties and templates
    const activeLang = currentLang.value
    const currentDict = translations[activeLang] || translations.en
    const enDict = translations.en

    // 1. Try key in current locale dictionary
    let value = getNestedTranslation(currentDict, key)
    
    // 2. Check inside dashboard group if flat key in current locale
    if (value === undefined && currentDict.dashboard) {
      value = getNestedTranslation(currentDict.dashboard, key)
    }

    // 3. Fallback to English dictionary if current locale lacks the key
    if (value === undefined && activeLang !== 'en') {
      value = getNestedTranslation(enDict, key)
      if (value === undefined && enDict.dashboard) {
        value = getNestedTranslation(enDict.dashboard, key)
      }
    }

    // 4. Fallback to provided fallback or key itself
    if (value === undefined || typeof value !== 'string') {
      value = (fallback !== undefined && fallback !== null) ? fallback : key
    }

    // 5. Parameter interpolation: {name} -> params.name
    if (params && Object.keys(params).length > 0 && typeof value === 'string') {
      return value.replace(/\{(\w+)\}/g, (match, paramName) => {
        return params[paramName] !== undefined ? params[paramName] : match
      })
    }

    return value
  }

  const currentLanguageObject = computed(() => {
    return SUPPORTED_LANGUAGES.find(l => l.code === currentLang.value) || SUPPORTED_LANGUAGES[0]
  })

  return {
    currentLang,
    currentLanguageObject,
    languages: SUPPORTED_LANGUAGES,
    setLanguage,
    t
  }
}
