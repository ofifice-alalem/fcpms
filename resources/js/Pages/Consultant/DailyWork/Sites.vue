<template>
  <ConsultantLayout>
    <template #title>اختيار الموقع الميداني للزيارة</template>

    <div class="space-y-6">
      <!-- M1-P02: Header & Instant Search Filter -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-800 dark:text-white">المواقع الميدانية المتاحة</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">اختر موقعاً ميدانياً لبدء تسجيل زيارة العمل وتعبئة المهام</p>
        </div>

        <div class="w-full md:w-80">
          <SpatialInput
            v-model="searchQuery"
            placeholder="بحث باسم الموقع أو المدينة..."
            prefixIcon="🔍"
          />
        </div>
      </div>

      <!-- FB-004: Skeleton Loading Grid State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <SpatialSkeleton v-for="i in 6" :key="i" type="card" />
      </div>

      <!-- FB-003: Empty State Handling -->
      <SpatialEmptyState
        v-else-if="filteredSites.length === 0"
        icon="🏗️"
        title="لا توجد مواقع ميدانية مطابقة"
        message="لم نتمكن من العثور على أي موقع فعال يطابق نتائج البحث الحالية."
      />

      <!-- LY-004: Responsive 3-Column Sites Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <SpatialCard
          v-for="site in filteredSites"
          :key="site.id"
          padding="normal"
          :glow="false"
          class="group hover:border-indigo-500 transition-all duration-300 flex flex-col justify-between"
        >
          <div class="space-y-3">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 group-hover:text-indigo-500 transition-colors">
                  {{ site.name }}
                </h3>
                <span class="text-xs text-slate-400 font-mono">رمز الموقع: {{ site.code }}</span>
              </div>
              <SpatialStatusPill status="active" label="نشط" />
            </div>

            <div class="flex items-center text-xs text-slate-500 dark:text-slate-400 space-x-1 space-x-reverse">
              <span>📍</span>
              <span>{{ site.location || 'الموقع الرئيسي' }}</span>
            </div>
          </div>

          <!-- Action Button to Start Visit -->
          <div class="mt-6 pt-4 border-t border-slate-200/50 dark:border-slate-800/50">
            <SpatialButton
              block
              variant="primary"
              size="normal"
              icon="🚀"
              :loading="selectingSiteId === site.id"
              @click="selectSite(site.id)"
            >
              بدء زيارة الموقع
            </SpatialButton>
          </div>
        </SpatialCard>
      </div>
    </div>
  </ConsultantLayout>
</template>

<script setup>
/**
 * Sites.vue - صفحة اختيار الموقع الميداني للاستشاري (M1-P02)
 * LY-001: Consistent ConsultantLayout Structure
 * LY-004: Responsive Mobile-first Grid Layout
 * FB-003: Empty State Display when No Active Sites
 * FB-004: Skeleton Loader Display during Loading State
 */
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'
import SpatialSkeleton from '@/Components/Spatial/SpatialSkeleton.vue'

const props = defineProps({
  sites: { type: Array, default: () => [] }
})

const searchQuery = ref('')
const loading = ref(false)
const selectingSiteId = ref(null)

// Client-side Instant Search Filtering
const filteredSites = computed(() => {
  if (!searchQuery.value) return props.sites
  const query = searchQuery.value.toLowerCase()
  return props.sites.filter(site =>
    site.name.toLowerCase().includes(query) ||
    (site.location && site.location.toLowerCase().includes(query)) ||
    (site.code && site.code.toLowerCase().includes(query))
  )
})

// Navigate to Site Tasks / Start Visit
const selectSite = (siteId) => {
  selectingSiteId.value = siteId
  router.post(route('consultant.visit.start'), { site_id: siteId }, {
    onFinish: () => {
      selectingSiteId.value = null
    }
  })
}
</script>
