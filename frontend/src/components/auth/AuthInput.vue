<template>
  <div class="space-y-1.5 text-left">
    <label v-if="label" :for="id" class="block text-xs sm:text-sm font-medium text-slate-700 dark:text-slate-300">
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>
    <div class="relative">
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        class="w-full px-3.5 py-2.5 sm:py-3 text-sm rounded-xl border transition-colors outline-none"
        :class="[
          error 
            ? 'border-rose-300 dark:border-rose-700 bg-rose-50/40 dark:bg-rose-950/20 text-rose-900 dark:text-rose-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500' 
            : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500',
          disabled ? 'bg-slate-100 dark:bg-slate-800/50 cursor-not-allowed opacity-70' : ''
        ]"
      />
      <slot name="icon" />
    </div>
    <p v-if="error" class="text-xs text-rose-500">{{ error }}</p>
    <p v-else-if="hint" class="text-xs text-slate-500 dark:text-slate-400">{{ hint }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
  hint: String
})

defineEmits(['update:modelValue'])
</script>
