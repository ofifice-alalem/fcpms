<template>
  <SpatialModal :show="show" title="تعطيل نموذج المهمة (M3-D02)" @close="$emit('close')">
    <div class="space-y-3">
      <p class="text-sm text-slate-700 dark:text-slate-300">
        هل أنت تأكد من تعطيل نموذج المهمة <span class="font-bold text-rose-500">{{ task?.name }}</span>؟
      </p>
      <p class="text-xs text-slate-500">
        عند تعطيل النموذج، لن يظهر للاستشاريين الميدانيين في الزيارات القادمة.
      </p>
    </div>

    <template #footer>
      <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
      <SpatialButton variant="destructive" :loading="isSubmitting" icon="🚫" @click="submit">
        تعطيل النموذج
      </SpatialButton>
    </template>
  </SpatialModal>
</template>

<script setup>
/**
 * DisableModal.vue - تأكيد تعطيل نماذج المهام (M3-D02)
 * DL-001 & DL-003: Confirm via Modal
 */
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  task: { type: Object, default: null }
})

const emit = defineEmits(['close', 'success'])
const isSubmitting = ref(false)

const submit = () => {
  if (!props.task) return
  isSubmitting.value = true

  router.patch(route('hr.tasks.change-status', props.task.id), { status: 'disabled' }, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم تعطيل نموذج المهمة بنجاح.')
      emit('close')
    },
    onError: () => { isSubmitting.value = false }
  })
}
</script>
