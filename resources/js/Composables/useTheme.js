import { ref, onMounted } from 'vue'

const isDark = ref(true)
let initialized = false

export function useTheme() {
  const applyThemeToDOM = (dark) => {
    if (typeof window === 'undefined') return
    if (dark) {
      document.documentElement.classList.add('dark')
      document.body.classList.add('body-bg-dark')
      document.body.classList.remove('body-bg-light')
    } else {
      document.documentElement.classList.remove('dark')
      document.body.classList.add('body-bg-light')
      document.body.classList.remove('body-bg-dark')
    }
  }

  const initTheme = () => {
    if (typeof window === 'undefined') return
    const saved = localStorage.getItem('theme')
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      isDark.value = true
    } else if (saved === 'light') {
      isDark.value = false
    } else {
      isDark.value = true
    }
    applyThemeToDOM(isDark.value)
    initialized = true
  }

  const toggleTheme = () => {
    isDark.value = !isDark.value
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    applyThemeToDOM(isDark.value)
  }

  onMounted(() => {
    if (!initialized) {
      initTheme()
    } else {
      isDark.value = document.documentElement.classList.contains('dark')
      applyThemeToDOM(isDark.value)
    }
  })

  return {
    isDark,
    toggleTheme,
    initTheme
  }
}
