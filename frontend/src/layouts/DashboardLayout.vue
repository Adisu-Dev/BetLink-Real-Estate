<template>
  <div class="h-screen w-screen flex overflow-hidden bg-white dark:bg-slate-950 font-sans antialiased m-0 p-0 select-none transition-colors duration-200">
    
    <!-- 1. Left Sidebar (Pinned Left, w-64) -->
    <AppSidebar 
      :is-mobile-open="isMobileDrawerOpen"
      @close="isMobileDrawerOpen = false"
    />

    <!-- 2. Right Canvas: Header + Breadcrumbs + Content Canvas + Natural Flow Footer -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-200 m-0 p-0">
      
      <!-- Top Sticky Navigation Header -->
      <AppHeader @toggle-drawer="isMobileDrawerOpen = !isMobileDrawerOpen" />

      <!-- Breadcrumbs Bar -->
      <Breadcrumbs class="w-full px-3 sm:px-5 py-2 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 m-0 flex-shrink-0 transition-colors" />

      <!-- Natural Scrollable Container (Main Content + Non-Fixed Footer) -->
      <div class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col justify-between select-text">
        
        <!-- Main Scrollable Page Content -->
        <main class="flex-1 px-3 sm:px-5 py-4 sm:py-5 space-y-5">
          <RouterView />
        </main>

        <!-- Natural Flow Footer (Positioned at bottom of scrollable content, NOT fixed) -->
        <footer class="w-full bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 px-3 sm:px-5 py-3 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3 flex-shrink-0 mt-auto transition-colors">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500 inline-block"></span>
            <span class="font-bold text-slate-700 dark:text-slate-300">BetLink v2026</span>
            <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">&bull;</span>
            <span class="text-slate-500 dark:text-slate-400 hidden sm:inline">{{ t('trusted_marketplace') }}</span>
          </div>
          <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 font-medium">
            <RouterLink to="/" class="hover:text-slate-900 dark:hover:text-white transition-colors">{{ t('public_portal') }}</RouterLink>
            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
            <span>&copy; 2026 BetLink</span>
          </div>
        </footer>
      </div>

    </div>

    <!-- Toast Notifications -->
    <Toast ref="toastComponent" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import AppSidebar from '../components/dashboard/AppSidebar.vue'
import AppHeader from '../components/dashboard/AppHeader.vue'
import Breadcrumbs from '../components/dashboard/Breadcrumbs.vue'
import Toast from '../components/dashboard/Toast.vue'
import { useToast } from '../composables/useToast'
import { useLanguage } from '../composables/useLanguage'

const isMobileDrawerOpen = ref(false)
const toastComponent = ref(null)
const { setToastInstance } = useToast()
const { t } = useLanguage()

onMounted(() => {
  if (toastComponent.value) {
    setToastInstance(toastComponent.value)
  }
})
</script>

<style scoped>
/* High-End Dark/Light Mode Responsive Canvas Shell */
</style>
