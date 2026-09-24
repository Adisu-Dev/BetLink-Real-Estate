<template>
  <Teleport to="body">
    <transition name="modal">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 z-[100001] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
        @click.self="$emit('cancel')"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-6 sm:p-7 max-w-sm w-full border border-slate-200 dark:border-slate-800 transition-colors my-auto text-left">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ title }}</h3>
          <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 font-medium leading-relaxed">{{ message }}</p>
          
          <div class="mt-6 flex gap-3 justify-end">
            <button
              @click="$emit('cancel')"
              type="button"
              class="px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer"
            >
              {{ cancelLabel || t('cancel', 'Cancel') }}
            </button>
            <button
              @click="$emit('confirm')"
              type="button"
              :class="[
                'px-4 py-2.5 text-xs sm:text-sm font-bold text-white rounded-xl shadow-xs transition-colors cursor-pointer',
                danger
                  ? 'bg-rose-600 hover:bg-rose-700'
                  : 'bg-emerald-600 hover:bg-emerald-700'
              ]"
            >
              {{ confirmLabel || t('confirm', 'Confirm') }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()

defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmLabel: {
    type: String,
    default: ''
  },
  cancelLabel: {
    type: String,
    default: ''
  },
  danger: {
    type: Boolean,
    default: false
  }
})

defineEmits(['confirm', 'cancel'])
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
