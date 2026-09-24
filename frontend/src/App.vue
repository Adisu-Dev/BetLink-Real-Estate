<template>
  <div id="betlink-app" 
       :class="isDark ? 'dark' : ''"
       class="min-h-screen bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white transition-colors duration-200">
    <RouterView />
    <ToastContainer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import ToastContainer from './components/common/ToastContainer.vue'
import { useAuthStore } from './stores/auth'
import { useTheme } from './composables/useTheme'

const authStore = useAuthStore()
const { isDark } = useTheme()

onMounted(() => {
  authStore.init()
})
</script>
