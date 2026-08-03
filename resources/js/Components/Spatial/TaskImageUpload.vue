<template>
  <div class="space-y-2 w-full">
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
      {{ label }}
    </label>

    <!-- Upload Dropzone Area -->
    <div
      @click="triggerFileSelect"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all duration-300 backdrop-blur-xl bg-white/40 dark:bg-slate-900/40 relative overflow-hidden group"
      :class="[
        isDragging
          ? 'border-indigo-500 bg-indigo-500/10'
          : 'border-slate-300 dark:border-slate-700 hover:border-indigo-400'
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        class="hidden"
        @change="handleFileSelect"
      />

      <!-- Preview Image -->
      <div v-if="previewUrl" class="relative group/preview">
        <img :src="previewUrl" class="max-h-48 mx-auto rounded-xl shadow-lg object-cover" />
        <button
          @click.stop="removeImage"
          class="absolute top-2 right-2 w-8 h-8 rounded-full bg-rose-500 text-white font-bold text-xs shadow-lg opacity-0 group-hover/preview:opacity-100 transition-opacity"
        >
          ✕
        </button>
      </div>

      <!-- Default Placeholder -->
      <div v-else class="space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 mx-auto flex items-center justify-center text-2xl">
          📸
        </div>
        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">اسحب وانسكب الصورة هنا أو اضغط للرفع</p>
        <p class="text-[10px] text-slate-400">يدعم صيغ JPG, PNG (حد أقصى 5 ميجابايت)</p>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * TaskImageUpload.vue - مكون رفع الصور التفاعلي بالمعاينة المباشرة للمهام الميدانية
 */
import { ref } from 'vue'

defineProps({
  label: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const fileInput = ref(null)
const previewUrl = ref(null)
const isDragging = ref(false)

const triggerFileSelect = () => fileInput.value?.click()

const processFile = (file) => {
  if (file && file.type.startsWith('image/')) {
    previewUrl.value = URL.createObjectURL(file)
    emit('update:modelValue', file)
    emit('change', file)
  }
}

const handleFileSelect = (e) => {
  const file = e.target.files[0]
  processFile(file)
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  processFile(file)
}

const removeImage = () => {
  previewUrl.value = null
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', null)
  emit('change', null)
}
</script>
