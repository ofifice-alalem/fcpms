<template>
  <div class="relative space-y-1.5 w-full" ref="dropdownRef">
    <!-- Label -->
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>

    <!-- Trigger Input Bar -->
    <div
      @click="toggle"
      class="w-full px-4 py-3 min-h-[48px] text-sm rounded-xl border transition-all duration-300 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl text-slate-800 dark:text-slate-100 flex items-center justify-between cursor-pointer shadow-sm border-white/30 dark:border-white/10"
      :class="[
        isOpen ? 'border-indigo-500 ring-4 ring-indigo-500/20' : '',
        error ? 'border-rose-500' : ''
      ]"
    >
      <!-- Selected Display Value -->
      <div class="flex flex-wrap gap-1.5 items-center flex-1 truncate">
        <!-- Multi Select Tags -->
        <template v-if="multiple && Array.isArray(modelValue) && modelValue.length > 0">
          <span
            v-for="val in modelValue"
            :key="val"
            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30"
          >
            {{ getOptionLabel(val) }}
            <button @click.stop="removeTag(val)" class="mr-1 hover:text-rose-500">✕</button>
          </span>
        </template>
        
        <!-- Single Select Display -->
        <template v-else-if="!multiple && modelValue">
          <span class="font-semibold">{{ getOptionLabel(modelValue) }}</span>
        </template>

        <!-- Placeholder -->
        <span v-else class="text-slate-400 dark:text-slate-500">{{ placeholder }}</span>
      </div>

      <!-- Chevron Icon -->
      <span class="mr-2 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': isOpen }">
        ▼
      </span>
    </div>

    <!-- Dropdown Menu Floating Panel -->
    <div
      v-if="isOpen"
      class="absolute z-50 mt-1 w-full rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-white/30 dark:border-white/10 shadow-2xl overflow-hidden max-h-60 overflow-y-auto p-1.5 space-y-1 animate-fadeIn"
    >
      <!-- Search inside dropdown if searchable -->
      <div v-if="searchable" class="p-1.5 mb-1">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="بحث..."
          class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 outline-none"
        />
      </div>

      <!-- Option Items -->
      <div
        v-for="option in filteredOptions"
        :key="getOptionValue(option)"
        @click="selectOption(option)"
        class="px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors duration-150 cursor-pointer flex items-center justify-between"
        :class="[
          isSelected(getOptionValue(option))
            ? 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-bold'
            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60'
        ]"
      >
        <span>{{ getOptionLabel(option) }}</span>
        <span v-if="isSelected(getOptionValue(option))" class="text-indigo-500 font-bold">✓</span>
      </div>

      <!-- Empty State -->
      <div v-if="filteredOptions.length === 0" class="px-4 py-3 text-xs text-center text-slate-400">
        لا توجد خيارات مطابقة
      </div>
    </div>

    <p v-if="error" class="text-xs text-rose-500 font-medium mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
/**
 * SpatialDropdown.vue - القائمة المنسدلة التفاعلية النمطية High-Fidelity Interactive Select (Single & Multi)
 */
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number, Array], default: null },
  options: { type: Array, required: true }, // Array of strings or objects { label, value }
  label: { type: String, default: '' },
  placeholder: { type: String, default: 'اختر خياراً...' },
  multiple: { type: Boolean, default: false },
  searchable: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownRef = ref(null)

const toggle = () => {
  isOpen.value = !isOpen.value
}

const getOptionValue = (option) => {
  return typeof option === 'object' ? option.value : option
}

const getOptionLabel = (option) => {
  if (typeof option === 'object') return option.label
  const found = props.options.find(o => (typeof o === 'object' ? o.value === option : o === option))
  return found ? (typeof found === 'object' ? found.label : found) : option
}

const isSelected = (value) => {
  if (props.multiple) {
    return Array.isArray(props.modelValue) && props.modelValue.includes(value)
  }
  return props.modelValue === value
}

const selectOption = (option) => {
  const val = getOptionValue(option)
  if (props.multiple) {
    const current = Array.isArray(props.modelValue) ? [...props.modelValue] : []
    const idx = current.indexOf(val)
    if (idx > -1) {
      current.splice(idx, 1)
    } else {
      current.push(val)
    }
    emit('update:modelValue', current)
    emit('change', current)
  } else {
    emit('update:modelValue', val)
    emit('change', val)
    isOpen.value = false
  }
}

const removeTag = (val) => {
  if (Array.isArray(props.modelValue)) {
    const updated = props.modelValue.filter(item => item !== val)
    emit('update:modelValue', updated)
    emit('change', updated)
  }
}

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options
  return props.options.filter(o => getOptionLabel(o).toLowerCase().includes(searchQuery.value.toLowerCase()))
})

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
