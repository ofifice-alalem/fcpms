<template>
  <HRLayout>
    <template #title>تفاصيل الموقع الميداني - {{ site?.name }}</template>

    <div class="space-y-6">
      <!-- Header Card -->
      <SpatialCard padding="large" variant="gradient">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div>
            <div class="flex items-center space-x-3 space-x-reverse">
              <h2 class="text-2xl font-black text-slate-800 dark:text-white">{{ site?.name }}</h2>
              <SpatialStatusPill :status="site?.status" :label="site?.status === 'active' ? 'نشط' : 'معطل'" />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              رمز الموقع: <span class="font-mono font-bold text-indigo-500">{{ site?.code }}</span> | الموقع الجغرافي: {{ site?.location || 'غير محدد' }}
            </p>
          </div>

          <SpatialButton variant="secondary" icon="⬅️" @click="backToIndex">
            العودة لقائمة المواقع
          </SpatialButton>
        </div>
      </SpatialCard>

      <!-- Recent Visits List -->
      <SpatialCard title="سجل زيارات هذا الموقع" subtitle="جميع الزيارات الإشرافية الميدانية المسجلة بهذا الموقع">
        <SpatialEmptyState
          v-if="!site?.visits || site.visits.length === 0"
          icon="🏗️"
          title="لا توجد زيارات مسجلة لهذا الموقع"
          message="لم يقم أي استشاري ميداني بزيارة هذا الموقع حتى الآن."
        />

        <SpatialTable
          v-else
          :columns="tableColumns"
          :data="site.visits"
          :perPage="10"
        />
      </SpatialCard>
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Show.vue - عرض تفاصيل موقع ميداني فردي وسجل زياراته (M2-D01)
 */
import { router } from '@inertiajs/vue3'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialTable from '@/Components/Spatial/SpatialTable.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'

defineProps({
  site: { type: Object, default: null }
})

const tableColumns = [
  { key: 'id', label: 'رقم الزيارة', sortable: true },
  { key: 'visit_started_at', label: 'توقيت البدء', sortable: true },
  { key: 'status', label: 'الحالة', sortable: true }
]

const backToIndex = () => {
  router.get(route('hr.sites.index'))
}
</script>
