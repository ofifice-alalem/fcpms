<template>
  <div class="space-y-1.5 w-full">
    <!-- Label -->
    <label v-if="label" :for="id" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>

    <!-- Input Field Wrapper -->
    <div class="relative flex items-center">
      <!-- Prefix Icon -->
      <div v-if="$slots.prefix || prefixIcon" class="absolute right-3 text-slate-400 pointer-events-none">
        <slot name="prefix">{{ prefixIcon }}</slot>
      </div>

      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
        class="w-full px-4 py-3 text-sm rounded-xl border transition-all duration-300 outline-none bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 shadow-sm"
        :class="[
          $slots.prefix || prefixIcon ? 'pr-10' : '',
          $slots.suffix || suffixIcon ? 'pl-10' : '',
          error
            ? 'border-rose-500/80 focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500'
            : success
              ? 'border-emerald-500/80 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500'
              : 'border-white/30 dark:border-white/10 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/20'
        ]"
      />

      <!-- Suffix Icon -->
      <div v-if="$slots.suffix || suffixIcon" class="absolute left-3 text-slate-400 pointer-events-none">
        <slot name="suffix">{{ suffixIcon }}</slot>
      </div>
    </div>

    <!-- Error or Hint Message -->
    <p v-if="error" class="text-xs text-rose-500 font-medium mt-1">{{ error }}</p>
    <p v-else-if="hint" class="text-xs text-slate-400 mt-1">{{ hint }}</p>
  </div>
</template>

<script setup>
/**
 * SpatialInput.vue - مدخل النصوص الزجاجي العالي الجودة
 */
defineProps({
  modelValue: { type: [String, Number], default: '' },
  id: { type: String, default: () => `spatial-input-${Math.random().toString(36).substr(2, 9)}` },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  error: { type: String, default: '' },
  success: { type: Boolean, default: false },
  hint: { type: String, default: '' },
  prefixIcon: { type: String, default: '' },
  suffixIcon: { type: String, default: '' }
})

defineEmits(['update:modelValue', 'blur', 'focus'])
</script>
