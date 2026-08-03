<template>
  <label class="inline-flex items-center cursor-pointer select-none group">
    <div class="relative">
      <input
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        @change="$emit('update:modelValue', $event.target.checked)"
        class="sr-only"
      />
      <div
        class="w-5 h-5 rounded-lg border transition-all duration-300 flex items-center justify-center bg-white/70 dark:bg-slate-900/70 backdrop-blur-md shadow-sm border-slate-300 dark:border-slate-700 group-hover:border-indigo-500"
        :class="[
          modelValue ? 'bg-gradient-to-tr from-indigo-600 to-purple-600 border-transparent text-white shadow-indigo-500/30' : '',
          disabled ? 'opacity-50 cursor-not-allowed' : ''
        ]"
      >
        <span v-if="modelValue" class="text-xs font-black">✓</span>
      </div>
    </div>
    <span v-if="label || $slots.default" class="mr-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-indigo-500 transition-colors">
      <slot>{{ label }}</slot>
    </span>
  </label>
</template>

<script setup>
/**
 * SpatialCheckbox.vue - مربع اختيار مخصص
 */
defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>
