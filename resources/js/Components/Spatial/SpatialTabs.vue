<template>
  <div class="w-full space-y-4">
    <!-- Tab Controls Container -->
    <div class="inline-flex p-1.5 rounded-2xl bg-white/40 dark:bg-slate-900/60 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-inner">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="selectTab(tab.id)"
        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 relative flex items-center space-x-2 space-x-reverse"
        :class="[
          modelValue === tab.id
            ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/25 scale-[1.02]'
            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white/20 dark:hover:bg-slate-800/40'
        ]"
      >
        <span v-if="tab.icon" class="text-base">{{ tab.icon }}</span>
        <span>{{ tab.label }}</span>
        <span
          v-if="tab.count !== undefined"
          class="px-2 py-0.5 text-xs rounded-full"
          :class="[
            modelValue === tab.id
              ? 'bg-white/30 text-white'
              : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
          ]"
        >
          {{ tab.count }}
        </span>
      </button>
    </div>

    <!-- Active Content Panel Slot -->
    <div class="transition-opacity duration-300">
      <slot :activeTab="modelValue" />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialTabs.vue - بدّال التبويبات الفضائي
 */
const props = defineProps({
  tabs: { type: Array, required: true }, // Array of { id, label, icon, count }
  modelValue: { type: [String, Number], required: true }
})

const emit = defineEmits(['update:modelValue', 'change'])

const selectTab = (tabId) => {
  emit('update:modelValue', tabId)
  emit('change', tabId)
}
</script>
