<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-[9999] overflow-y-auto" @keydown.esc="$emit('cancel')">
      <!-- Background overlay -->
      <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="$emit('cancel')"></div>
      </transition>

      <!-- Modal Container -->
      <div class="flex items-center justify-center min-h-screen p-4">
        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <!-- Modal Panel -->
          <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-sm w-full transition-colors duration-200 border border-slate-200 dark:border-slate-800 z-10">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-200/80 dark:border-slate-800">
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ t('logout_confirm_title', 'Logout of BetLink?') }}
              </h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
              <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
                {{ t('logout_confirm_message', 'Are you sure you want to logout?') }}
              </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200/80 dark:border-slate-800 flex gap-3 justify-end rounded-b-3xl transition-colors duration-200">
              <button
                type="button"
                @click="$emit('cancel')"
                :disabled="loading"
                class="px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {{ t('cancel', 'Cancel') }}
              </button>
              <button
                type="button"
                @click="handleConfirm"
                :disabled="loading"
                class="px-4 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2 shadow-xs"
              >
                <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loading ? t('signing_out', 'Logging out...') : t('confirm_logout', 'Logout') }}
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useLanguage } from '@/composables/useLanguage'

const emit = defineEmits(['confirm', 'cancel'])
const { logout } = useAuth()
const { t } = useLanguage()

const loading = ref(false)

async function handleConfirm() {
  loading.value = true
  try {
    await logout()
  } catch (error) {
    console.error('Logout failed:', error)
    loading.value = false
  }
}

defineExpose({
  loading
})
</script>

<style scoped>
/* Modal animations handled by transitions */
</style>
