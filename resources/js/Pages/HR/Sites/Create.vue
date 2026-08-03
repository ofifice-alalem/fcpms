<template>
  <SpatialDrawer :show="show" title="إضافة موقع ميداني جديد (M2-R01)" @close="$emit('close')">
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Site Name (FM-004: Required) -->
      <SpatialInput
        v-model="form.name"
        label="اسم الموقع الميداني"
        placeholder="مثال: موقع البريقة الميداني"
        :required="true"
        :error="errors.name"
      />

      <!-- Site Code (FM-004: Required & Unique) -->
      <SpatialInput
        v-model="form.code"
        label="رمز الموقع (Site Code)"
        placeholder="مثال: BRQ-101"
        :required="true"
        :error="errors.code"
      />

      <!-- Location / City -->
      <SpatialInput
        v-model="form.location"
        label="المدينة / الموقع الجغرافي"
        placeholder="مثال: طرابلس"
        :error="errors.location"
      />

      <!-- Status Option -->
      <SpatialRadioGroup
        v-model="form.status"
        label="حالة الموقع الميداني"
        :options="[
          { label: 'نشط (متاح للزيارات)', value: 'active' },
          { label: 'غير نشط (معطل)', value: 'inactive' }
        ]"
        :cols="2"
      />
    </form>

    <template #footer>
      <div class="flex items-center justify-end space-x-2 space-x-reverse">
        <SpatialButton variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
        <SpatialButton
          variant="primary"
          :loading="isSubmitting"
          icon="➕"
          @click="submit"
        >
          حفظ وإضافة الموقع
        </SpatialButton>
      </div>
    </template>
  </SpatialDrawer>
</template>

<script setup>
/**
 * Create.vue - نموذج إضافة موقع ميداني جديد (M2-R01)
 * FM-002: Use SpatialDrawer for medium forms
 * FM-004: Mark required fields with *
 * FM-007: Show inline errors
 */
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

defineProps({
  show: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'success'])

const isSubmitting = ref(false)
const errors = reactive({})

const form = reactive({
  name: '',
  code: '',
  location: '',
  status: 'active'
})

const submit = () => {
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  router.post(route('hr.sites.store'), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم إضافة الموقع الميداني بنجاح.')
      emit('close')
      form.name = ''
      form.code = ''
      form.location = ''
      form.status = 'active'
    },
    onError: (errs) => {
      isSubmitting.value = false
      Object.assign(errors, errs)
    }
  })
}
</script>
