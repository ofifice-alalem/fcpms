<template>
  <SpatialModal :show="show" title="تعيين قالب جدول العمل (M1-D03)" @close="$emit('close')">
    <div class="space-y-4">
      <SpatialDropdown
        v-model="form.work_schedule_template_id"
        label="قالب جدول العمل (BR-008)"
        :options="formattedSchedules"
        placeholder="اختر قالب الجدول المطلوبة..."
      />
    </div>

    <template #footer>
      <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
      <SpatialButton variant="primary" :loading="isSubmitting" icon="📅" @click="submit">
        تخصيص جدول العمل
      </SpatialButton>
    </template>
  </SpatialModal>
</template>

<script setup>
/**
 * AssignSchedule.vue - ربط الاستشاري بقالب جدول العمل (M1-D03)
 * BR-008: Work Schedule Binding
 */
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  consultant: { type: Object, default: null },
  schedules: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'success'])
const isSubmitting = ref(false)

const form = reactive({ work_schedule_template_id: null })

const formattedSchedules = computed(() => {
  return props.schedules.map(s => ({ label: s.name || `جدول رقم ${s.id}`, value: s.id }))
})

const submit = () => {
  if (!props.consultant) return
  isSubmitting.value = true

  router.put(route('hr.consultants.update', { id: props.consultant.id }), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم ربط جدول العمل بالاستشاري بنجاح.')
      emit('close')
    },
    onError: () => { isSubmitting.value = false }
  })
}
</script>
