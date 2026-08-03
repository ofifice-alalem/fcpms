<template>
  <HRLayout>
    <template #title>تعديل نموذج المهمة الميدانية (Task Builder)</template>

    <div class="space-y-6 max-w-5xl mx-auto">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white">تعديل قوالب المهمة الديناميكية</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تحديث مكونات وتخصيص نموذج المهمة</p>
        </div>

        <SpatialButton variant="ghost" icon="⬅️" @click="cancel">إلغاء والعودة</SpatialButton>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <SpatialCard title="البيانات الأساسية للمهمة">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <SpatialInput v-model="form.name" label="عنوان المهمة" :required="true" :error="errors.name" />
            <SpatialDropdown v-model="form.type" label="نوع المهمة" :options="[{ label: 'يومية إجبارية', value: 'daily' }, { label: 'عند الطلب', value: 'on_demand' }]" />
          </div>
        </SpatialCard>

        <div class="flex items-center justify-end space-x-3 space-x-reverse">
          <SpatialButton type="button" variant="ghost" @click="cancel">إلغاء</SpatialButton>
          <SpatialButton type="submit" variant="primary" size="lg" icon="💾" :loading="isSubmitting">
            حفظ التعديلات
          </SpatialButton>
        </div>
      </form>
    </div>
  </HRLayout>
</template>

<script setup>
/**
 * Edit.vue - تعديل نموذج المهمة (M3-P02)
 */
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import HRLayout from '@/Layouts/HRLayout.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'

const props = defineProps({
  task: { type: Object, default: null }
})

const isSubmitting = ref(false)
const errors = reactive({})

const form = reactive({
  name: '',
  type: 'daily'
})

watch(() => props.task, (t) => {
  if (t) {
    form.name = t.name || ''
    form.type = t.type || 'daily'
  }
}, { immediate: true })

const cancel = () => router.get(route('hr.tasks.index'))

const submit = () => {
  if (!props.task) return
  isSubmitting.value = true

  router.put(route('hr.tasks.index', { id: props.task.id }), form, {
    onSuccess: () => { isSubmitting.value = false },
    onError: (errs) => { isSubmitting.value = false; Object.assign(errors, errs) }
  })
}
</script>
