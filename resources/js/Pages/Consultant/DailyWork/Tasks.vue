<template>
  <ConsultantLayout>
    <template #title>تعبئة مهام الزيارة الميدانية</template>

    <div class="space-y-6 max-w-4xl mx-auto pb-12">
      <!-- 1. Header Card with Live Progress Bar -->
      <SpatialCard padding="large" variant="gradient">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="space-y-1 text-center md:text-right">
            <div class="flex items-center justify-center md:justify-start space-x-3 space-x-reverse">
              <h2 class="text-2xl font-black text-slate-800 dark:text-white">{{ site?.name || 'موقع ميداني' }}</h2>
              <SpatialStatusPill status="active" label="زيارة قيد التنفيذ" />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              رمز الموقع: <span class="font-mono font-bold text-indigo-500">{{ site?.code || '---' }}</span> | التاريخ: {{ currentDate }}
            </p>
          </div>

          <!-- Progress Bar Tracker -->
          <div class="w-full md:w-64 space-y-1">
            <SpatialProgressBar
              :value="completionProgress"
              :max="100"
              label="نسبة إنجاز مهام الزيارة"
            />
          </div>
        </div>
      </SpatialCard>

      <!-- FB-003: Empty State when no tasks defined -->
      <SpatialEmptyState
        v-if="tasks.length === 0"
        icon="📋"
        title="لا توجد مهام إجبارية محددة لهذا الموقع"
        message="لا توجد مهام يومية إجبارية بهذا الموقع الميداني. يمكنك اختيار إضافة مهمة عند الطلب."
      />

      <!-- 2. Task Cards List -->
      <form v-else @submit.prevent="submitVisit" class="space-y-6">
        <SpatialCard
          v-for="task in tasks"
          :key="task.id"
          padding="large"
          :glow="false"
        >
          <!-- Task Header -->
          <template #header>
            <div class="flex items-center justify-between w-full border-b pb-4 border-slate-200/60 dark:border-white/10">
              <div>
                <div class="flex items-center space-x-2 space-x-reverse">
                  <h3 class="text-lg font-black text-slate-800 dark:text-white">{{ task.name }}</h3>
                  <SpatialStatusPill
                    :status="task.type === 'on_demand' ? 'info' : (task.is_required ? 'completed' : 'info')"
                    :label="task.type === 'on_demand' ? 'مهمة عند الطلب' : (task.is_required ? 'إجباري' : 'اختياري')"
                  />
                </div>
                <p v-if="task.description" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  {{ task.description }}
                </p>
              </div>
            </div>
          </template>

          <!-- Task Components List -->
          <div class="space-y-6 pt-2">
            <template v-for="(comp, compIdx) in task.components" :key="comp.id">
              <transition name="fade">
                <div
                  v-if="isComponentVisible(comp, compIdx, task.components)"
                  class="p-5 rounded-2xl border space-y-3 transition-all bg-white/50 dark:bg-slate-900/50 border-slate-200 dark:border-white/10 hover:border-indigo-500/40"
                >
                  <!-- Conditional Logic Indicator Badge -->
                  <div
                    v-if="hasConditionRule(comp)"
                    class="flex items-center space-x-1.5 space-x-reverse text-[11px] font-bold text-amber-500 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-xl w-fit"
                  >
                    <span>⚡</span>
                    <span>شرطي: يظهر عند اختيار "{{ getConditionValue(comp) }}"</span>
                  </div>

                  <!-- Component Question Label -->
                  <label class="block text-sm font-bold text-slate-800 dark:text-slate-100">
                    {{ comp.label }}
                    <span v-if="comp.is_required" class="text-rose-500">*</span>
                  </label>

                  <!-- 1. Text Input -->
                  <SpatialInput
                    v-if="(comp.type || comp.component_type) === 'text' || (comp.type || comp.component_type) === 'number'"
                    v-model="formResponses[comp.id]"
                    :type="(comp.type || comp.component_type) === 'number' ? 'number' : 'text'"
                    :placeholder="getPlaceholder(comp) || 'أدخل الإجابة...'"
                    :error="errors[comp.id]"
                  />

                  <!-- 2. Textarea -->
                  <SpatialTextarea
                    v-else-if="(comp.type || comp.component_type) === 'textarea'"
                    v-model="formResponses[comp.id]"
                    :placeholder="getPlaceholder(comp) || 'أدخل التفاصيل والملاحظات...'"
                    :maxLength="500"
                    :error="errors[comp.id]"
                  />

                  <!-- 3. Dropdown / Select -->
                  <SpatialDropdown
                    v-else-if="(comp.type || comp.component_type) === 'select'"
                    v-model="formResponses[comp.id]"
                    :options="formatOptions(comp.options)"
                    placeholder="اختر الإجابة من القائمة..."
                    :error="errors[comp.id]"
                  />

                  <!-- 4. Radio Group -->
                  <SpatialRadioGroup
                    v-else-if="(comp.type || comp.component_type) === 'radio'"
                    v-model="formResponses[comp.id]"
                    :options="formatOptions(comp.options)"
                  />

                  <!-- 5. Stepper -->
                  <div
                    v-else-if="(comp.type || comp.component_type) === 'stepper'"
                    class="flex items-center space-x-4 space-x-reverse"
                  >
                    <button
                      type="button"
                      @click="stepperDecrement(comp.id)"
                      class="w-11 h-11 rounded-xl font-black text-xl flex items-center justify-center border transition-all bg-slate-100 dark:bg-white/10 text-slate-800 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border-slate-300 dark:border-white/10"
                    >-</button>
                    <span class="font-mono text-xl font-black w-14 text-center text-indigo-500">
                      {{ formResponses[comp.id] || 0 }}
                    </span>
                    <button
                      type="button"
                      @click="stepperIncrement(comp.id)"
                      class="w-11 h-11 rounded-xl font-black text-xl flex items-center justify-center border transition-all bg-slate-100 dark:bg-white/10 text-slate-800 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 border-slate-300 dark:border-white/10"
                    >+</button>
                  </div>

                  <!-- 6. Date Picker -->
                  <SpatialDatePicker
                    v-else-if="(comp.type || comp.component_type) === 'date'"
                    v-model="formResponses[comp.id]"
                  />

                  <!-- 7. Image Upload -->
                  <TaskImageUpload
                    v-else-if="(comp.type || comp.component_type) === 'image'"
                    v-model="formResponses[comp.id]"
                    label="التقاط أو إرفاق صورة ميدانية"
                  />
                </div>
              </transition>
            </template>
          </div>
        </SpatialCard>

        <!-- 3. Actions Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-xl gap-4">
          <SpatialButton
            v-if="onDemandTasks.length > 0"
            type="button"
            variant="secondary"
            icon="➕"
            @click="showDrawer = true"
          >
            إضافة مهمة عند الطلب (On-Demand)
          </SpatialButton>
          <div v-else class="text-xs text-slate-400">جميع المهام المطلوبة معروضة بالسجل</div>

          <SpatialButton
            type="submit"
            variant="primary"
            size="lg"
            icon="💾"
            :loading="isSubmitting"
          >
            حفظ وتأكيد زيارة الموقع
          </SpatialButton>
        </div>
      </form>

      <!-- On-Demand Tasks Drawer -->
      <SpatialDrawer :show="showDrawer" title="قائمة المهام عند الطلب (On-Demand Tasks)" @close="showDrawer = false">
        <div class="space-y-3">
          <p class="text-xs text-slate-500">اختر المهام الإضافية غير المبرمجة لإضافتها لنموذج هذه الزيارة:</p>
          <div
            v-for="onDemand in onDemandTasks"
            :key="onDemand.id"
            @click="addOnDemandTask(onDemand)"
            class="p-4 rounded-xl border border-white/20 dark:border-white/10 bg-white/50 dark:bg-slate-800/50 hover:border-indigo-500 transition-all cursor-pointer flex items-center justify-between"
          >
            <div>
              <h5 class="text-xs font-bold">{{ onDemand.name }}</h5>
              <p class="text-[11px] text-slate-400 mt-0.5">{{ onDemand.description }}</p>
            </div>
            <span class="text-xs text-indigo-500 font-bold">إضافة ➕</span>
          </div>
        </div>
      </SpatialDrawer>

      <!-- Feedback Toast -->
      <SpatialToast
        v-if="toast.show"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        @dismiss="toast.show = false"
      />
    </div>
  </ConsultantLayout>
