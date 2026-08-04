<template>
  <HRLayout>
    <template #title>معاينة نموذج المهمة الميدانية (Task Builder Simulator)</template>

    <div class="space-y-6 max-w-5xl mx-auto pb-12">
      <!-- 1. Top Bar Navigation & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-3">
            <h2 class="text-2xl font-black text-slate-800 dark:text-white">{{ task?.name }}</h2>
            <SpatialStatusPill
              :status="task?.type === 'daily' ? 'completed' : 'info'"
              :label="task?.type === 'daily' ? 'يومية إجبارية' : 'عند الطلب'"
            />
            <SpatialStatusPill
              :status="task?.is_required ? 'warning' : 'neutral'"
              :label="task?.is_required ? 'إجبارية الاعتماد' : 'اختيارية'"
            />
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ task?.description || 'لا يوجد وصف تفصيلي مدخل لهذه المهمة.' }}</p>
        </div>

        <div class="flex items-center gap-2">
          <SpatialButton variant="primary" icon="✏️" @click="editTask">
            تعديل النموذج
          </SpatialButton>
          <SpatialButton variant="ghost" icon="⬅️" @click="back">
            العودة للقائمة
          </SpatialButton>
        </div>
      </div>

      <!-- 2. Interactive Simulator Mode Toggle -->
      <div class="flex flex-col sm:flex-row items-center justify-between p-2 rounded-2xl bg-slate-200/50 dark:bg-slate-900/50 border border-slate-300/50 dark:border-white/10 gap-3">
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button
            @click="activeView = 'mobile'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
            :class="activeView === 'mobile' ? 'bg-primary text-white shadow-lg shadow-primary/30 font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
          >
            <span>📱</span>
            <span>عرض تطبيق الاستشاري (Mobile View)</span>
          </button>

          <button
            @click="activeView = 'inspector'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
            :class="activeView === 'inspector' ? 'bg-primary text-white shadow-lg shadow-primary/30 font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
          >
            <span>🔍</span>
            <span>تفاصيل وهيكلية النموذج (Inspector)</span>
          </button>
        </div>

        <!-- Sync Indicator with Active System Theme -->
        <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-primary/10 border border-primary/20 text-xs font-bold text-primary">
          <span>🎨</span>
          <span>متزامن مع الوضع الفعّال: {{ isDark ? '🌙 داكن' : '☀️ فاتح' }}</span>
        </div>
      </div>

      <!-- VIEW 1: Mobile Phone Simulator (Automatically Syncs with Active Theme) -->
      <div v-if="activeView === 'mobile'" class="flex justify-center my-6">
        <!-- Phone Outer Frame -->
        <div
          class="w-full max-w-md rounded-[44px] p-4 shadow-2xl transition-all border-4 relative"
          :class="isDark ? 'bg-slate-950 border-slate-800 ring-1 ring-white/10' : 'bg-slate-200 border-slate-300 ring-1 ring-black/10'"
        >
          <!-- Phone Camera Notch -->
          <div
            class="w-32 h-5 rounded-b-2xl mx-auto mb-4 flex items-center justify-center gap-2"
            :class="isDark ? 'bg-slate-900' : 'bg-slate-300'"
          >
            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-primary/40"></div>
          </div>

          <!-- Phone Screen Container -->
          <div
            class="rounded-[32px] p-5 space-y-5 border min-h-[580px] max-h-[720px] overflow-y-auto custom-scroll transition-colors"
            :class="isDark ? 'bg-slate-900 text-white border-white/10' : 'bg-slate-50 text-slate-900 border-slate-200/80'"
          >
            <!-- Screen Header -->
            <div class="border-b pb-3" :class="isDark ? 'border-white/10' : 'border-slate-200'">
              <span class="text-[10px] uppercase tracking-wider font-bold text-primary block">نموذج المهمة الميدانية</span>
              <h3 class="text-lg font-black mt-0.5" :class="isDark ? 'text-white' : 'text-slate-800'">{{ task?.name }}</h3>
              <p class="text-xs mt-1" :class="isDark ? 'text-slate-400' : 'text-slate-500'">{{ task?.description }}</p>
            </div>

            <!-- Dynamic Components Live Form -->
            <div class="space-y-5">
              <template v-for="(comp, idx) in formattedComponents" :key="idx">
                <transition name="fade">
                  <div
                    v-if="isComponentVisible(comp, idx)"
                    class="p-4 rounded-2xl border space-y-3 relative transition-all"
                    :class="isDark ? 'bg-white/5 border-white/10 hover:border-primary/40' : 'bg-white border-slate-200 shadow-sm hover:border-primary/40'"
                  >
                    <!-- Conditional Logic Badge -->
                    <div v-if="comp.has_condition" class="flex items-center gap-1.5 text-[11px] font-bold text-amber-500 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-xl w-fit">
                      <span>⚡</span>
                      <span>يظهر في حال اختيار "{{ comp.condition_value }}" من (مكون {{ comp.condition_parent_idx + 1 }})</span>
                    </div>

                    <!-- Label -->
                    <label class="block text-xs font-bold" :class="isDark ? 'text-white' : 'text-slate-800'">
                      {{ comp.label }}
                      <span v-if="comp.is_required" class="text-rose-500">*</span>
                    </label>

                    <!-- Render TYPE: Text -->
                    <div v-if="comp.component_type === 'text'">
                      <SpatialInput
                        v-model="simulatedData[idx]"
                        :placeholder="comp.placeholder || 'أدخل النص هنا...'"
                      />
                    </div>

                    <!-- Render TYPE: Textarea -->
                    <div v-else-if="comp.component_type === 'textarea'">
                      <SpatialTextarea
                        v-model="simulatedData[idx]"
                        :placeholder="comp.placeholder || 'أدخل التفاصيل والملاحظات...'"
                      />
                    </div>

                    <!-- Render TYPE: Select Dropdown -->
                    <div v-else-if="comp.component_type === 'select'">
                      <SpatialDropdown
                        v-model="simulatedData[idx]"
                        :options="comp.formattedOptions"
                        placeholder="اختر من القائمة..."
                      />
                    </div>

                    <!-- Render TYPE: Radio Group -->
                    <div v-else-if="comp.component_type === 'radio'">
                      <SpatialRadioGroup
                        v-model="simulatedData[idx]"
                        :options="comp.formattedOptions"
                      />
                    </div>

                    <!-- Render TYPE: Stepper -->
                    <div v-else-if="comp.component_type === 'stepper'" class="flex items-center gap-3">
                      <button
                        type="button"
                        @click="stepperDecrement(idx, comp.settings?.min)"
                        class="w-10 h-10 rounded-xl font-black text-lg flex items-center justify-center border transition-all"
                        :class="isDark ? 'bg-white/10 text-white hover:bg-white/20 border-white/10' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 border-slate-300'"
                      >-</button>
                      <span class="font-mono text-lg font-black w-12 text-center text-primary">
                        {{ simulatedData[idx] ?? (comp.settings?.min || 0) }}
                      </span>
                      <button
                        type="button"
                        @click="stepperIncrement(idx, comp.settings?.max)"
                        class="w-10 h-10 rounded-xl font-black text-lg flex items-center justify-center border transition-all"
                        :class="isDark ? 'bg-white/10 text-white hover:bg-white/20 border-white/10' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 border-slate-300'"
                      >+</button>
                    </div>

                    <!-- Render TYPE: Date -->
                    <div v-else-if="comp.component_type === 'date'">
                      <SpatialInput
                        v-model="simulatedData[idx]"
                        type="date"
                      />
                    </div>

                    <!-- Render TYPE: Image Upload -->
                    <div v-else-if="comp.component_type === 'image'">
                      <div
                        class="border-2 border-dashed rounded-2xl p-4 text-center cursor-pointer transition-all"
                        :class="isDark ? 'border-white/20 hover:border-primary bg-white/5' : 'border-slate-300 hover:border-primary bg-slate-50'"
                      >
                        <div class="text-2xl mb-1">📸</div>
                        <p class="text-xs font-bold" :class="isDark ? 'text-slate-300' : 'text-slate-700'">التقاط صورة ميدانية أو فتح المعرض</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">صيغ الصور المسموحة: JPG, PNG</p>
                      </div>
                    </div>
                  </div>
                </transition>
              </template>

              <!-- Phone Action Button -->
              <SpatialButton variant="primary" block size="lg" icon="✅" class="mt-6">
                اعتماد وإرسال التقرير الميداني
              </SpatialButton>
            </div>
          </div>
        </div>
      </div>

      <!-- VIEW 2: Structure Inspector (HR Admin Detail View) -->
      <div v-else class="space-y-6">
        <!-- Target Sites and Consultants Target Scope -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <SpatialCard title="المواقع الميدانية المخصصة لها">
            <div class="flex flex-wrap gap-2">
              <span
                v-for="site in task?.sites"
                :key="site.id"
                class="px-3 py-1.5 rounded-xl bg-primary/10 text-primary border border-primary/20 text-xs font-bold"
              >
                📍 {{ site.name }}
              </span>
              <span v-if="!task?.sites?.length" class="text-xs text-slate-400 italic">
                جميع المواقع الميدانية (عام)
              </span>
            </div>
          </SpatialCard>

          <SpatialCard title="الاستشاريون المكلفون بها">
            <div class="flex flex-wrap gap-2">
              <span
                v-for="c in task?.consultants"
                :key="c.id"
                class="px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-xs font-bold"
              >
                👤 {{ c.full_name }}
              </span>
              <span v-if="!task?.consultants?.length" class="text-xs text-slate-400 italic">
                جميع الاستشاريين الميدانيين
              </span>
            </div>
          </SpatialCard>
        </div>

        <!-- Components Table Breakdown -->
        <SpatialCard title="هيكلية مكونات نموذج المهمة والاشتراطات">
          <div class="divide-y divide-black/10 dark:divide-white/10">
            <div
              v-for="(comp, idx) in formattedComponents"
              :key="idx"
              class="py-4 space-y-3"
            >
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-primary/20 text-primary text-xs font-bold flex items-center justify-center">
                      {{ idx + 1 }}
                    </span>
                    <h4 class="text-sm font-black text-slate-800 dark:text-white">{{ comp.label }}</h4>
                    <SpatialStatusPill
                      :status="comp.is_required ? 'completed' : 'neutral'"
                      :label="comp.is_required ? 'إجباري' : 'اختياري'"
                    />
                  </div>

                  <!-- Display Options if any -->
                  <div v-if="comp.formattedOptions.length > 0" class="flex flex-wrap gap-1.5 mt-2 mr-8">
                    <span class="text-xs text-slate-400 font-bold ml-1">الخيارات:</span>
                    <span
                      v-for="(opt, optIdx) in comp.formattedOptions"
                      :key="optIdx"
                      class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-700"
                    >
                      {{ typeof opt === 'object' ? opt.label : opt }}
                    </span>
                  </div>
                </div>

                <div class="flex items-center gap-3 text-xs font-mono">
                  <span class="px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-500 font-bold">
                    {{ comp.component_type }}
                  </span>
                </div>
              </div>

              <!-- Explicit Detailed Condition Statement -->
              <div
                v-if="comp.has_condition"
                class="mr-8 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2"
              >
                <span>⚡</span>
                <span>
                  شرط الظهور: يظهر هذا المكون فقط في حال تم اختيار الخيار
                  <strong class="text-slate-900 dark:text-white underline mx-1">"{{ comp.condition_value }}"</strong>
                  من المكون السابق
                  <strong class="text-primary mx-1">({{ getParentLabel(comp.condition_parent_idx) }})</strong>
                </span>
              </div>
            </div>
          </div>
        </SpatialCard>
      </div>
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Preview.vue - ربط مظهر محاكي الجوال تلقائياً بالثيم الفعّال للمستخدم
 */
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useTheme } from '@/Composables/useTheme'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialTextarea from '@/Components/Spatial/SpatialTextarea.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'

