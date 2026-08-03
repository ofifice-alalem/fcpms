<template>
  <div class="space-y-2 w-full">
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
      {{ label }}
    </label>

    <div :class="[gridColsClass, 'grid gap-3']">
      <div
        v-for="option in options"
        :key="getOptionValue(option)"
        @click="select(getOptionValue(option))"
        class="p-4 rounded-2xl border transition-all duration-300 cursor-pointer backdrop-blur-xl relative overflow-hidden flex items-start space-x-3 space-x-reverse"
        :class="[
          modelValue === getOptionValue(option)
            ? 'bg-gradient-to-br from-indigo-500/15 to-purple-500/15 border-indigo-500 ring-2 ring-indigo-500/30 text-indigo-900 dark:text-indigo-200'
            : 'bg-white/60 dark:bg-slate-900/60 border-white/20 dark:border-white/10 hover:border-indigo-300 dark:hover:border-indigo-800 text-slate-700 dark:text-slate-300'
        ]"
      >
        <!-- Radio Circle -->
        <div class="w-5 h-5 rounded-full border flex items-center justify-center mt-0.5"
             :class="[modelValue === getOptionValue(option) ? 'border-indigo-500 bg-indigo-500 text-white' : 'border-slate-400']">
          <div v-if="modelValue === getOptionValue(option)" class="w-2 h-2 rounded-full bg-white"></div>
        </div>

        <!-- Option Content -->
        <div class="flex-1">
          <p class="text-xs font-bold">{{ getOptionLabel(option) }}</p>
          <p v-if="getOptionDesc(option)" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
            {{ getOptionDesc(option) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialRadioGroup.vue - مجموعة الخيارات البطاقية التفاعلية
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: null },
  options: { type: Array, required: true }, // [{ label, value, description }]
  label: { type: String, default: '' },
  cols: { type: Number, default: 1 }
})

const emit = defineEmits(['update:modelValue', 'change'])

const getOptionValue = o => typeof o === 'object' ? o.value : o
const getOptionLabel = o => typeof o === 'object' ? o.label : o
const getOptionDesc = o => typeof o === 'object' ? o.description : null

const select = (val) => {
  emit('update:modelValue', val)
  emit('change', val)
}

const gridColsClass = computed(() => {
  switch (props.cols) {
    case 2: return 'grid-cols-1 md:grid-cols-2'
    case 3: return 'grid-cols-1 md:grid-cols-3'
    case 4: return 'grid-cols-1 md:grid-cols-4'
    case 1:
    default: return 'grid-cols-1'
  }
})
</script>