</template>

<script setup>
/**
 * Tasks.vue - صفحة تعبئة المهام الديناميكية للزيارة الميدانية للاستشاري
 */
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialTextarea from '@/Components/Spatial/SpatialTextarea.vue'
import SpatialDatePicker from '@/Components/Spatial/SpatialDatePicker.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialRadioGroup from '@/Components/Spatial/SpatialRadioGroup.vue'
import TaskImageUpload from '@/Components/Spatial/TaskImageUpload.vue'
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue'
import SpatialToast from '@/Components/Spatial/SpatialToast.vue'

const props = defineProps({
  visitId: { type: [Number, String], required: true },
  site: { type: Object, default: null },
  tasks: { type: Array, default: () => [] },
  onDemandTasks: { type: Array, default: () => [] },
  savedResponses: { type: Array, default: () => [] }
})

const isSubmitting = ref(false)
const showDrawer = ref(false)
const errors = reactive({})
const toast = ref({ show: false, type: 'success', title: '', message: '' })
const currentDate = computed(() => new Date().toLocaleDateString('ar-LY'))

// Reactive form responses map: formResponses[componentId] = value
const formResponses = reactive({})

// Initialize form values for all components across all tasks
props.tasks.forEach(t => {
  const components = t.components || []
  components.forEach(c => {
    const cType = c.type || c.component_type
    formResponses[c.id] = cType === 'stepper' ? 0 : ''
  })
})

