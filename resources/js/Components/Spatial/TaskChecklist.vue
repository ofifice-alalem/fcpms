<template>
  <div class="space-y-3 w-full">
    <!-- Progress Indicator Header -->
    <div class="flex items-center justify-between text-xs font-bold">
      <span class="text-slate-700 dark:text-slate-300">قائمة التحقق الميدانية</span>
      <span class="text-indigo-500 font-mono">{{ completedCount }}/{{ items.length }} مكتمل</span>
    </div>
    <SpatialProgressBar :value="completedCount" :max="items.length" :showLabel="false" />

    <!-- Checklist Items -->
    <div class="space-y-2 pt-1">
      <div
        v-for="(item, idx) in items"
        :key="idx"
        @click="toggleItem(idx)"
        class="p-3 rounded-xl border transition-all duration-200 cursor-pointer flex items-center justify-between"
        :class="[
          item.completed
            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-800 dark:text-emerald-300 line-through opacity-80'
            : 'bg-white/60 dark:bg-slate-900/60 border-white/20 dark:border-white/10 text-slate-800 dark:text-slate-200 hover:border-indigo-400'
        ]"
      >
        <span class="text-xs font-bold">{{ item.label }}</span>
        <SpatialCheckbox :modelValue="item.completed" @update:modelValue="toggleItem(idx)" />
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * TaskChecklist.vue - قائمة التحقق التفاعلية مع حساب الأداء المباشر
 */
import { computed } from 'vue'
import SpatialProgressBar from './SpatialProgressBar.vue'
import SpatialCheckbox from './SpatialCheckbox.vue'

const props = defineProps({
  items: { type: Array, required: true } // [{ label, completed }]
})

const emit = defineEmits(['update:items'])

const completedCount = computed(() => props.items.filter(i => i.completed).length)

const toggleItem = (index) => {
  const updated = [...props.items]
  updated[index].completed = !updated[index].completed
  emit('update:items', updated)
}
</script>
