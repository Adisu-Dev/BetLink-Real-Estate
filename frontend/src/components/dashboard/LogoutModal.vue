<template>
  <Teleport to="body">
    <transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
          <!-- Background overlay with fade transition -->
          <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div v-if="show" class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="cancel"></div>
          </transition>

          <!-- Modal panel -->
          <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div v-if="show" class="inline-block align-middle bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-sm w-full border border-slate-200 dark:border-slate-800 z-10">
              <div class="bg-white dark:bg-slate-900 p-6 sm:p-7">
                <div class="sm:flex sm:items-start">
                  <!-- Content -->
                  <div class="text-left">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">
                      Logout of BetLink?
                    </h3>
                    <div class="mt-2">
                      <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium">
                        Are you sure you want to logout?
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Actions -->
              <div class="bg-slate-50 dark:bg-slate-800/60 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  @click="confirm"
                  :disabled="loading"
                  class="inline-flex justify-center rounded-xl px-4 py-2.5 bg-rose-600 text-xs sm:text-sm font-bold text-white hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-xs"
                >
                  <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ loading ? 'Logging out...' : 'Logout' }}
                </button>
                <button
                  type="button"
                  @click="cancel"
                  :disabled="loading"
                  class="inline-flex justify-center rounded-xl px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const loading = ref(false)

async function confirm() {
  loading.value = true
  // Simulate logout delay
  await new Promise(resolve => setTimeout(resolve, 500))
  emit('confirm')
  loading.value = false
}

function cancel() {
  if (!loading.value) {
    emit('cancel')
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>