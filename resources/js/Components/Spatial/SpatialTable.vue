<template>
  <div class="space-y-4 w-full">
    <!-- Top Action Bar (Search & Filter) -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="w-full md:w-80">
        <SpatialInput
          v-model="searchQuery"
          placeholder="بحث في الجدول..."
          prefixIcon="🔍"
        />
      </div>

      <!-- Bulk Actions Bar -->
      <div v-if="selectedRows.length > 0" class="flex items-center space-x-3 space-x-reverse animate-fadeIn">
        <span class="text-xs font-bold text-indigo-500 bg-indigo-500/10 px-3 py-1.5 rounded-xl border border-indigo-500/20">
          تم تحديد {{ selectedRows.length }} عنصر
        </span>
        <slot name="bulk-actions" :selected="selectedRows" />
      </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl shadow-lg">
      <table class="w-full text-right text-sm">
        <thead class="bg-slate-100/50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase border-b border-slate-200/50 dark:border-slate-800/50">
          <tr>
            <th v-if="selectable" class="p-4 w-12 text-center">
              <SpatialCheckbox
                :modelValue="isAllSelected"
                @update:modelValue="toggleSelectAll"
              />
            </th>
            <th
              v-for="col in columns"
              :key="col.key"
              @click="col.sortable ? sortBy(col.key) : null"
              class="p-4 transition-colors"
              :class="[col.sortable ? 'cursor-pointer hover:text-indigo-500' : '']"
            >
              <div class="flex items-center space-x-1 space-x-reverse">
                <span>{{ col.label }}</span>
                <span v-if="col.sortable && sortColumn === col.key" class="text-xs">
                  {{ sortDirection === 'asc' ? '▲' : '▼' }}
                </span>
              </div>
            </th>
            <th v-if="$slots.actions" class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200/30 dark:divide-slate-800/30 text-slate-800 dark:text-slate-200">
          <tr
            v-for="(row, idx) in paginatedData"
            :key="row.id || idx"
            class="transition-colors hover:bg-indigo-500/5 dark:hover:bg-indigo-500/10"
            :class="[isRowSelected(row) ? 'bg-indigo-500/10 dark:bg-indigo-500/20' : '']"
          >
            <td v-if="selectable" class="p-4 text-center">
              <SpatialCheckbox
                :modelValue="isRowSelected(row)"
                @update:modelValue="toggleSelectRow(row)"
              />
            </td>
            <td v-for="col in columns" :key="col.key" class="p-4">
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ row[col.key] }}
              </slot>
            </td>
            <td v-if="$slots.actions" class="p-4 text-center">
              <slot name="actions" :row="row" />
            </td>
          </tr>

          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)" class="p-8 text-center">
              <SpatialEmptyState title="لا توجد بيانات متاحة" message="لم يتم العثور على سجلات مطابقة للبحث الحالي." />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div v-if="totalPages > 1" class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
      <span>عرض الصفحة {{ currentPage }} من {{ totalPages }}</span>
      <div class="flex items-center space-x-2 space-x-reverse">
        <SpatialButton
          size="sm"
          variant="secondary"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          السابق
        </SpatialButton>
        <SpatialButton
          size="sm"
          variant="secondary"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          التالي
        </SpatialButton>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialTable.vue - جدول البيانات المتقدم مع البحث، الترتيب، التحديد المتعدد والصفحات
 */
import { ref, computed } from 'vue'
import SpatialInput from './SpatialInput.vue'
import SpatialCheckbox from './SpatialCheckbox.vue'
import SpatialButton from './SpatialButton.vue'
import SpatialEmptyState from './SpatialEmptyState.vue'

const props = defineProps({
  columns: { type: Array, required: true }, // [{ key, label, sortable }]
  data: { type: Array, required: true },
  selectable: { type: Boolean, default: false },
  perPage: { type: Number, default: 10 }
})

const searchQuery = ref('')
const sortColumn = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const selectedRows = ref([])

const filteredData = computed(() => {
  let result = [...props.data]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(row =>
      Object.values(row).some(val => String(val).toLowerCase().includes(query))
    )
  }

  if (sortColumn.value) {
    result.sort((a, b) => {
      const valA = a[sortColumn.value]
      const valB = b[sortColumn.value]
      if (valA < valB) return sortDirection.value === 'asc' ? -1 : 1
      if (valA > valB) return sortDirection.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return result
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / props.perPage))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.perPage
  return filteredData.value.slice(start, start + props.perPage)
})

const sortBy = (key) => {
  if (sortColumn.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = key
    sortDirection.value = 'asc'
  }
}

const isRowSelected = (row) => selectedRows.value.includes(row.id || row)
const toggleSelectRow = (row) => {
  const id = row.id || row
  const idx = selectedRows.value.indexOf(id)
  if (idx > -1) selectedRows.value.splice(idx, 1)
  else selectedRows.value.push(id)
}

const isAllSelected = computed(() => {
  return paginatedData.value.length > 0 && paginatedData.value.every(r => isRowSelected(r))
})

const toggleSelectAll = (val) => {
  if (val) {
    paginatedData.value.forEach(r => {
      const id = r.id || r
      if (!selectedRows.value.includes(id)) selectedRows.value.push(id)
    })
  } else {
    selectedRows.value = []
  }
}
</script>
