import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index.js'
import './assets/index.css'
import { useLanguage } from './composables/useLanguage'

const app = createApp(App)
const pinia = createPinia()
const { t } = useLanguage()

// Global i18n template helper
app.config.globalProperties.$t = (key, fallback, params) => t(key, fallback, params)

app.use(pinia)
app.use(router)

app.mount('#app')
