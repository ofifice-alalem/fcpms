<template>
  <SpatialModal :show="show" title="تعيين إجازة رسمية للاستشاري (M1-D02)" @close="$emit('close')">
    <div class="space-y-4">
      <SpatialDropdown
        v-model="form.type"
        label="نوع الإجازة (BR-015)"
        :options="[
          { label: 'إجازة سنوية', value: 'annual' },
          { label: 'إجازة مرضية', value: 'sick' },
          { label: 'إجازة طارئة / غير مدفوعة', value: 'emergency' }
        ]"
      />

      <SpatialDatePicker
        v-model="form.start_date"
        label="تاريخ بدء الإجازة"
        :required="true"
      />

      <SpatialDatePicker
        v-model="form.end_date"
        label="تاريخ نهاية الإجازة"
        :required="true"
      />

      <SpatialTextarea
        v-model="form.notes"
        label="ملاحظات / سبب الإجازة"
        placeholder="أدخل تفاصيل وملاحظات الإجازة..."
      />
    </div>

    <template #footer>
      <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
      <SpatialButton variant="primary" :loading="isSubmitting" icon="🌴" @click="submit">
        حفظ وتعيين الإجازة
      </SpatialButton>
    </template>
  </SpatialModal>
</template>

<script setup>
/**
 * AssignLeave.vue - تعيين وتخصيص الإجازات للاستشاري (M1-D02)
 * BR-015 & BR-016: Leave policy management
 */
import { ref, reactive } from 'vue'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialDatePicker from '@/Components/Spatial/SpatialDatePicker.vue'
import SpatialTextarea from '@/Components/Spatial/SpatialTextarea.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

defineProps({
  show: { type: Boolean, default: false },
  consultant: { type: Object, default: null }
})

const emit = defineEmits(['close', 'success'])
const isSubmitting = ref(false)

const form = reactive({
  type: 'annual',
  start_date: '',
  end_date: '',
  notes: ''
})

const submit = () => {
  isSubmitting.value = true
  setTimeout(() => {
    isSubmitting.value = false
    emit('success', 'تم تعيين فترة الإجازة بنجاح.')
    emit('close')
  }, 500)
}
</script>