const props = defineProps({
  task: { type: Object, default: null }
})

const { isDark } = useTheme()

const activeView = ref('mobile')
const simulatedData = reactive({})

const formattedComponents = computed(() => {
  return (props.task?.components || []).map((c, i) => {
    const vis = c.visibility_rules || {}
    const valRules = c.validation_rules || {}
    
    // Normalize options
    const rawOptions = c.options || []
    const formattedOpts = rawOptions.map(o => {
      if (typeof o === 'object' && o !== null) {
        const txt = o.option_label || o.label || o.option_value || o.value || ''
        return { label: txt, value: txt }
      }
      return { label: String(o), value: String(o) }
    })

    return {
      label: c.label || '',
      component_type: c.type || c.component_type || 'text',
      is_required: Boolean(c.is_required),
      formattedOptions: formattedOpts,
      placeholder: valRules.placeholder || c.placeholder || '',
      settings: valRules.settings || c.settings || { min: 0, max: 100, step: 1 },
      has_condition: Boolean(vis.has_condition || c.has_condition),
      condition_parent_idx: vis.condition_parent_idx !== undefined ? vis.condition_parent_idx : (c.condition_parent_idx ?? null),
      condition_value: vis.condition_value || c.condition_value || ''
    }
  })
})

const getParentLabel = (parentIdx) => {
  if (parentIdx === null || parentIdx === undefined || !formattedComponents.value[parentIdx]) {
    return 'المكون السابق'
  }
  return `مكون ${parentIdx + 1}: ${formattedComponents.value[parentIdx].label}`
}

const isComponentVisible = (comp, idx) => {
  if (!comp.has_condition) return true
  if (comp.condition_parent_idx === null || comp.condition_parent_idx === undefined) return true

  const parentVal = simulatedData[comp.condition_parent_idx]
  if (!parentVal) return false
  return String(parentVal).trim() === String(comp.condition_value).trim()
}

const stepperIncrement = (idx, max = 100) => {
  const current = Number(simulatedData[idx] || 0)
  if (current < max) simulatedData[idx] = current + 1
}

const stepperDecrement = (idx, min = 0) => {
  const current = Number(simulatedData[idx] || 0)
  if (current > min) simulatedData[idx] = current - 1
}

const back = () => router.get(route('hr.tasks.index'))
const editTask = () => router.get(route('hr.tasks.edit', props.task.id))
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
