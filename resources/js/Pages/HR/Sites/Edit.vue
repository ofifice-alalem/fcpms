<template>
  <SpatialDrawer :show="show" title="تعديل بيانات الموقع الميداني (M2-R02)" @close="$emit('close')">
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Site Name -->
      <SpatialInput
        v-model="form.name"
        label="اسم الموقع الميداني"
        :required="true"
        :error="errors.name"
      />

      <!-- Site Code (Disabled / Read-only in Edit mode) -->
      <SpatialInput
        v-model="form.code"
        label="رمز الموقع (Site Code)"
        :readonly="true"
        :disabled="true"
        hint="رمز الموقع غير قابل للتعديل"
      />

      <!-- Location / City -->
      <SpatialInput
        v-model="form.location"
        label="المدينة / الموقع الجغرافي"
        :error="errors.location"
      />

      <!-- Status Option -->
      <SpatialRadioGroup
        v-model="form.status"
        label="حالة الموقع"
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
 * Edit.vue - نموذج تعديل موقع ميداني قائم (M2-R02)
 * FM-002: SpatialDrawer for form inputs
 * FM-007: Inline validation errors
 */
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  site: { type: Object, default: null }
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

watch(() => props.site, (newSite) => {
  if (newSite) {
    form.name = newSite.name || ''
    form.code = newSite.code || ''
    form.location = newSite.location || ''
    form.status = newSite.status || 'active'
  }
}, { immediate: true })

const submit = () => {
  if (!props.site) return
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  router.put(route('hr.sites.update', { id: props.site.id }), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('success', 'تم تعديل بيانات الموقع الميداني بنجاح.')
      emit('close')
    },
    onError: (errs) => {
      isSubmitting.value = false
      Object.assign(errors, errs)
    }
  })
}
</script>
