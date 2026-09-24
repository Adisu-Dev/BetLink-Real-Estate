<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
      {{ label }}
      <span v-if="required" class="text-rose-400">*</span>
    </label>

    <div class="relative rounded-lg shadow-sm">
      <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <slot name="prefix" />
      </div>

      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-lg text-sm bg-slate-900 border text-slate-100 placeholder-slate-500 transition-colors duration-150 focus:outline-none focus:ring-2',
          $slots.prefix ? 'pl-10' : 'pl-3.5',
          $slots.suffix ? 'pr-10' : 'pr-3.5',
          'py-2.5',
          error
            ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/20'
            : 'border-slate-700 focus:border-emerald-500 focus:ring-emerald-500/20',
          disabled && 'opacity-60 bg-slate-950 cursor-not-allowed'
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />

      <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
        <slot name="suffix" />
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
    default: () => `input-${Math.random().toString(36).substring(2, 9)}`,
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
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
  autocomplete: {
    type: String,
    default: 'off',
  },
})

defineEmits(['update:modelValue', 'blur', 'focus'])
</script>
