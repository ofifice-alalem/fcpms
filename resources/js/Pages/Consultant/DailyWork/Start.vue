<template>
  <ConsultantLayout>
    <template #title>بدء يوم العمل الميداني</template>

    <!-- LY-004: Responsive centered glass container -->
    <div class="max-w-2xl mx-auto py-8 space-y-6">
      <!-- M1-P01: Main Start Working Day Glass Card -->
      <SpatialCard padding="large" :glow="true" variant="gradient">
        <div class="space-y-6 text-center">
          <!-- Header Icon & Title -->
          <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white text-3xl shadow-xl mx-auto">
            🚀
          </div>

          <div class="space-y-2">
            <h2 class="text-2xl font-black text-slate-800 dark:text-white">بدء سجّل الأعمال الميدانية اليومية</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              التاريخ اليوم: <span class="font-bold text-indigo-500 font-mono">{{ todayDate }}</span>
            </p>
          </div>

          <!-- Status Banner (BR-018: Working Day Validation) -->
          <div
            class="p-4 rounded-2xl border text-xs font-bold transition-all duration-300 backdrop-blur-md"
            :class="[
              canStart
                ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
                : 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30'
            ]"
          >
            <div class="flex items-center justify-center space-x-2 space-x-reverse">
              <span>{{ canStart ? '✅' : '⚠️' }}</span>
              <span>{{ statusMessage }}</span>
            </div>
          </div>

          <!-- Consultant Quick Profile Info -->
          <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-white/40 dark:bg-slate-900/40 border border-white/20 dark:border-white/10 text-right text-xs">
            <div>
              <span class="text-slate-400 block mb-0.5">اسم الاستشاري:</span>
              <span class="font-bold text-slate-800 dark:text-slate-100">{{ consultant?.full_name || user?.name }}</span>
            </div>
            <div>
              <span class="text-slate-400 block mb-0.5">الرقم الوظيفي:</span>
              <span class="font-mono font-bold text-indigo-500">{{ consultant?.employee_number || 'EMP-1001' }}</span>
            </div>
          </div>

          <!-- Start Working Day Button (AC-004: Loading state) -->
          <div class="pt-2">
            <SpatialButton
              variant="primary"
              size="lg"
              block
              :disabled="!canStart"
              :loading="loading"
              icon="⚡"
              @click="handleStartDay"
            >
              بدء يوم العمل والانتقال للمواقع
            </SpatialButton>
          </div>
        </div>
      </SpatialCard>

      <!-- Toast Feedback Notification (FB-001 & FB-002) -->
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
 * Start.vue - صفحة بدء يوم العمل للاستشاري (M1-P01)
 * LY-001: ConsultantLayout Structure
 * BR-018: Valid Work Day Enforcement
 * AC-004: Loading State on Action Buttons
 * FB-001 & FB-002: Toast Feedback Notifications
 */
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialToast from '@/Components/Spatial/SpatialToast.vue'

const props = defineProps({
  canStart: { type: Boolean, default: true },
  message: { type: String, default: '' },
  consultant: { type: Object, default: null },
  today: { type: String, default: '' }
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
const loading = ref(false)

const toast = ref({
  show: false,
  type: 'success',
  title: '',
  message: ''
})

const todayDate = computed(() => props.today || new Date().toLocaleDateString('ar-LY'))

const statusMessage = computed(() => {
  if (props.message) return props.message
  return props.canStart ? 'اليوم هو يوم عمل رسمي مطالب بالإشراف والمتابعة.' : 'عفواً، لا يمكنك بدء العمل اليوم.'
})

// Action Handler (AC-004 & FB-001)
const handleStartDay = () => {
  loading.value = true

  router.get(route('consultant.sites'), {}, {
    onSuccess: () => {
      loading.value = false
      toast.value = {
        show: true,
        type: 'success',
        title: 'تم بدء اليوم بنجاح',
        message: 'تم إنشاء سجل اليوم وبدء العمل الميداني بنجاح.'
      }
    },
    onError: () => {
      loading.value = false
      toast.value = {
        show: true,
        type: 'error',
        title: 'خطأ في بدء اليوم',
        message: 'تعذر بدء يوم العمل، يرجى المحاولة لاحقاً.'
      }
    }
  })
}
</script>
