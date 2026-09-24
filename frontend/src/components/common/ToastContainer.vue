<template>
  <div class="fixed top-5 right-4 sm:right-6 z-[9999] flex flex-col space-y-3 pointer-events-none max-w-sm w-full px-4 sm:px-0">
    <TransitionGroup
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="-translate-y-3 opacity-0 sm:translate-y-0 sm:translate-x-3"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <ToastItem
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        :toast="toast"
        @close="() => toastStore.remove(toast.id)"
      />
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToastStore } from '../../stores/toast'
import ToastItem from './ToastItem.vue'

const toastStore = useToastStore()
</script>
