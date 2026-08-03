<template>
  <SpatialDrawer :show="show" title="تعديل بيانات الاستشاري (M1-R01)" @close="$emit('close')">
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Full Name (FM-004) -->
      <SpatialInput
        v-model="form.full_name"
        label="الاسم الكامل"
        :required="true"
        :error="errors.full_name"
      />

      <!-- Employee Number (Disabled) -->
      <SpatialInput
        v-model="form.employee_number"
        label="الرقم الوظيفي"
        :readonly="true"
        :disabled="true"
        hint="الرقم الوظيفي ثابت وغير قابل للتعديل"
      />

      <!-- Phone Number -->
      <SpatialInput
        v-model="form.phone"
        label="رقم الهاتف"
        placeholder="09XXXXXXXX"
        :error="errors.phone"
      />

      <!-- Specialization -->
      <SpatialInput
        v-model="form.specialization"
        label="التخصص الإشرافي"
        placeholder="مثال: هندسة مدنية / تكييف وتبريد"
        :error="errors.specialization"
      />
    </form>

    <template #footer>
      <div class="flex items-center justify-end space-x-2 space-x-reverse">
        <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
        <SpatialButton
          variant="primary"
          :loading="isSubmitting"
          icon="💾"
          @click="submit"
        >
          حفظ التعديلات
        </SpatialButton>
      </div>
    </template>
  </SpatialDrawer>
</template>

<script setup>
/**
 * Edit.vue - نموذج تعديل بيانات الاستشاري (M1-R01)
 * FM-002: SpatialDrawer for form
 * FM-007: Inline validation errors
 */
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  consultant: { type: Object, default: null }
})

const emit = defineEmits(['close', 'success'])
const isSubmitting = ref(false)
const errors = reactive({})

const form = reactive({
  full_name: '',
  employee_number: '',
  phone: '',
  specialization: ''
})

watch(() => props.consultant, (newVal) => {
  if (newVal) {
    form.full_name = newVal.full_name || ''
    form.employee_number = newVal.employee_number || ''
    form.phone = newVal.phone || ''
    form.specialization = newVal.specialization || ''
  }
}, { immediate: true })

const submit = () => {
  if (!props.consultant) return
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  router.put(route('hr.consultants.update', { id: props.consultant.id }), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم تحديث بيانات الاستشاري بنجاح.')
      emit('close')
    },
    onError: (errs) => {
      isSubmitting.value = false
      Object.assign(errors, errs)
    }
  })
}
</script>
