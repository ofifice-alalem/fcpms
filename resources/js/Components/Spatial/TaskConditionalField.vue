<template>
  <Transition name="fade">
    <div v-if="isVisible" class="p-4 rounded-2xl bg-indigo-500/5 dark:bg-indigo-500/10 border border-indigo-500/20 space-y-2 animate-fadeIn">
      <div class="flex items-center space-x-1.5 space-x-reverse text-indigo-600 dark:text-indigo-400 text-xs font-bold mb-1">
        <span>⚡</span>
        <span>حقل شرطي (ظهر بناءً على اختيارك السابق)</span>
      </div>
      <slot />
    </div>
  </Transition>
</template>

<script setup>
/**
 * TaskConditionalField.vue - الحقل الشرطي للمهام الميدانية الديناميكية
 */
import { computed } from 'vue'

const props = defineProps({
  dependsOnValue: { required: true },
  targetValue: { required: true }
})

const isVisible = computed(() => {
  if (Array.isArray(props.targetValue)) {
    return props.targetValue.includes(props.dependsOnValue)
  }
  return props.dependsOnValue === props.targetValue
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
