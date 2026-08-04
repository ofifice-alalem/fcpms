<template>
  <ConsultantLayout>
    <template #title>لوحة تتبع الأعمال الميدانية اليومية</template>

    <div class="space-y-6">
      <!-- 1. Daily Status Card (Top Priority - M0-P01) -->
      <!-- LY-004: Responsive mobile-first layout -->
      <SpatialCard padding="large" :glow="true" variant="gradient">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="space-y-2 text-center md:text-right">
            <div class="flex items-center justify-center md:justify-start space-x-3 space-x-reverse">
              <h2 class="text-2xl font-black text-slate-800 dark:text-white">حالة يوم العمل الحالية</h2>
              <SpatialStatusPill :status="statusPillType" :label="statusPillLabel" />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              {{ statusDescription }}
            </p>
          </div>

          <!-- Dynamic Action Buttons -->
          <div class="flex items-center space-x-3 space-x-reverse w-full md:w-auto">
            <!-- Case A: Today is Holiday or Vacation -->
            <SpatialButton
              v-if="isHoliday || isVacation"
              disabled
              variant="secondary"
              block
            >
              {{ isHoliday ? 'عطلة رسمية' : 'في إجازة رسمية' }}
            </SpatialButton>

            <!-- Case B: Today is Not Started -->
            <SpatialButton
              v-else-if="!dailyRecord"
              variant="primary"
              size="lg"
              block
              icon="🚀"
              @click="startDay"
            >
              بدء يوم العمل اليوم
            </SpatialButton>

            <!-- Case C: Today is Active -->
            <template v-else>
              <SpatialButton
                variant="primary"
                size="lg"
                icon="📍"
                @click="goToSites"
              >
                {{ activeVisit ? 'استئناف الزيارة الحالية' : 'بدء زيارة موقع جديد' }}
              </SpatialButton>
            </template>
          </div>
        </div>
      </SpatialCard>

      <!-- 2. Quick Statistics (M0-P01 - Completion Metrics) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stat 1: Completion Percentage -->
        <SpatialCard padding="normal">
          <div class="space-y-3">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400">
              <span>نسبة الإنجاز اليومية</span>
              <span class="text-indigo-500 text-sm font-mono font-bold">{{ completionPercentage }}%</span>
            </div>
            <SpatialProgressBar :value="completionPercentage" :max="100" :showLabel="false" />
            <p class="text-[11px] text-slate-400">تحديث فورياً بناءً على إجابات المهام (Revision 1.1)</p>
          </div>
        </SpatialCard>

        <!-- Stat 2: Completed vs Required Tasks -->
        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">المهام المكتملة اليوم</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                {{ completedTasks }} <span class="text-sm font-semibold text-slate-400">/ {{ requiredTasks }}</span>
              </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">
              ✅
            </div>
          </div>
        </SpatialCard>

        <!-- Stat 3: Total Sites Visited -->
        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">زيارات المواقع اليومية</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">
                {{ effectiveSiteVisits.length }} <span class="text-sm font-semibold text-slate-400">مواقع</span>
              </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-2xl">
              🏗️
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- 3. Recent Site Visits (M0-P01 & TB-001) -->
      <SpatialCard title="سجل زيارات اليوم" subtitle="جميع المواقع الميدانية المسجلة اليوم">
        <!-- Empty State Handling (FB-003) -->
        <SpatialEmptyState
          v-if="effectiveSiteVisits.length === 0"
          icon="🏗️"
          title="لم تبدأ أي زيارة ميدانية بعد"
          message="قم باختيار موقع ميداني لبدء تسجيل المهام والإشراف."
        >
          <template #action>
            <SpatialButton size="sm" variant="primary" @click="goToSites">
              اختيار موقع الآن
            </SpatialButton>
          </template>
        </SpatialEmptyState>

        <!-- Data Table (TB-001) -->
        <SpatialTable
          v-else
          :columns="tableColumns"
          :data="effectiveSiteVisits"
          :perPage="5"
        >
          <!-- Custom Site Cell -->
          <template #cell-site="{ row }">
            <div class="font-bold text-slate-800 dark:text-slate-100">
              {{ row.site?.name || 'موقع ميداني' }}
            </div>
            <span class="text-[11px] text-slate-400 font-mono">{{ row.site?.code }}</span>
          </template>

          <!-- Custom Status Cell -->
          <template #cell-status="{ row }">
            <SpatialStatusPill
              :status="row.status === 'completed' ? 'completed' : 'pending'"
              :label="row.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ'"
            />
          </template>

          <!-- Action Cell -->
          <template #actions="{ row }">
            <SpatialButton
              size="sm"
              variant="secondary"
              @click="viewVisit(row.id)"
            >
              عرض التفاصيل
            </SpatialButton>
          </template>
        </SpatialTable>
      </SpatialCard>
    </div>
  </ConsultantLayout>
