<template>
  <HRLayout>
    <template #title>الملف الوظيفي للاستشاري - {{ consultant?.full_name }}</template>

    <div class="space-y-6">
      <!-- 1. Profile Header Glass Card (M1-P02) -->
      <SpatialCard padding="large" variant="gradient">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center space-x-4 space-x-reverse">
            <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white font-black text-2xl shadow-xl">
              {{ consultant?.full_name?.charAt(0) || 'م' }}
            </div>
            <div>
              <div class="flex items-center space-x-3 space-x-reverse">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white">{{ consultant?.full_name }}</h2>
                <SpatialStatusPill :status="consultant?.status" :label="consultant?.status" />
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                الرقم الوظيفي: <span class="font-mono font-bold text-indigo-500">{{ consultant?.employee_number }}</span> | التخصص: {{ consultant?.specialization || 'إشراف عام' }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-2 space-x-reverse">
            <SpatialButton size="sm" variant="secondary" icon="✏️" @click="showEditDrawer = true">
              تعديل البيانات
            </SpatialButton>
            <SpatialButton size="sm" variant="primary" icon="🔄" @click="showStatusModal = true">
              تغيير الحالة
            </SpatialButton>
          </div>
        </div>
      </SpatialCard>

      <!-- 2. Profile Tabs (SpatialTabs - M1-P02) -->
      <SpatialTabs v-model="activeTab" :tabs="tabList">
        <template #default="{ activeTab: current }">
          <!-- Tab 1: General Info -->
          <div v-if="current === 'info'" class="space-y-6">
            <SpatialCard title="البيانات العامة والارتباط الوظيفي">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                  <span class="text-slate-400 block text-xs">رقم الهاتف:</span>
                  <span class="font-bold text-slate-800 dark:text-slate-100">{{ consultant?.phone || 'غير مسجل' }}</span>
                </div>
                <div>
                  <span class="text-slate-400 block text-xs">قالب جدول العمل:</span>
                  <span class="font-bold text-indigo-500">{{ consultant?.work_schedule_template?.name || 'الجدول الافتراضي' }}</span>
                </div>
              </div>
            </SpatialCard>
          </div>

          <!-- Tab 2: Performance Records -->
          <div v-else-if="current === 'performance'" class="space-y-6">
            <SpatialCard title="سجل مؤشرات الأداء الميداني">
              <SpatialTable
                :columns="[
                  { key: 'work_date', label: 'تاريخ اليوم', sortable: true },
                  { key: 'completed_daily_tasks', label: 'المهام المكتملة', sortable: true },
                  { key: 'required_daily_tasks', label: 'المهام المطلوبة', sortable: true },
                  { key: 'completion_percentage', label: 'نسبة الإنجاز %', sortable: true }
                ]"
                :data="consultant?.daily_records || []"
                :perPage="5"
              />
            </SpatialCard>
          </div>

          <!-- Tab 3: Leaves -->
          <div v-else-if="current === 'leaves'" class="space-y-6">
            <SpatialCard title="سجل إجازات الاستشاري">
              <SpatialEmptyState v-if="!consultant?.leaves?.length" title="لا توجد إجازات مسجلة" />
              <SpatialTable
                v-else
                :columns="[
                  { key: 'start_date', label: 'تاريخ البدء' },
                  { key: 'end_date', label: 'تاريخ النهاية' },
                  { key: 'type', label: 'نوع الإجازة' }
                ]"
                :data="consultant?.leaves || []"
              />
            </SpatialCard>
          </div>
        </template>
      </SpatialTabs>

      <!-- Drawers & Modals -->
      <EditDrawer :show="showEditDrawer" :consultant="consultant" @close="showEditDrawer = false" />
      <ChangeStatusModal :show="showStatusModal" :consultant="consultant" @close="showStatusModal = false" />
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Show.vue - الملف الوظيفي الكامل للاستشاري (M1-P02)
 * LY-001: HRLayout Structure
 * SpatialTabs integration
 */
import { ref } from 'vue'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialTabs from '@/Components/Spatial/SpatialTabs.vue'
import SpatialTable from '@/Components/Spatial/SpatialTable.vue'
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue'
import EditDrawer from './Edit.vue'
import ChangeStatusModal from './ChangeStatus.vue'

defineProps({
  consultant: { type: Object, default: null }
})

const activeTab = ref('info')
const showEditDrawer = ref(false)
const showStatusModal = ref(false)

const tabList = [
  { id: 'info', label: 'البيانات العامة', icon: '👤' },
  { id: 'performance', label: 'مؤشرات الأداء', icon: '📈' },
  { id: 'leaves', label: 'سجل الإجازات', icon: '🌴' }
]
</script>
