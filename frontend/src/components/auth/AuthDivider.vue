<template>
  <div class="relative flex items-center my-6">
    <div class="flex-grow border-t border-gray-300"></div>
    <span class="flex-shrink mx-4 text-sm text-gray-500 font-medium">{{ text }}</span>
    <div class="flex-grow border-t border-gray-300"></div>
  </div>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    default: 'OR'
  }
})
</script>
