<template>
  <HRLayout>
    <template #title>بناء نموذج مهمة جديد (Task Builder - M3-P02)</template>

    <div class="space-y-6 max-w-5xl mx-auto">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">مُنشئ النماذج الديناميكية (Task Builder)</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تحديد خصائص المهمة، المواقع والاستشاريين المستهدفين، وبناء العناصر التفاعلية والخيارات</p>
        </div>

        <SpatialButton variant="ghost" icon="⬅️" @click="cancel">
          إلغاء والعودة
        </SpatialButton>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: Basic Task Info -->
        <SpatialCard title="1. البيانات الأساسية للمهمة">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <SpatialInput
              v-model="form.name"
              label="عنوان المهمة"
              placeholder="مثال: الفحص الفني لأنظمة السلامة"
              :required="true"
              :error="errors.name"
            />

            <SpatialDropdown
              v-model="form.type"
              label="نوع المهمة (BR-030)"
              :options="[
                { label: 'يومية إجبارية (Daily Task)', value: 'daily' },
                { label: 'عند الطلب / إضافية (On-Demand Task)', value: 'on_demand' }
              ]"
            />

            <SpatialRadioGroup
              v-model="form.is_required"
              label="هل المهمة إجبارية الاعتماد؟"
              :options="[
                { label: 'إجبارية (Required)', value: true },
                { label: 'اختيارية (Optional)', value: false }
              ]"
              :cols="2"
            />

            <div class="md:col-span-2">
              <SpatialTextarea
                v-model="form.description"
                label="وصف وشروط المهمة الميدانية"
                placeholder="توضيح خطوات تنفيذ المهمة للاستشاري الميداني..."
              />
            </div>
          </div>
        </SpatialCard>

        <!-- Step 2: Target Assignment -->
        <SpatialCard title="2. نطاق التخصيص والمستهدفين (Revision 1.2)">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <SpatialDropdown
              v-model="form.site_ids"
              label="المواقع الميدانية المستهدفة"
              :options="formattedSites"
              :multiple="true"
              placeholder="جميع المواقع (أو اختر مواقع محددة)..."
            />

            <SpatialDropdown
              v-model="form.consultant_ids"
              label="الاستشاريون المستهدفون"
              :options="formattedConsultants"
              :multiple="true"
              placeholder="جميع الاستشاريين (أو اختر استشاريين محددين)..."
            />
          </div>
        </SpatialCard>

        <!-- Step 3: Dynamic Form Component Builder -->
        <SpatialCard title="3. بناء المكونات التفاعلية والمنطق الشرطي (BR-035)">
          <div class="space-y-6">
            <div
              v-for="(comp, idx) in form.components"
              :key="idx"
              class="p-5 rounded-[22px] bg-white/60 dark:bg-slate-900/60 border border-black/10 dark:border-white/15 space-y-4 relative shadow-sm"
            >
              <!-- Component Header -->
              <div class="flex items-center justify-between border-b border-black/5 dark:border-white/10 pb-3">
                <span class="text-sm font-black text-primary flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs">
                    {{ idx + 1 }}
                  </span>
                  المكون التفاعلي رقم {{ idx + 1 }}
                </span>
                <button
                  type="button"
                  @click="removeComponent(idx)"
                  class="text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors"
                >
                  إزالة المكون ✕
                </button>
              </div>

              <!-- Main Fields: Label, Type, Required -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <SpatialInput
                  v-model="comp.label"
                  label="عنوان المكون / السؤال"
                  placeholder="مثال: حالة أنظمة الإنذار المبكر"
                  :required="true"
                />

                <SpatialDropdown
                  v-model="comp.component_type"
                  label="نوع المكون التفاعلي"
                  :options="[
                    { label: 'حقل نصي مفرد (Text)', value: 'text' },
                    { label: 'مربع نص متعدد (Textarea)', value: 'textarea' },
                    { label: 'اختيار من قائمة (Select Dropdown)', value: 'select' },
                    { label: 'أزرار اختيار خيار مفرد (Radio Group)', value: 'radio' },
                    { label: 'عداد أرقام (Stepper)', value: 'stepper' },
                    { label: 'منتقي تواريخ (Date Picker)', value: 'date' },
                    { label: 'رفع صورة ميدانية (Image Upload)', value: 'image' }
                  ]"
                />

                <div class="flex items-center mt-6">
                  <SpatialCheckbox v-model="comp.is_required" label="مكون إجباري الإدخال" />
                </div>
              </div>

              <!-- Dynamic Options Builder for Select & Radio Types -->
              <div
                v-if="comp.component_type === 'select' || comp.component_type === 'radio'"
                class="p-4 rounded-xl bg-primary/5 border border-primary/20 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <label class="text-xs font-bold text-slate-800 dark:text-white">
                    خيارات الاختيار (Options Builder):
                  </label>
                  <SpatialButton
                    type="button"
                    variant="outline"
                    size="sm"
                    icon="➕"
                    @click="addOption(comp)"
                  >
                    إضافة خيار جديد
                  </SpatialButton>
                </div>

                <div class="space-y-2">
                  <div
                    v-for="(opt, optIdx) in comp.options"
                    :key="optIdx"
                    class="flex items-center gap-2"
                  >
                    <SpatialInput
                      v-model="comp.options[optIdx]"
                      :placeholder="`الخيار رقم ${optIdx + 1} (مثال: مطابق / غير مطابق)`"
                      class="flex-1"
                    />
                    <button
                      type="button"
                      @click="removeOption(comp, optIdx)"
                      class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-500 hover:bg-rose-500/20 text-xs font-bold flex items-center justify-center"
                    >
                      ✕
                    </button>
                  </div>

                  <p v-if="comp.options.length === 0" class="text-xs text-amber-500 font-bold">
                    ⚠️ يرجى إضافة خيار واحد على الأقل للقائمة أو الأزرار.
                  </p>
                </div>
              </div>

              <!-- Settings for Text/Textarea Placeholder -->
              <div v-else-if="comp.component_type === 'text' || comp.component_type === 'textarea'" class="p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
                <SpatialInput
                  v-model="comp.placeholder"
                  label="نص التوضيح الإرشادي (Placeholder)"
                  placeholder="مثال: يرجى كتابة أي ملاحظات إضافية هنا..."
                />
              </div>

              <!-- Settings for Stepper (Numbers) -->
              <div v-else-if="comp.component_type === 'stepper'" class="p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 grid grid-cols-3 gap-3">
                <SpatialInput v-model="comp.settings.min" type="number" label="أدنى قيمة (Min)" placeholder="0" />
                <SpatialInput v-model="comp.settings.max" type="number" label="أعلى قيمة (Max)" placeholder="100" />
                <SpatialInput v-model="comp.settings.step" type="number" label="الخطوة (Step)" placeholder="1" />
              </div>

              <!-- Conditional Logic Toggle (BR-035) -->
              <div class="pt-2 border-t border-black/5 dark:border-white/10">
                <SpatialCheckbox
                  v-model="comp.has_condition"
                  label="تفعيل المنطق الشرطي (إظهار المكون بناءً على شرط سابق)"
                />

                <div v-if="comp.has_condition" class="mt-3 p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 space-y-3">
                  <div v-if="idx === 0" class="text-xs text-amber-500 font-bold flex items-center gap-1.5">
                    <span>ℹ️</span>
                    <span>المكون الأول لا يحتوي على مكونات سابقة لربطه بها. يمكنك إضافة شرط للمكونات التالية.</span>
                  </div>

                  <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <SpatialDropdown
                      v-model="comp.condition_parent_idx"
                      label="يعتمد على إجابة المكون السابق"
                      :options="getPreviousComponentsOptions(idx)"
                      placeholder="اختر المكون السابق..."
                    />

                    <template v-if="comp.condition_parent_idx !== null && form.components[comp.condition_parent_idx]">
                      <SpatialDropdown
                        v-if="getParentComponentOptions(comp.condition_parent_idx).length > 0"
                        v-model="comp.condition_value"
                        label="يظهر إذا كانت الإجابة تساوي"
                        :options="getParentComponentOptions(comp.condition_parent_idx)"
                        placeholder="اختر الخيار الشرطي..."
                      />
                      <SpatialInput
                        v-else
                        v-model="comp.condition_value"
                        label="يظهر إذا كانت الإجابة تساوي"
                        placeholder="أدخل قيمة الشرط..."
                      />
                    </template>
                    <SpatialInput
                      v-else
                      v-model="comp.condition_value"
                      label="يظهر إذا كانت الإجابة تساوي"
                      placeholder="أدخل قيمة الشرط..."
                    />
                  </div>
                </div>
              </div>
            </div>

            <SpatialButton type="button" variant="secondary" icon="➕" block @click="addComponent">
              إضافة مكون تفاعلي جديد للنموذج
            </SpatialButton>
          </div>
        </SpatialCard>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3 space-x-reverse">
          <SpatialButton type="button" variant="ghost" @click="cancel">إلغاء</SpatialButton>
          <SpatialButton type="submit" variant="primary" size="lg" icon="💾" :loading="isSubmitting">
            حفظ وإنشاء نموذج المهمة
          </SpatialButton>
        </div>
      </form>
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Create.vue - Task Builder الكامل مع المعالجة الدقيقة
 */
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialTextarea from '@/Components/Spatial/SpatialTextarea.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  sites: { type: Array, default: () => [] },
  consultants: { type: Array, default: () => [] }
})

