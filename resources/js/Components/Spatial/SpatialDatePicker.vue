<template>
  <div class="space-y-1.5 w-full">
    <div class="flex items-center justify-between">
      <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
        {{ label }}
        <span v-if="required" class="text-rose-500">*</span>
      </label>
      <button
        type="button"
        @click="selectToday"
        class="text-[11px] font-bold text-indigo-500 hover:text-indigo-600 dark:text-indigo-400"
      >
        اليوم
      </button>
    </div>

    <div class="relative flex items-center">
      <input
        type="date"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full px-4 py-3 text-sm rounded-xl border transition-all duration-300 outline-none bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl text-slate-800 dark:text-slate-100 border-white/30 dark:border-white/10 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm"
      />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialDatePicker.vue - منتقي التواريخ الفضائي
 */
defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  required: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue'])

const selectToday = () => {
  const today = new Date().toISOString().split('T')[0]
  emit('update:modelValue', today)
}
</script>
