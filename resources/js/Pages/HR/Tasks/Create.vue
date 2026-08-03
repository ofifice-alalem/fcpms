<template>
  <HRLayout>
    <template #title>بناء نموذج مهمة جديد (Task Builder - M3-P02)</template>

    <div class="space-y-6 max-w-5xl mx-auto">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">مُنشئ النماذج الديناميكية (Task Builder)</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تحديد خصائص المهمة، المواقع والاستشاريين المستهدفين، وبناء العناصر التفاعلية</p>
        </div>

        <SpatialButton variant="ghost" icon="⬅️" @click="cancel">
          إلغاء والعودة
        </SpatialButton>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: Basic Task Info (FM-005: Grouped Sections) -->
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

            <SpatialInput
              v-model="form.performance_weight"
              type="number"
              label="وزن المهمة في الأداء % (0 - 100)"
              placeholder="10"
              :required="true"
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

        <!-- Step 2: Target Assignment (Revision 1.2: Sites & Consultants Intersection) -->
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
          <div class="space-y-4">
            <div v-for="(comp, idx) in form.components" :key="idx" class="p-4 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/20 dark:border-white/10 space-y-3 relative">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-500">المكون رقم {{ idx + 1 }}</span>
                <button type="button" @click="removeComponent(idx)" class="text-xs text-rose-500 hover:font-bold">إزالة ✕</button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <SpatialInput v-model="comp.label" label="عنوان المكون" placeholder="مثال: حالة أجهزة التكييف" :required="true" />
                <SpatialDropdown
                  v-model="comp.component_type"
                  label="نوع المكون"
                  :options="[
                    { label: 'حقل نصي مفرد (Text)', value: 'text' },
                    { label: 'مربع نص متعدد (Textarea)', value: 'textarea' },
                    { label: 'اختيار من قائمة (Select)', value: 'select' },
                    { label: 'أزرار اختيار (Radio)', value: 'radio' },
                    { label: 'عداد أرقام (Stepper)', value: 'stepper' },
                    { label: 'منتقي تواريخ (Date)', value: 'date' },
                    { label: 'رفع صورة ميدانية (Image)', value: 'image' }
                  ]"
                />
                <SpatialCheckbox v-model="comp.is_required" label="حقل إجباري" class="mt-6" />
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
 * Create.vue - Task Builder الكامل لبناء النماذج الديناميكية (M3-P02)
 * FM-003: Full Page for Task Builder
 * Revision 1.2: Sites and Consultants Visibility Logic
 * BR-035: Conditional Components Logic
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
  performance_weight: 10,
  site_ids: [],
  consultant_ids: [],
  components: [
    { label: 'نتيجة الفحص الإشرافي', component_type: 'text', is_required: true, options: [] }
  ]
})

const formattedSites = computed(() => props.sites.map(s => ({ label: s.name, value: s.id })))
const formattedConsultants = computed(() => props.consultants.map(c => ({ label: c.full_name, value: c.id })))

const addComponent = () => {
  form.components.push({ label: '', component_type: 'text', is_required: false, options: [] })
}

const removeComponent = (idx) => {
  if (form.components.length > 1) form.components.splice(idx, 1)
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
