<template>
  <HRLayout>
    <template #title>إدارة المواقع الميدانية</template>

    <div class="space-y-6">
      <!-- 1. Header & Actions Bar (M2-P01) -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">قائمة المواقع الميدانية</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">إدارة وتحديث بيانات وتحديد حالة المواقع الإشرافية بالشركة</p>
        </div>

        <SpatialButton
          variant="primary"
          size="lg"
          icon="➕"
          @click="showCreateDrawer = true"
        >
          إضافة موقع ميداني جديد
        </SpatialButton>
      </div>

      <!-- 2. Statistics Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي المواقع</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ sites.length }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-2xl">
              🏗️
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">المواقع النشطة</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1 font-mono text-emerald-500">{{ activeSitesCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">
              ✅
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">المواقع المعطلة</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1 font-mono text-rose-500">{{ inactiveSitesCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-2xl">
              🚫
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- 3. Sites Data Table with Search, Filter & Bulk Actions (TB-001, TB-002, TB-003, TB-004) -->
      <SpatialCard>
        <SpatialTable
          :columns="tableColumns"
          :data="sites"
          :selectable="true"
          :perPage="10"
        >
          <!-- Bulk Actions Slot -->
          <template #bulk-actions="{ selected }">
            <SpatialButton size="sm" variant="secondary" @click="bulkToggleStatus(selected)">
              تغيير حالة المواقع المحددة
            </SpatialButton>
          </template>

          <!-- Custom Code Cell -->
          <template #cell-code="{ value }">
            <span class="font-mono font-bold text-indigo-500 bg-indigo-500/10 px-2.5 py-1 rounded-lg">
              {{ value }}
            </span>
          </template>

          <!-- Custom Status Cell -->
          <template #cell-status="{ value }">
            <SpatialStatusPill
              :status="value === 'active' ? 'active' : 'inactive'"
              :label="value === 'active' ? 'نشط' : 'معطل'"
            />
          </template>

          <!-- Actions Column (TB-004) -->
          <template #actions="{ row }">
            <div class="flex items-center justify-center space-x-2 space-x-reverse">
              <SpatialIconBtn variant="ghost" title="تعديل" @click="openEdit(row)">
                ✏️
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="عرض الزيارات" @click="showSite(row.id)">
                👁️
              </SpatialIconBtn>
            </div>
          </template>
        </SpatialTable>
      </SpatialCard>

      <!-- Drawers & Modals -->
      <CreateSiteDrawer
        :show="showCreateDrawer"
        @close="showCreateDrawer = false"
        @success="handleSuccess"
      />

      <EditSiteDrawer
        :show="showEditDrawer"
        :site="selectedSite"
        @close="showEditDrawer = false"
        @success="handleSuccess"
      />

      <!-- Feedback Toast Notification (FB-001 & FB-002) -->
      <SpatialToast
        v-if="toast.show"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        @dismiss="toast.show = false"
      />
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Index.vue - الصفحة الرئيسية لإدارة المواقع الميدانية لـ HR (M2-P01)
 * LY-001: HRLayout Structure
 * TB-001 to TB-004: Table Search, Filter, Sort, Pagination & Actions Column
 * FM-002: Create & Edit via SpatialDrawers
 * DL-001 & DL-003: Confirm Actions
 * FB-001 & FB-002: Toast Notifications
 */
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialIconBtn from '@/Components/Spatial/SpatialIconBtn.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialTable from '@/Components/Spatial/SpatialTable.vue'
import SpatialToast from '@/Components/Spatial/SpatialToast.vue'
import CreateSiteDrawer from './Create.vue'
import EditSiteDrawer from './Edit.vue'

const props = defineProps({
  sites: { type: Array, default: () => [] }
})

const showCreateDrawer = ref(false)
const showEditDrawer = ref(false)
const selectedSite = ref(null)

const toast = ref({ show: false, type: 'success', title: '', message: '' })

// Table Specs (TB-001)
const tableColumns = [
  { key: 'code', label: 'رمز الموقع', sortable: true },
  { key: 'name', label: 'اسم الموقع', sortable: true },
  { key: 'location', label: 'المدينة / الموقع', sortable: true },
  { key: 'status', label: 'الحالة', sortable: true }
]

const activeSitesCount = computed(() => props.sites.filter(s => s.status === 'active').length)
const inactiveSitesCount = computed(() => props.sites.filter(s => s.status === 'inactive').length)

const openEdit = (site) => {
  selectedSite.value = site
  showEditDrawer.value = true
}

const showSite = (id) => {
  router.get(route('hr.sites.index', { site: id }))
}

const handleSuccess = (msg) => {
  toast.value = {
    show: true,
    type: 'success',
    title: 'نجاح العملية',
    message: msg
  }
}

const bulkToggleStatus = (selectedIds) => {
  toast.value = {
    show: true,
    type: 'info',
    title: 'الإجراءات الجماعية',
    message: `تم اختيار ${selectedIds.length} موقع لتنفيذ الإجراء الجماعي.`
  }
}
</script>
