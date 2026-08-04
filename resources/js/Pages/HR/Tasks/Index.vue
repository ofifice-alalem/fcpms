<template>
  <HRLayout>
    <template #title>إدارة نماذج المهام الميدانية (Task Builder)</template>

    <div class="space-y-6">
      <!-- 1. Header Bar (M3-P01) -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">قائمة نماذج المهام الميدانية</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تصميم وبناء ونشر نماذج الإشراف والرقابة الميدانية للاستشاريين</p>
        </div>

        <SpatialButton
          variant="primary"
          size="lg"
          icon="➕"
          @click="createTask"
        >
          إنشاء نموذج مهمة جديد (Task Builder)
        </SpatialButton>
      </div>

      <!-- 2. Statistics Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي النماذج</p>
              <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ tasks.length }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-2xl">
              📋
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">النماذج اليومية الإجبارية</p>
              <h3 class="text-3xl font-black text-emerald-500 font-mono mt-1">{{ dailyTasksCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">
              ⚡
            </div>
          </div>
        </SpatialCard>

        <SpatialCard padding="normal">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-500 dark:text-slate-400">مهام عند الطلب (On-Demand)</p>
              <h3 class="text-3xl font-black text-purple-500 font-mono mt-1">{{ onDemandTasksCount }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-2xl">
              ➕
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- 3. Tasks Data Table (TB-001 to TB-004) -->
      <SpatialCard>
        <SpatialTable
          :columns="tableColumns"
          :data="tasks"
          :selectable="true"
          :perPage="10"
        >
          <!-- Task Type Custom Cell -->
          <template #cell-type="{ value }">
            <SpatialStatusPill
              :status="value === 'daily' ? 'completed' : 'info'"
              :label="value === 'daily' ? 'يومية إجبارية' : 'عند الطلب'"
            />
          </template>

          <!-- Status Custom Cell -->
          <template #cell-status="{ value }">
            <SpatialStatusPill
              :status="value === 'active' ? 'active' : 'inactive'"
              :label="value === 'active' ? 'نشط / مفعل' : 'معطل'"
            />
          </template>

          <!-- Actions Column (TB-004) -->
          <template #actions="{ row }">
            <div class="flex items-center justify-center space-x-2 space-x-reverse">
              <SpatialIconBtn variant="ghost" title="معاينة النموذج" @click="previewTask(row.id)">
                👁️
              </SpatialIconBtn>
              <SpatialIconBtn variant="ghost" title="تعديل النموذج" @click="editTask(row.id)">
                ✏️
              </SpatialIconBtn>
              <SpatialIconBtn v-if="row.status !== 'active'" variant="ghost" title="نشر وتفعيل" @click="openPublish(row)">
                🚀
              </SpatialIconBtn>
              <SpatialIconBtn v-else variant="ghost" title="تعطيل" @click="openDisable(row)">
                🚫
              </SpatialIconBtn>
            </div>
          </template>
        </SpatialTable>
      </SpatialCard>

      <!-- Modals -->
      <PublishModal :show="showPublishModal" :task="selectedTask" @close="showPublishModal = false" @success="handleSuccess" />
      <DisableModal :show="showDisableModal" :task="selectedTask" @close="showDisableModal = false" @success="handleSuccess" />

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
 * Index.vue - القائمة الرئيسية لإدارة ونشر نماذج المهام (M3-P01)
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
import PublishModal from './PublishModal.vue'
import DisableModal from './DisableModal.vue'

const props = defineProps({
  tasks: { type: Array, default: () => [] }
})

const showPublishModal = ref(false)
const showDisableModal = ref(false)
const selectedTask = ref(null)

const toast = ref({ show: false, type: 'success', title: '', message: '' })

const tableColumns = [
  { key: 'name', label: 'اسم نموذج المهمة', sortable: true },
  { key: 'type', label: 'نوع المهمة', sortable: true },
  { key: 'status', label: 'حالة النموذج', sortable: true }
]

const dailyTasksCount = computed(() => props.tasks.filter(t => t.type === 'daily').length)
const onDemandTasksCount = computed(() => props.tasks.filter(t => t.type === 'on_demand').length)

const createTask = () => router.get(route('hr.tasks.create'))
const editTask = (id) => router.get(route('hr.tasks.edit', id))
const previewTask = (id) => router.get(route('hr.tasks.preview', id))

const openPublish = (t) => { selectedTask.value = t; showPublishModal.value = true }
const openDisable = (t) => { selectedTask.value = t; showDisableModal.value = true }

const handleSuccess = (msg) => {
  toast.value = { show: true, type: 'success', title: 'تمت العملية', message: msg }
}
</script>
