<template>
  <div
    class="relative space-y-1.5 w-full transition-all"
    :class="[isOpen ? 'z-[100]' : 'z-10']"
    ref="dropdownRef"
  >
    <!-- Label -->
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-white/80">
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>

    <!-- Trigger Input Bar (Spatial Interactive Select v3.0) -->
    <div
      @click="toggle"
      class="spatial-input spatial-dropdown-trigger w-full px-4 h-14 rounded-[20px] text-sm flex items-center justify-between cursor-pointer border transition-all duration-200"
      :class="[
        isOpen ? 'border-primary shadow-lg shadow-primary/20 ring-2 ring-primary/30' : '',
        error ? 'error' : ''
      ]"
    >
      <!-- Selected Display Value -->
      <div class="flex flex-wrap gap-1.5 items-center flex-1 truncate">
        <!-- Multi Select Tags -->
        <template v-if="multiple && Array.isArray(modelValue) && modelValue.length > 0">
          <span
            v-for="val in modelValue"
            :key="val"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-primary/20 text-primary border border-primary/30"
          >
            {{ getOptionLabel(val) }}
            <button type="button" @click.stop="removeTag(val)" class="hover:text-rose-500 font-bold">✕</button>
          </span>
        </template>
        
        <!-- Single Select Display (Supports 0 and false as valid values) -->
        <template v-else-if="!multiple && (modelValue !== null && modelValue !== undefined && modelValue !== '')">
          <span class="font-bold text-slate-900 dark:text-white">{{ getOptionLabel(modelValue) }}</span>
        </template>

        <!-- Placeholder -->
        <span v-else class="text-slate-400 dark:text-white/40 font-bold">{{ placeholder }}</span>
      </div>

      <!-- Chevron Arrow Icon (Fixed Dimensions v3.0) -->
      <svg
        class="spatial-dropdown-arrow text-primary transition-transform duration-300"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>

    <!-- Dropdown Menu Floating Panel (v3.0 high z-index & blur) -->
    <div
      v-if="isOpen"
      class="spatial-dropdown-menu absolute z-[999] mt-2 w-full rounded-[22px] shadow-2xl border border-black/10 dark:border-white/15 max-h-60 overflow-y-auto p-2 space-y-1 custom-scroll animate-spatial-in"
    >
      <!-- Search inside dropdown if searchable -->
      <div v-if="searchable" class="p-1.5 mb-1">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="بحث..."
          class="spatial-input h-10 rounded-[14px] px-3.5 text-xs font-bold w-full"
        />
      </div>

      <!-- Option Items -->
      <div
        v-for="option in filteredOptions"
        :key="getOptionValue(option)"
        @click="selectOption(option)"
        class="spatial-dropdown-item px-4 py-2.5 rounded-[14px] text-xs font-bold cursor-pointer flex items-center justify-between transition-all"
        :class="[
          isSelected(getOptionValue(option))
            ? 'bg-primary/20 text-primary font-black'
            : 'text-slate-700 dark:text-white/80 hover:bg-primary/10 hover:text-primary'
        ]"
      >
        <span>{{ getOptionLabel(option) }}</span>
        <span v-if="isSelected(getOptionValue(option))" class="text-primary font-black">✓</span>
      </div>

      <!-- Empty State -->
      <div v-if="filteredOptions.length === 0" class="px-4 py-3 text-xs text-center font-bold text-slate-400 dark:text-white/40">
        لا توجد خيارات مطابقة
      </div>
    </div>

    <p v-if="error" class="text-xs text-rose-500 font-bold mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
/**
 * SpatialDropdown.vue - دعم اختيار الأرقام الصفرية 0 والمنطق الشرطي
 */
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number, Boolean, Array], default: null },
  options: { type: Array, required: true },
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
  if (typeof option === 'object' && option !== null) return option.label
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
