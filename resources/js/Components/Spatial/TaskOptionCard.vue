<template>
  <div
    @click="$emit('select', option.value)"
    class="p-4 rounded-2xl border transition-all duration-300 cursor-pointer backdrop-blur-xl relative overflow-hidden flex items-center space-x-3 space-x-reverse"
    :class="[
      isSelected
        ? 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-indigo-500 ring-2 ring-indigo-500/30 text-indigo-950 dark:text-indigo-100 shadow-md'
        : 'bg-white/60 dark:bg-slate-900/60 border-white/20 dark:border-white/10 hover:border-indigo-300 dark:hover:border-indigo-800 text-slate-700 dark:text-slate-300'
    ]"
  >
    <div
      class="w-6 h-6 rounded-full border flex items-center justify-center font-bold text-xs"
      :class="[isSelected ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-400']"
    >
      <span v-if="isSelected">✓</span>
    </div>
    <div class="flex-1">
      <h5 class="text-xs font-bold">{{ option.label }}</h5>
      <p v-if="option.description" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ option.description }}</p>
    </div>
  </div>
</template>

<script setup>
/**
 * TaskOptionCard.vue - بطاقة الخيار الفردي للمهام التفاعلية
 */
defineProps({
  option: { type: Object, required: true }, // { label, value, description }
  isSelected: { type: Boolean, default: false }
})

defineEmits(['select'])
</script>
