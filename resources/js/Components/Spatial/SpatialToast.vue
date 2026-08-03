<template>
  <Transition name="slide-fade">
    <div
      v-if="visible"
      class="fixed bottom-6 left-6 z-50 max-w-md w-full p-4 rounded-2xl border backdrop-blur-2xl shadow-2xl flex items-center justify-between space-x-3 space-x-reverse"
      :class="[toastClasses]"
    >
      <div class="flex items-center space-x-3 space-x-reverse">
        <span class="text-xl">{{ icon }}</span>
        <div>
          <h5 class="text-sm font-bold">{{ title }}</h5>
          <p v-if="message" class="text-xs opacity-90 mt-0.5">{{ message }}</p>
        </div>
      </div>

      <button @click="dismiss" class="text-xs font-bold hover:opacity-75">✕</button>
    </div>
  </Transition>
</template>

<script setup>
/**
 * SpatialToast.vue - التنبيهات المنبثقة التلقائية
 */
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  type: { type: String, default: 'success' }, // success, error, warning, info
  title: { type: String, default: 'تمت العملية' },
  message: { type: String, default: '' },
  duration: { type: Number, default: 4000 }
})

const visible = ref(true)

const dismiss = () => {
  visible.value = false
}

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(dismiss, props.duration)
  }
})

const icon = computed(() => {
  switch (props.type) {
    case 'error': return '🚨'
    case 'warning': return '⚠️'
    case 'info': return 'ℹ️'
    case 'success':
    default: return '✅'
  }
})

const toastClasses = computed(() => {
  switch (props.type) {
    case 'error':
      return 'bg-rose-950/80 text-rose-100 border-rose-500/30 shadow-rose-900/50'
    case 'warning':
      return 'bg-amber-950/80 text-amber-100 border-amber-500/30 shadow-amber-900/50'
    case 'info':
      return 'bg-cyan-950/80 text-cyan-100 border-cyan-500/30 shadow-cyan-900/50'
    case 'success':
    default:
      return 'bg-emerald-950/80 text-emerald-100 border-emerald-500/30 shadow-emerald-900/50'
  }
})
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.3s ease-out;
}
.slide-fade-enter-from, .slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
