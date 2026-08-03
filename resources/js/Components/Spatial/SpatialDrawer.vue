<template>
  <Teleport to="body">
    <Transition name="drawer-slide">
      <div v-if="show" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Backdrop Backdrop -->
        <div
          @click="$emit('close')"
          class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Drawer Content Panel -->
        <div class="fixed inset-y-0 left-0 max-w-full flex">
          <div class="w-screen max-w-md bg-white/90 dark:bg-slate-900/90 backdrop-blur-2xl border-r border-white/20 dark:border-white/10 shadow-2xl p-6 flex flex-col justify-between">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200/50 dark:border-slate-800/50">
              <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ title }}</h3>
              <button @click="$emit('close')" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-500">
                ✕
              </button>
            </div>

            <!-- Body Content -->
            <div class="flex-1 overflow-y-auto py-4 space-y-4">
              <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="pt-4 border-t border-slate-200/50 dark:border-slate-800/50">
              <slot name="footer" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
/**
 * SpatialDrawer.vue - الدرج المنزلق الجانبي للتعديلات السريعة
 */
defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: '' }
})

defineEmits(['close'])
</script>

<style scoped>
.drawer-slide-enter-active, .drawer-slide-leave-active {
  transition: transform 0.3s ease-in-out;
}
.drawer-slide-enter-from, .drawer-slide-leave-to {
  transform: translateX(-100%);
}
</style>
