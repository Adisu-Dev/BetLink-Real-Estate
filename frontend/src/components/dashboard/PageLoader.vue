<template>
  <div class="flex items-center justify-center py-12">
    <div class="flex flex-col items-center gap-4">
      <div class="relative w-12 h-12">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-blue-500 rounded-full animate-pulse" />
        <div class="absolute inset-1 bg-white rounded-full" />
      </div>
      <p class="text-gray-600 font-medium">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Loading...'
  }
})
</script>