// Pre-fill form values from saved responses if resuming visit
props.savedResponses.forEach(res => {
  const answers = res.response_data || {}
  Object.keys(answers).forEach(compId => {
    formResponses[compId] = answers[compId]
  })
})

// Normalize options format matching Preview.vue
const formatOptions = (opts) => {
  if (!opts) return []
  return opts.map(o => {
    if (typeof o === 'object' && o !== null) {
      const txt = o.option_label || o.label || o.option_value || o.value || o.option_text || ''
      return { label: String(txt), value: String(txt) }
    }
    return { label: String(o), value: String(o) }
  })
}

const getPlaceholder = (comp) => {
  const rules = comp.validation_rules || {}
  return rules.placeholder || comp.placeholder || ''
}

const hasConditionRule = (comp) => {
  const vis = comp.visibility_rules || {}
  return Boolean(vis.has_condition || comp.has_condition)
}

const getConditionValue = (comp) => {
  const vis = comp.visibility_rules || {}
  return vis.condition_value || comp.condition_value || ''
}

// Conditional Visibility check logic matching Preview.vue
const isComponentVisible = (comp, compIdx, componentsList) => {
  const vis = comp.visibility_rules || {}
  const hasCond = Boolean(vis.has_condition || comp.has_condition)
  if (!hasCond) return true

  const parentIdx = vis.condition_parent_idx !== undefined ? vis.condition_parent_idx : comp.condition_parent_idx
  if (parentIdx === null || parentIdx === undefined) return true

  const parentComp = componentsList[parentIdx]
  if (!parentComp) return true

  const parentVal = formResponses[parentComp.id]
  if (!parentVal) return false

  const condVal = vis.condition_value || comp.condition_value || ''
  return String(parentVal).trim() === String(condVal).trim()
}

const stepperIncrement = (compId) => {
  const current = Number(formResponses[compId] || 0)
  formResponses[compId] = current + 1
}

const stepperDecrement = (compId) => {
  const current = Number(formResponses[compId] || 0)
  if (current > 0) formResponses[compId] = current - 1
}

// Progress calculation: ONLY daily/required tasks count towards mandatory progress
const completionProgress = computed(() => {
  let totalRequired = 0
  let completedRequired = 0

  props.tasks.forEach(t => {
    if (t.type === 'daily' || (t.is_required && t.type !== 'on_demand')) {
      const components = t.components || []
      components.forEach((c, idx) => {
        if (c.is_required && isComponentVisible(c, idx, components)) {
          totalRequired++
          const val = formResponses[c.id]
          if (val !== '' && val !== null && val !== undefined) {
            completedRequired++
          }
        }
      })
    }
  })

  if (totalRequired === 0) return 100
  return Math.round((completedRequired / totalRequired) * 100)
})

const addOnDemandTask = (task) => {
  const components = task.components || []
  components.forEach(c => {
    const cType = c.type || c.component_type
    formResponses[c.id] = cType === 'stepper' ? 0 : ''
  })
  props.tasks.push(task)
  showDrawer.value = false
}

const validateForm = () => {
  Object.keys(errors).forEach(k => delete errors[k])
  let isValid = true

  props.tasks.forEach(t => {
    const components = t.components || []
    components.forEach((c, idx) => {
      if (c.is_required && isComponentVisible(c, idx, components)) {
        const val = formResponses[c.id]
        if (val === '' || val === null || val === undefined) {
          errors[c.id] = 'هذا الحقل إجباري لاستكمال الزيارة'
          isValid = false
        }
      }
    })
  })

  return isValid
}

const submitVisit = () => {
  if (!validateForm()) {
    toast.value = {
      show: true,
      type: 'error',
      title: 'خطأ في التعبئة',
      message: 'يرجى استكمال جميع الحقول الإجبارية المطلوبة قبل الحفظ.'
    }
    return
  }

  isSubmitting.value = true

  const payloadResponses = props.tasks.map(t => {
    const taskAnswers = {}
    const components = t.components || []
    components.forEach(c => {
      taskAnswers[c.id] = formResponses[c.id]
    })
    return {
      task_definition_id: t.id,
      is_completed: true,
      answers: taskAnswers
    }
  })

  router.post(route('consultant.visit.submit'), {
    site_visit_id: props.visitId,
    responses: payloadResponses
  }, {
    onSuccess: () => {
      isSubmitting.value = false
      toast.value = {
        show: true,
        type: 'success',
        title: 'تم حفظ الزيارة بنجاح',
        message: 'تم حفظ إجابات المهام وتحديث مؤشرات الأداء فورياً.'
      }
    },
    onError: () => {
      isSubmitting.value = false
      toast.value = {
        show: true,
        type: 'error',
        title: 'خطأ في عملية الحفظ',
        message: 'حدث خطأ أثناء حفظ الزيارة، يرجى إعادة المحاولة.'
      }
    }
  })
}
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
