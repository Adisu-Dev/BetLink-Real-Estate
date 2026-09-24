<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
      {{ label }}
      <span v-if="required" class="text-rose-400">*</span>
    </label>

    <div class="relative rounded-lg shadow-sm">
      <select
        :id="id"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="[
          'block w-full rounded-lg text-sm bg-slate-900 border text-slate-100 placeholder-slate-500 transition-colors duration-150 focus:outline-none focus:ring-2 appearance-none pl-3.5 pr-10 py-2.5',
          error
            ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/20'
            : 'border-slate-700 focus:border-emerald-500 focus:ring-emerald-500/20',
          disabled && 'opacity-60 bg-slate-950 cursor-not-allowed'
        ]"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="" disabled selected>
          {{ placeholder }}
        </option>
        <option
          v-for="option in options"
          :key="option.value !== undefined ? option.value : option"
          :value="option.value !== undefined ? option.value : option"
        >
          {{ option.label || option }}
        </option>
      </select>

      <!-- Chevron Down Icon -->
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-rose-400">
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-slate-400">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: () => `select-${Math.random().toString(36).substring(2, 9)}`,
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    default: () => [],
  },
  error: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:modelValue'])
</script>
