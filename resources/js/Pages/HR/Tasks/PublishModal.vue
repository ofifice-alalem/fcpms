<template>
  <SpatialModal :show="show" title="تأكيد نشر قوالب المهام (M3-D01)" @close="$emit('close')">
    <div class="space-y-3">
      <p class="text-sm text-slate-700 dark:text-slate-300">
        هل أنت تأكد من نشر نموذج المهمة <span class="font-bold text-indigo-500">{{ task?.name }}</span>؟
      </p>
      <p class="text-xs text-slate-500">
        بمجرد النشر، سيتمكن الاستشاريون الميدانيون المخصصون من رؤية وتعبئة هذه المهمة في زيارات المواقع المستهدفة.
      </p>
    </div>

    <template #footer>
      <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
      <SpatialButton variant="primary" :loading="isSubmitting" icon="🚀" @click="submit">
        نشر النموذج الآن
      </SpatialButton>
    </template>
  </SpatialModal>
</template>

<script setup>
/**
 * PublishModal.vue - تأكيد نشر نماذج المهام (M3-D01)
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

  router.post(route('hr.tasks.index'), { id: props.task.id, status: 'published' }, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم نشر نموذج المهمة وتفعيله بنجاح.')
      emit('close')
    },
    onError: () => { isSubmitting.value = false }
  })
}
</script>