</template>

<script setup>
/**
 * Dashboard.vue - لوحة تتبع الاستشاري الميداني الرئيسية (M0-P01)
 * LY-001: Consistent ConsultantLayout Structure
 * LY-004: Responsive Grid Layout
 * FB-003: Empty State Handling
 * Revision 1.1: Immediate Performance Recalculation Metrics Display
 */
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialTable from '@/Components/Spatial/SpatialTable.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'

const props = defineProps({
  dailyRecord: { type: Object, default: null },
  siteVisits: { type: Array, default: () => [] },
  isHoliday: { type: Boolean, default: false },
  isVacation: { type: Boolean, default: false },
  isWorkingDay: { type: Boolean, default: true }
})

// M0-P01 Computed Statuses
const statusPillType = computed(() => {
  if (props.isHoliday) return 'warning'
  if (props.isVacation) return 'danger'
  if (props.dailyRecord) return 'completed'
  return 'pending'
})

const statusPillLabel = computed(() => {
  if (props.isHoliday) return 'عطلة رسمية'
  if (props.isVacation) return 'إجازة'
  if (props.dailyRecord) return 'اليوم نشط'
  return 'لم يبدأ بعد'
})

const statusDescription = computed(() => {
  if (props.isHoliday) return 'اليوم يقع ضمن العطلات الرسمية المعتمدة بالشركة.'
  if (props.isVacation) return 'أنت حالياً في فترة إجازة رسمية معتمدة من HR.'
  if (props.dailyRecord) return 'يوم العمل نشط ومسجل بالمنظومة، يمكنك إضافة أو استكمال زيارات المواقع.'
  return 'اضغط على زر بدء اليوم لإنشاء سجل اليوم والبدء في متابعة المهام.'
})

// Effective Site Visits list computed from props
const effectiveSiteVisits = computed(() => {
  if (props.siteVisits && props.siteVisits.length > 0) return props.siteVisits
  if (props.dailyRecord?.siteVisits && props.dailyRecord.siteVisits.length > 0) return props.dailyRecord.siteVisits
  if (props.dailyRecord?.site_visits && props.dailyRecord.site_visits.length > 0) return props.dailyRecord.site_visits
  return []
})

// Metrics computation (Revision 1.1)
const completionPercentage = computed(() => props.dailyRecord ? parseFloat(props.dailyRecord.completion_percentage || 0) : 0)
const completedTasks = computed(() => props.dailyRecord ? props.dailyRecord.completed_daily_tasks || 0 : 0)
const requiredTasks = computed(() => props.dailyRecord ? props.dailyRecord.required_daily_tasks || 0 : 0)

const activeVisit = computed(() => effectiveSiteVisits.value.find(v => v.status === 'in_progress'))

// Table Specifications (TB-001)
const tableColumns = [
  { key: 'site', label: 'اسم الموقع', sortable: true },
  { key: 'visit_started_at', label: 'توقيت الدخول', sortable: true },
  { key: 'status', label: 'حالة الزيارة', sortable: true },
]

// Navigation Actions
const startDay = () => {
  router.get(route('consultant.sites'))
}

const goToSites = () => {
  if (activeVisit.value) {
    router.get(route('consultant.visit.tasks', { visitId: activeVisit.value.id }))
  } else {
    router.get(route('consultant.sites'))
  }
}

const viewVisit = (visitId) => {
  router.get(route('consultant.visit.tasks', { visitId }))
}
</script>
