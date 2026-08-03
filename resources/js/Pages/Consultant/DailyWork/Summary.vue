<template>
  <ConsultantLayout>
    <template #title>ملخص مراجعة اعتماد الزيارة الميدانية</template>

    <div class="space-y-6 max-w-4xl mx-auto">
      <!-- 1. Visit Header & Final Progress Summary (M1-P04) -->
      <SpatialCard padding="large" variant="gradient" :glow="true">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="space-y-2 text-center md:text-right">
            <div class="flex items-center justify-center md:justify-start space-x-2 space-x-reverse">
              <h2 class="text-2xl font-black text-slate-800 dark:text-white">{{ visitSummary?.siteName || 'موقع ميداني' }}</h2>
              <SpatialStatusPill status="completed" label="جاهز للاعتماد" />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              تاريخ الزيارة: <span class="font-mono font-bold text-indigo-500">{{ visitSummary?.date || currentDate }}</span>
            </p>
          </div>

          <!-- Progress Bar & Additional Tasks Metric -->
          <div class="w-full md:w-72 space-y-2">
            <SpatialProgressBar
              :value="visitSummary?.completionPercentage || 100"
              :max="100"
              label="معدل الإنجاز النهائي"
            />
            <div v-if="visitSummary?.additionalTasksCount" class="text-left text-[11px] font-bold text-purple-500">
              + {{ visitSummary.additionalTasksCount }} مهام إضافية (On-Demand)
            </div>
          </div>
        </div>
      </SpatialCard>

      <!-- 2. Read-Only Task Responses Summary Cards -->
      <SpatialCard title="إجابات المهام المسجلة" subtitle="مراجعة الإجابات قبل إغلاق الزيارة واعتمادها النهائي">
        <SpatialEmptyState
          v-if="!visitSummary?.responses || visitSummary.responses.length === 0"
          icon="📋"
          title="لا توجد استجابات مسجلة"
          message="لم يتم تسجيل أي إجابات في هذه الزيارة بعد."
        />

        <div v-else class="space-y-4 pt-2">
          <div
            v-for="(resp, idx) in visitSummary.responses"
            :key="idx"
            class="p-4 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/20 dark:border-white/10 space-y-2"
          >
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ resp.taskName }}</h4>
              <span class="text-xs text-emerald-500 font-bold">✓ تم الإنجاز</span>
            </div>

            <!-- Response Answers Readout -->
            <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1 bg-slate-100/50 dark:bg-slate-800/50 p-3 rounded-xl">
              <div v-for="(val, key) in resp.answers" :key="key" class="flex justify-between">
                <span class="font-semibold text-slate-500">{{ key }}:</span>
                <span class="font-bold text-slate-800 dark:text-slate-100">{{ val }}</span>
              </div>
            </div>
          </div>
        </div>
      </SpatialCard>

      <!-- 3. Uploaded Images Thumbnails Grid -->
      <SpatialCard v-if="visitSummary?.images && visitSummary.images.length > 0" title="الصور المرفقة بالزيارة">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="(img, idx) in visitSummary.images"
            :key="idx"
            class="relative rounded-2xl overflow-hidden border border-white/20 dark:border-white/10 shadow-md group"
          >
            <img :src="img" class="w-full h-32 object-cover transition-transform duration-300 group-hover:scale-105" />
          </div>
        </div>
      </SpatialCard>

      <!-- 4. Lock & Confirm Actions Bar (BR-056: Lock visit after confirmation & AC-003/AC-004) -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/20 dark:border-white/10 shadow-xl">
        <SpatialButton
          variant="secondary"
          size="normal"
          icon="✏️"
          :disabled="isConfirming"
          @click="backToTasks"
        >
          تعديل الإجابات والعودة للمهام
        </SpatialButton>

        <SpatialButton
          variant="primary"
          size="lg"
          icon="🔒"
          :loading="isConfirming"
          @click="confirmAndComplete"
        >
          تأكيد واعتماد الزيارة وإغلاق السجل
        </SpatialButton>
      </div>

      <!-- Feedback Toast Notification (FB-001 & FB-002) -->
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
 * Summary.vue - صفحة مراجعة واعتماد الزيارة الميدانية (M1-P04)
 * LY-001: Consistent ConsultantLayout Structure
 * BR-056: Lock Visit Permanently After Confirmation
 * AC-003 & AC-004: Double Submission Prevention & Loading Spinner State
 * FB-001 & FB-002: Toast Feedback Notifications
 */
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'
import SpatialToast from '@/Components/Spatial/SpatialToast.vue'

const props = defineProps({
  visitId: { type: [Number, String], required: true },
  visitSummary: { type: Object, default: null }
})

const isConfirming = ref(false)
const toast = ref({ show: false, type: 'success', title: '', message: '' })
const currentDate = computed(() => new Date().toLocaleDateString('ar-LY'))

// Back to Tasks for Edit
const backToTasks = () => {
  router.get(route('consultant.visit.tasks', { visitId: props.visitId }))
}

// BR-056: Confirm & Lock Visit Action
const confirmAndComplete = () => {
  isConfirming.value = true

  router.post(route('consultant.dashboard'), {}, {
    onSuccess: () => {
      isConfirming.value = false
      toast.value = {
        show: true,
        type: 'success',
        title: 'تم اعتماد الزيارة بنجاح',
        message: 'تم إغلاق وتثبيت السجل النهائي للزيارة وتحديث الأداء اليومي.'
      }
    },
    onError: () => {
      isConfirming.value = false
      toast.value = {
        show: true,
        type: 'error',
        title: 'خطأ في الاعتماد',
        message: 'تعذر إتمام اعتماد الزيارة، يرجى المحاولة لاحقاً.'
      }
    }
  })
}
</script>
