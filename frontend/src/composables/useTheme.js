import { ref, watch } from 'vue'

const isDark = ref(false)

// Initialize theme from localStorage ('theme') or system preference
function initTheme() {
  if (typeof window === 'undefined') return

  const savedTheme = localStorage.getItem('theme') || localStorage.getItem('betlink_theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    isDark.value = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  applyTheme(isDark.value)
}

function applyTheme(dark) {
  if (typeof document === 'undefined') return
  const root = document.documentElement
  if (dark) {
    root.classList.add('dark')
    localStorage.setItem('theme', 'dark')
    localStorage.setItem('betlink_theme', 'dark')
  } else {
    root.classList.remove('dark')
    localStorage.setItem('theme', 'light')
    localStorage.setItem('betlink_theme', 'light')
  }
}

// Watch for reactive changes
watch(isDark, (val) => {
  applyTheme(val)
})

// Initialize immediately on module load
initTheme()

export function useTheme() {
  function toggleTheme() {
    isDark.value = !isDark.value
  }

  function setTheme(theme) {
    isDark.value = theme === 'dark'
  }

  return {
    isDark,
    toggleTheme,
    setTheme
  }
}
