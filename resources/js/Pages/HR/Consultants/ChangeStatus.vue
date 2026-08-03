<template>
  <SpatialModal :show="show" title="تغيير الحالة الوظيفية للاستشاري (M1-D01)" @close="$emit('close')">
    <div class="space-y-4">
      <p class="text-xs text-slate-500">اختر الحالة الوظيفية الجديدة للاستشاري <span class="font-bold text-slate-800 dark:text-slate-100">{{ consultant?.full_name }}</span>:</p>

      <SpatialRadioGroup
        v-model="form.status"
        label="الحالة الوظيفية الجديدة (BR-003)"
        :options="[
          { label: 'نشط (على رأس العمل)', value: 'active', description: 'يسمح بالدخول وتلقي المهام' },
          { label: 'في إجازة رسمية', value: 'vacation', description: 'تأجيل احتساب الغياب والأداء' },
          { label: 'غير نشط (معطل)', value: 'inactive', description: 'حظر الدخول وحظر التعيين' }
        ]"
        :cols="1"
      />
    </div>

    <template #footer>
      <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
      <SpatialButton variant="primary" :loading="isSubmitting" icon="🔄" @click="submit">
        تحديث الحالة الوظيفية
      </SpatialButton>
    </template>
  </SpatialModal>
</template>

<script setup>
/**
 * ChangeStatus.vue - تغيير الحالة الوظيفية للاستشاري (M1-D01)
 * BR-003: Employment status logic
 * DL-001 & DL-003: Confirmation modal window
 */
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  consultant: { type: Object, default: null }
})

const emit = defineEmits(['close', 'success'])
const isSubmitting = ref(false)

const form = reactive({ status: 'active' })

watch(() => props.consultant, (c) => {
  if (c) form.status = c.status || 'active'
}, { immediate: true })

const submit = () => {
  if (!props.consultant) return
  isSubmitting.value = true

  router.patch(route('hr.consultants.change-status', { id: props.consultant.id }), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم تغيير الحالة الوظيفية بنجاح.')
      emit('close')
    },
    onError: () => { isSubmitting.value = false }
  })
}
</script>
