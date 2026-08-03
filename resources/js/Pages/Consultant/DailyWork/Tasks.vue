<template>
  <ConsultantLayout>
    <template #title>تعبئة مهام الزيارة الميدانية</template>

    <div class="space-y-6 max-w-4xl mx-auto">
      <!-- 1. Site Header & Live Progress Tracker (Revision 1.1) -->
      <SpatialCard padding="normal" variant="gradient">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div>
            <div class="flex items-center space-x-2 space-x-reverse">
              <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ site?.name || 'موقع ميداني' }}</h2>
              <SpatialStatusPill status="active" label="زيارة قيد التنفيذ" />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              رمز الموقع: <span class="font-mono font-bold text-indigo-500">{{ site?.code }}</span> | التاريخ: {{ currentDate }}
            </p>
          </div>

          <!-- Progress Bar (Layered Aggregation Progress) -->
          <div class="w-full md:w-64 space-y-1">
            <SpatialProgressBar
              :value="completionProgress"
              :max="100"
              label="نسبة إنجاز مهام الموقع"
            />
          </div>
        </div>
      </SpatialCard>

      <!-- FB-003: Empty State when no tasks defined -->
      <SpatialEmptyState
        v-if="tasks.length === 0"
        icon="📋"
        title="لا توجد مهام محددة لهذا الموقع"
        message="لم يتم ربط أي نموذج مهام يومية أو اختيارية بهذا الموقع الميداني حالياً."
      />

      <!-- 2. Dynamic Task Builder Cards List (M1-P03) -->
      <form v-else @submit.prevent="submitVisit" class="space-y-6">
        <SpatialCard
          v-for="task in tasks"
          :key="task.id"
          padding="large"
          :glow="false"
        >
          <!-- Task Header -->
          <template #header>
            <div class="flex items-center justify-between w-full">
              <div>
                <div class="flex items-center space-x-2 space-x-reverse">
                  <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">{{ task.name }}</h3>
                  <SpatialStatusPill
                    :status="task.is_required ? 'completed' : 'info'"
                    :label="task.is_required ? 'إجباري' : 'اختياري'"
                  />
                </div>
                <p v-if="task.description" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                  {{ task.description }}
                </p>
              </div>
              <span class="text-xs font-mono font-bold text-indigo-500 bg-indigo-500/10 px-2.5 py-1 rounded-lg">
                وزن الأداء: {{ task.performance_weight }}%
              </span>
            </div>
          </template>

          <!-- Task Components Dynamic Rendering (M1-P03 & BR-035) -->
          <div class="space-y-5">
            <template v-for="comp in task.components" :key="comp.id">
              <!-- BR-035: Conditional Field Logic check -->
              <div v-if="isComponentVisible(comp, task.id)" class="space-y-1.5">
                <!-- Text / Number Input -->
                <SpatialInput
                  v-if="comp.component_type === 'text' || comp.component_type === 'number'"
                  v-model="formResponses[task.id][comp.id]"
                  :type="comp.component_type === 'number' ? 'number' : 'text'"
                  :label="comp.label"
                  :required="comp.is_required"
                  :error="errors[`${task.id}.${comp.id}`]"
                  :placeholder="comp.placeholder || 'أدخل الإجابة...'"
                />

                <!-- Textarea -->
                <SpatialTextarea
                  v-else-if="comp.component_type === 'textarea'"
                  v-model="formResponses[task.id][comp.id]"
                  :label="comp.label"
                  :required="comp.is_required"
                  :maxLength="500"
                  :error="errors[`${task.id}.${comp.id}`]"
                  placeholder="أدخل الملاحظات والتفاصيل..."
                />

                <!-- Stepper -->
                <SpatialStepper
                  v-else-if="comp.component_type === 'stepper'"
                  v-model="formResponses[task.id][comp.id]"
                  :label="comp.label"
                />

                <!-- Date Picker -->
                <SpatialDatePicker
                  v-else-if="comp.component_type === 'date'"
                  v-model="formResponses[task.id][comp.id]"
                  :label="comp.label"
                  :required="comp.is_required"
                />

                <!-- Dropdown / Select -->
                <SpatialDropdown
                  v-else-if="comp.component_type === 'select'"
                  v-model="formResponses[task.id][comp.id]"
                  :options="formatOptions(comp.options)"
                  :label="comp.label"
                  :required="comp.is_required"
                  :error="errors[`${task.id}.${comp.id}`]"
                />

                <!-- Radio Group / Single Choice -->
                <SpatialRadioGroup
                  v-else-if="comp.component_type === 'radio'"
                  v-model="formResponses[task.id][comp.id]"
                  :options="formatOptions(comp.options)"
                  :label="comp.label"
                  :cols="2"
                />

                <!-- Task Image Upload -->
                <TaskImageUpload
                  v-else-if="comp.component_type === 'image'"
                  v-model="formResponses[task.id][comp.id]"
                  :label="comp.label"
                />
              </div>
            </template>
          </div>
        </SpatialCard>

        <!-- 3. Form Submit Actions Bar (AC-003 & AC-004) -->
        <div class="flex items-center justify-between p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-xl">
          <SpatialButton
            type="button"
            variant="secondary"
            icon="➕"
            @click="showDrawer = true"
          >
            إضافة مهمة عند الطلب (On-Demand)
          </SpatialButton>

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

      <!-- Feedback Toast (FB-001 & FB-002) -->
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
 * Tasks.vue - صفحة تعبئة المهام الديناميكية للزيارة الميدانية (M1-P03)
 * BR-035: Conditional Logic for Task Components
 * BR-041: Mandatory Task Components Validation
 * AC-003 & AC-004: Double Submission Prevention & Loading State
 * FB-001 & FB-002: Submission Feedback Toast Notifications
 * Revision 1.1: Immediate Performance Percentage Recalculation
 */
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialTextarea from '@/Components/Spatial/SpatialTextarea.vue'
import SpatialStepper from '@/Components/Spatial/SpatialStepper.vue'
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
  onDemandTasks: { type: Array, default: () => [] }
})

