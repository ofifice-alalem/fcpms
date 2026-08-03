<template>
  <HRLayout>
    <template #title>معاينة نموذج المهمة الميدانية (M3-P03)</template>

    <div class="space-y-6 max-w-4xl mx-auto">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">معاينة النموذج كما يظهر للاستشاري</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">وضع القراءة فقط للمراجعة واختبار العرض والمكونات الشرطية</p>
        </div>

        <SpatialButton variant="secondary" icon="⬅️" @click="back">
          العودة لقائمة المهام
        </SpatialButton>
      </div>

      <!-- Task Preview Card -->
      <SpatialCard padding="large" :glow="true">
        <template #header>
          <div class="flex items-center justify-between w-full">
            <div>
              <div class="flex items-center space-x-2 space-x-reverse">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ task?.name }}</h3>
                <SpatialStatusPill
                  :status="task?.is_required ? 'completed' : 'info'"
                  :label="task?.is_required ? 'إجباري' : 'اختياري'"
                />
              </div>
              <p class="text-xs text-slate-500 mt-1">{{ task?.description }}</p>
            </div>
            <span class="text-xs font-mono font-bold text-indigo-500 bg-indigo-500/10 px-3 py-1.5 rounded-xl">
              وزن الأداء: {{ task?.performance_weight }}%
            </span>
          </div>
        </template>

        <!-- Components Preview Render -->
        <div class="space-y-4 pt-2">
          <div v-for="comp in task?.components" :key="comp.id" class="p-3 rounded-xl bg-slate-100/50 dark:bg-slate-800/50 border border-slate-200/50 dark:border-slate-700/50">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
              {{ comp.label }}
              <span v-if="comp.is_required" class="text-rose-500">*</span>
              <span class="text-[10px] text-slate-400 mr-2 font-mono">({{ comp.component_type }})</span>
            </label>
            <div class="text-xs text-slate-400 italic">مُكون تفاعلي مخصص للاستشاري في التطبيق</div>
          </div>
        </div>
      </SpatialCard>
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Preview.vue - معاينة نموذج المهمة الميدانية للقراءة فقط (M3-P03)
 */
import { router } from '@inertiajs/vue3'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

defineProps({
  task: { type: Object, default: null }
})

const back = () => {
  router.get(route('hr.tasks.index'))
}
</script>
