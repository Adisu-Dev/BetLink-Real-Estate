<template>
  <div class="min-h-screen bg-slate-100 dark:bg-slate-950 flex items-center justify-center px-4 transition-colors duration-200">
    <div class="max-w-md w-full text-center">
      <!-- Icon -->
      <div class="w-24 h-24 bg-red-100 dark:bg-red-950 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-200">
        <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>

      <!-- Error Code -->
      <h1 class="text-3xl font-extrabold text-navy-950 dark:text-slate-100 mb-4">403</h1>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-navy-950 dark:text-slate-100 mb-3">Access Denied</h2>

      <!-- Description -->
      <p class="text-slate-600 dark:text-slate-400 mb-8">
        You don't have permission to access this page. Please contact your administrator if you believe this is an error.
      </p>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button 
          @click="goBack"
          class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors font-medium"
        >
          Go Back
        </button>
        <button 
          @click="goToDashboard"
          class="px-6 py-3 bg-betlink-green text-white rounded-lg hover:bg-betlink-green-light transition-colors font-medium"
        >
          Go to Dashboard
        </button>
      </div>

      <!-- Additional Info -->
      <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700 transition-colors duration-200">
        <RouterLink to="/" class="flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-slate-200 transition-colors">
          <div class="w-6 h-6 bg-betlink-green rounded flex items-center justify-center">
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 text-white">
              <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <span class="font-semibold">Return to BetLink Home</span>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { user, getDashboardRoute } = useAuth()

function goBack() {
  router.back()
}

function goToDashboard() {
  if (user.value) {
    router.push(getDashboardRoute())
  } else {
    router.push('/login')
  }
}
</script>
