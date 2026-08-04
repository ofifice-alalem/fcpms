<template>
  <HRLayout>
    <template #title>إدارة الاستشاريين الميدانيين</template>

    <div class="space-y-6">
      <!-- 1. Header Bar (M1-P01) -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">قائمة الاستشاريين الميدانيين</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">متابعة السجلات الوظيفية، جداول العمل، والإجازات للاستشاريين بالشركة</p>
        </div>

        <SpatialButton
          variant="primary"
          size="lg"
          icon="➕"
          @click="showCreateDrawer = true"
        >
          إضافة استشاري جديد
        </SpatialButton>
      </div>

      <!-- 2. Statistics Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي الاستشاريين</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ consultants.length }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-2xl">
              👨‍💼
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">النشطون على رأس العمل</p>
              <h3 class="text-3xl font-black text-emerald-500 font-mono mt-1">{{ activeCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">
              ✅
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">في إجازات رسمية</p>
              <h3 class="text-3xl font-black text-amber-500 font-mono mt-1">{{ vacationCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-2xl">
              🌴
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- 3. Consultants Data Table (TB-001 to TB-004) -->
      <SpatialCard>
        <SpatialTable
          :columns="tableColumns"
          :data="consultants"
          :selectable="true"
          :perPage="10"
        >
          <!-- Employee Number Custom Cell -->
          <template #cell-employee_number="{ value }">
            <span class="font-mono font-bold text-indigo-500 bg-indigo-500/10 px-2.5 py-1 rounded-lg">
              {{ value }}
            </span>
          </template>

          <!-- Status Custom Cell -->
          <template #cell-status="{ value }">
            <SpatialStatusPill
              :status="value === 'active' ? 'active' : value === 'vacation' ? 'pending' : 'inactive'"
              :label="value === 'active' ? 'نشط' : value === 'vacation' ? 'إجازة' : 'غير نشط'"
            />
          </template>

          <!-- Actions Column (TB-004) -->
          <template #actions="{ row }">
            <div class="flex items-center justify-center space-x-2 space-x-reverse">
              <SpatialIconBtn variant="ghost" title="عرض الملف الكامل" @click="viewProfile(row.id)">
                👁️
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="تعديل البيانات" @click="openEdit(row)">
                ✏️
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="تغيير الحالة" @click="openStatus(row)">
                🔄
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="تعيين جدول عمل" @click="openSchedule(row)">
                📅
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="تخصيص إجازة" @click="openLeave(row)">
                🌴
              </SpatialIconBtn>
            </div>
          </template>
        </SpatialTable>
      </SpatialCard>

      <!-- Drawers & Modals -->
      <CreateConsultantDrawer
        :show="showCreateDrawer"
        @close="showCreateDrawer = false"
        @success="handleSuccess"
      />

      <EditConsultantDrawer
        :show="showEditDrawer"
        :consultant="selectedConsultant"
        @close="showEditDrawer = false"
        @success="handleSuccess"
      />

      <ChangeStatusModal
        :show="showStatusModal"
        :consultant="selectedConsultant"
        @close="showStatusModal = false"
        @success="handleSuccess"
      />

      <AssignScheduleModal
        :show="showScheduleModal"
        :consultant="selectedConsultant"
        :schedules="schedules"
        @close="showScheduleModal = false"
        @success="handleSuccess"
      />

      <AssignLeaveModal
        :show="showLeaveModal"
        :consultant="selectedConsultant"
        @close="showLeaveModal = false"
        @success="handleSuccess"
      />

      <!-- Feedback Toast Notification (FB-001) -->
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
 * Index.vue - القائمة الرئيسية لإدارة الاستشاريين الميدانيين لـ HR (M1-P01)
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
import CreateConsultantDrawer from './Create.vue'
import EditConsultantDrawer from './Edit.vue'
import ChangeStatusModal from './ChangeStatus.vue'
import AssignScheduleModal from './AssignSchedule.vue'
import AssignLeaveModal from './AssignLeave.vue'

const props = defineProps({
  consultants: { type: Array, default: () => [] },
  schedules: { type: Array, default: () => [] }
})

const showCreateDrawer = ref(false)
const showEditDrawer = ref(false)
const showStatusModal = ref(false)
const showScheduleModal = ref(false)
const showLeaveModal = ref(false)
const selectedConsultant = ref(null)

const toast = ref({ show: false, type: 'success', title: '', message: '' })

const tableColumns = [
  { key: 'employee_number', label: 'الرقم الوظيفي', sortable: true },
  { key: 'full_name', label: 'الاسم الكامل', sortable: true },
  { key: 'specialization', label: 'التخصص', sortable: true },
  { key: 'status', label: 'الحالة', sortable: true }
]

const activeCount = computed(() => props.consultants.filter(c => c.status === 'active').length)
const vacationCount = computed(() => props.consultants.filter(c => c.status === 'vacation').length)

const viewProfile = (id) => {
  router.get(route('hr.consultants.show', id))
}

const openEdit = (c) => { selectedConsultant.value = c; showEditDrawer.value = true }
const openStatus = (c) => { selectedConsultant.value = c; showStatusModal.value = true }
const openSchedule = (c) => { selectedConsultant.value = c; showScheduleModal.value = true }
const openLeave = (c) => { selectedConsultant.value = c; showLeaveModal.value = true }

const handleSuccess = (msg) => {
  toast.value = { show: true, type: 'success', title: 'تمت العملية', message: msg }
}
</script>