const isSubmitting = ref(false)
const showDrawer = ref(false)
const errors = reactive({})

const toast = ref({ show: false, type: 'success', title: '', message: '' })
const currentDate = computed(() => new Date().toLocaleDateString('ar-LY'))

// Reactive form responses tree: formResponses[taskId][componentId]
const formResponses = reactive({})

// Initialize form state
props.tasks.forEach(t => {
  if (!formResponses[t.id]) formResponses[t.id] = {}
  t.components?.forEach(c => {
    formResponses[t.id][c.id] = c.component_type === 'stepper' ? 0 : ''
  })
})

// Format options array for SpatialDropdown / SpatialRadioGroup
const formatOptions = (opts) => {
  if (!opts) return []
  return opts.map(o => (typeof o === 'object' ? { label: o.option_text || o.label, value: o.id || o.value } : { label: o, value: o }))
}

// BR-035: Check component visibility condition
const isComponentVisible = (comp, taskId) => {
  if (!comp.visibility_component_id) return true
  const parentVal = formResponses[taskId]?.[comp.visibility_component_id]
  return parentVal === comp.visibility_option_id
}

// Live Completion Progress (Revision 1.1)
const completionProgress = computed(() => {
  let totalRequired = 0
  let completedRequired = 0

  props.tasks.forEach(t => {
    if (t.is_required) {
      totalRequired++
      const taskDone = t.components?.every(c => {
        if (!c.is_required) return true
        const val = formResponses[t.id]?.[c.id]
        return val !== '' && val !== null && val !== undefined
      })
      if (taskDone) completedRequired++
    }
  })

  if (totalRequired === 0) return 100
  return Math.round((completedRequired / totalRequired) * 100)
})

// Add On-Demand Task to active form
const addOnDemandTask = (task) => {
  if (!formResponses[task.id]) {
    formResponses[task.id] = {}
    task.components?.forEach(c => {
      formResponses[task.id][c.id] = c.component_type === 'stepper' ? 0 : ''
    })
    props.tasks.push(task)
  }
  showDrawer.value = false
}

// BR-041: Form Validation Rules (FM-006 & FM-007)
const validateForm = () => {
  Object.keys(errors).forEach(k => delete errors[k])
  let isValid = true

  props.tasks.forEach(t => {
    t.components?.forEach(c => {
      if (c.is_required && isComponentVisible(c, t.id)) {
        const val = formResponses[t.id]?.[c.id]
        if (val === '' || val === null || val === undefined) {
          errors[`${t.id}.${c.id}`] = 'هذا الحقل إجباري لاستكمال زيارة الموقع'
          isValid = false
        }
      }
    })
  })

  return isValid
}

// Action Submit Handler (AC-003 & Revision 1.1)
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

  const payloadResponses = []
  props.tasks.forEach(t => {
    payloadResponses.push({
      task_definition_id: t.id,
      is_completed: true,
      answers: formResponses[t.id]
    })
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
        message: 'تم حفظ إجابات المهام وإعادة احتساب مؤشرات الأداء اليومية فورياً.'
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
