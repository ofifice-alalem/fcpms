<template>
  <div class="space-y-1.5">
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
      {{ label }}
    </label>

    <div class="inline-flex items-center p-1 rounded-xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white/30 dark:border-white/10 shadow-sm">
      <button
        type="button"
        @click="decrement"
        :disabled="modelValue <= min"
        class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold flex items-center justify-center transition-colors disabled:opacity-40"
      >
        -
      </button>
      <input
        type="number"
        :value="modelValue"
        @input="updateValue($event.target.value)"
        class="w-14 text-center text-sm font-bold bg-transparent border-none outline-none text-slate-800 dark:text-slate-100"
      />
      <button
        type="button"
        @click="increment"
        :disabled="modelValue >= max"
        class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold flex items-center justify-center transition-colors disabled:opacity-40"
      >
        +
      </button>
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialStepper.vue - عداد الأرقام التفاعلي
 */
const props = defineProps({
  modelValue: { type: Number, default: 0 },
  label: { type: String, default: '' },
  min: { type: Number, default: 0 },
  max: { type: Number, default: 9999 },
  step: { type: Number, default: 1 }
})

const emit = defineEmits(['update:modelValue'])

const increment = () => {
  if (props.modelValue + props.step <= props.max) {
    emit('update:modelValue', props.modelValue + props.step)
  }
}

const decrement = () => {
  if (props.modelValue - props.step >= props.min) {
    emit('update:modelValue', props.modelValue - props.step)
  }
}

const updateValue = (val) => {
  const num = parseInt(val, 10)
  if (!isNaN(num) && num >= props.min && num <= props.max) {
    emit('update:modelValue', num)
  }
}
</script>
