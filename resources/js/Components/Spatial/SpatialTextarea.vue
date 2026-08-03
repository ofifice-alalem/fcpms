<template>
  <div class="space-y-1.5 w-full">
    <!-- Label & Counter Header -->
    <div v-if="label || maxLength" class="flex items-center justify-between">
      <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
      </label>
      <span v-if="maxLength" class="text-[11px] font-semibold text-slate-400">
        {{ currentLength }}/{{ maxLength }}
      </span>
    </div>

    <!-- Textarea Input -->
    <textarea
      :value="modelValue"
      :rows="rows"
      :placeholder="placeholder"
      :disabled="disabled"
      :maxlength="maxLength"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full px-4 py-3 text-sm rounded-xl border transition-all duration-300 outline-none bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 shadow-sm border-white/30 dark:border-white/10 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/20 resize-y"
      :class="[error ? 'border-rose-500 focus:ring-rose-500/20' : '']"
    ></textarea>

    <p v-if="error" class="text-xs text-rose-500 font-medium mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
/**
 * SpatialTextarea.vue - مربع النص المتعدد الأسطر مع عدّاد الحروف
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  rows: { type: Number, default: 4 },
  placeholder: { type: String, default: '' },
  maxLength: { type: Number, default: null },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' }
})

defineEmits(['update:modelValue'])

const currentLength = computed(() => props.modelValue ? props.modelValue.length : 0)
</script>
