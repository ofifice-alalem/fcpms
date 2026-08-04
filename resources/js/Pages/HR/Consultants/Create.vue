<template>
  <SpatialDrawer
    :show="show"
    title="إضافة استشاري ميداني جديد"
    subtitle="إنشاء ملف واستشاري جديد وإصدار بيانات الاعتماد للولوج للتطبيق"
    size="medium"
    @close="$emit('close')"
  >
    <form @submit.prevent="submit" class="space-y-4">
      <SpatialInput
        v-model="form.employee_number"
        label="الرقم الوظيفي *"
        placeholder="مثال: CNS-109"
        :required="true"
        :error="errors.employee_number"
      />

      <SpatialInput
        v-model="form.full_name"
        label="الاسم الكامل للاستشاري *"
        placeholder="مثال: م. أحمد عبد الله"
        :required="true"
        :error="errors.full_name"
      />

      <SpatialInput
        v-model="form.email"
        type="email"
        label="البريد الإلكتروني الحساب *"
        placeholder="example@fcpms.com"
        :required="true"
        :error="errors.email"
      />

      <SpatialInput
        v-model="form.phone"
        label="رقم الهاتف"
        placeholder="05xxxxxxx"
        :error="errors.phone"
      />

      <SpatialInput
        v-model="form.specialization"
        label="التخصص المهني"
        placeholder="مثال: استشاري سلامة ومدني"
        :error="errors.specialization"
      />

      <SpatialDropdown
        v-model="form.status"
        label="الحالة الوظيفية"
        :options="[
          { label: 'نشط على رأس العمل (Active)', value: 'active' },
          { label: 'غير نشط (Inactive)', value: 'inactive' },
          { label: 'في إجازة رسمية (Vacation)', value: 'vacation' }
        ]"
      />

      <div class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-indigo-500 font-bold">
        ℹ️ سيتم إنشاء حساب مستخدم تلقائياً للاستشاري بكلمة مرور افتراضية (password123).
      </div>

      <!-- Action Footer -->
      <div class="pt-4 flex items-center justify-end space-x-3 space-x-reverse border-t border-black/10 dark:border-white/10">
        <SpatialButton type="button" variant="ghost" @click="$emit('close')">إلغاء</SpatialButton>
        <SpatialButton type="submit" variant="primary" icon="💾" :loading="isSubmitting">
          حفظ وإضافة الاستشاري
        </SpatialButton>
      </div>
    </form>
  </SpatialDrawer>
</template>

<script setup>
/**
 * Create.vue - إضافة استشاري جديد عبر SpatialDrawer (M1-P02)
 */
import { reactive, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'success'])

const isSubmitting = ref(false)
const errors = reactive({})

const form = reactive({
  employee_number: '',
  full_name: '',
  email: '',
  phone: '',
  specialization: '',
  status: 'active'
})

watch(() => props.show, (val) => {
  if (val) {
    form.employee_number = 'CNS-' + Math.floor(100 + Math.random() * 900)
    form.full_name = ''
    form.email = ''
    form.phone = ''
    form.specialization = ''
    form.status = 'active'
    Object.keys(errors).forEach(k => delete errors[k])
  }
})

const submit = () => {
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  router.post(route('hr.consultants.store'), form, {
    onSuccess: () => {
      isSubmitting.value = false
      emit('close')
      emit('success', 'تم إضافة الاستشاري الميداني وحسابه بنجاح.')
    },
    onError: (errs) => {
      isSubmitting.value = false
      Object.assign(errors, errs)
    }
  })
}
</script>
