<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop Backdrop -->
        <div
          @click="closeOnBackdrop ? $emit('close') : null"
          class="fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity"
        ></div>

        <!-- Modal Container Card -->
        <div class="relative z-10 w-full max-w-lg">
          <SpatialModalCard :title="title" :subtitle="subtitle" @close="$emit('close')">
            <slot />

            <template v-if="$slots.footer" #footer>
              <slot name="footer" />
            </template>
          </SpatialModalCard>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
/**
 * SpatialModal.vue - النافذة المنبثقة التفاعلية مع الخصائص والتحكم بالخلفية
 */
import SpatialModalCard from './SpatialModalCard.vue'

defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  closeOnBackdrop: { type: Boolean, default: true }
})

defineEmits(['close'])
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
</style>