const isSubmitting = ref(false)
const errors = reactive({})

const form = reactive({
  name: '',
  description: '',
  type: 'daily',
  is_required: true,
  site_ids: [],
  consultant_ids: [],
  components: [
    {
      label: 'نتيجة الفحص الإشرافي',
      component_type: 'select',
      is_required: true,
      options: ['مطابق للمواصفات', 'غير مطابق - يحتاج معالجة'],
      placeholder: '',
      settings: { min: 0, max: 100, step: 1 },
      has_condition: false,
      condition_parent_idx: null,
      condition_value: ''
    }
  ]
})

const normalizeOptionText = (opt) => {
  if (typeof opt === 'object' && opt !== null) {
    return opt.option_label || opt.label || opt.option_value || opt.value || ''
  }
  return String(opt || '')
}

const formattedSites = computed(() => props.sites.map(s => ({ label: s.name, value: s.id })))
const formattedConsultants = computed(() => props.consultants.map(c => ({ label: c.full_name, value: c.id })))

const getPreviousComponentsOptions = (currentIdx) => {
  return form.components
    .slice(0, currentIdx)
    .map((c, i) => ({
      label: `مكون ${i + 1}: ${c.label || 'بدون عنوان'}`,
      value: i
    }))
}

const getParentComponentOptions = (parentIdx) => {
  if (parentIdx === null || !form.components[parentIdx]) return []
  const parent = form.components[parentIdx]
  if (Array.isArray(parent.options)) {
    return parent.options
      .map(normalizeOptionText)
      .filter(o => o && o.trim() !== '')
      .map(o => ({ label: o, value: o }))
  }
  return []
}

const addComponent = () => {
  form.components.push({
    label: '',
    component_type: 'text',
    is_required: false,
    options: ['خيار 1', 'خيار 2'],
    placeholder: '',
    settings: { min: 0, max: 100, step: 1 },
    has_condition: false,
    condition_parent_idx: null,
    condition_value: ''
  })
}

const removeComponent = (idx) => {
  if (form.components.length > 1) form.components.splice(idx, 1)
}

const addOption = (comp) => {
  comp.options.push('')
}

const removeOption = (comp, optIdx) => {
  if (comp.options.length > 0) comp.options.splice(optIdx, 1)
}

const cancel = () => {
  router.get(route('hr.tasks.index'))
}

const submit = () => {
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  router.post(route('hr.tasks.store'), form, {
    onSuccess: () => {
      isSubmitting.value = false
    },
    onError: (errs) => {
      isSubmitting.value = false
      Object.assign(errors, errs)
    }
  })
}
</script>
