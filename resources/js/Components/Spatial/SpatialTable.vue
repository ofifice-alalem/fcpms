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
        <span class="text-xs font-black text-white px-3.5 py-1.5 rounded-full bg-primary shadow-md">
          تم تحديد {{ selectedRows.length }} عنصر
        </span>
        <slot name="bulk-actions" :selected="selectedRows" />
      </div>
    </div>

    <!-- Table Container (Spatial Card v3.0) -->
    <div class="spatial-card overflow-hidden rounded-[26px] p-0 shadow-xl border border-black/10 dark:border-white/10">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-sm">
          <thead class="bg-black/5 dark:bg-white/5 text-slate-500 dark:text-white/60 font-bold uppercase text-xs border-b border-black/10 dark:border-white/10">
            <tr>
              <th v-if="selectable" class="p-4 w-12 text-center">
                <input
                  type="checkbox"
                  class="row-checkbox cursor-pointer"
                  :checked="isAllSelected"
                  @change="toggleSelectAll($event.target.checked)"
                />
              </th>
              <th
                v-for="col in columns"
                :key="col.key"
                @click="col.sortable ? sortBy(col.key) : null"
                class="p-4 transition-colors"
                :class="[col.sortable ? 'cursor-pointer hover:text-primary' : '']"
              >
                <div class="flex items-center space-x-1 space-x-reverse font-black">
                  <span>{{ col.label }}</span>
                  <span v-if="col.sortable && sortColumn === col.key" class="text-xs text-primary">
                    {{ sortDirection === 'asc' ? '▲' : '▼' }}
                  </span>
                </div>
              </th>
              <th v-if="$slots.actions" class="p-4 text-center font-black">الإجراءات</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-black/5 dark:divide-white/5 text-slate-800 dark:text-white font-bold">
            <tr
              v-for="(row, idx) in paginatedData"
              :key="row.id || idx"
              class="transition-all hover:bg-primary/5 dark:hover:bg-primary/10"
              :class="[isRowSelected(row) ? 'selected-row' : '']"
            >
              <td v-if="selectable" class="p-4 text-center">
                <input
                  type="checkbox"
                  class="row-checkbox cursor-pointer"
                  :checked="isRowSelected(row)"
                  @change="toggleSelectRow(row)"
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
    </div>

    <!-- Pagination Footer -->
    <div v-if="totalPages > 1" class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-white/60 pt-2">
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
 * SpatialTable.vue - جدول البيانات الفضائي المطابق لـ Design System v3.0
 */
import { ref, computed } from 'vue'
import SpatialInput from './SpatialInput.vue'
import SpatialButton from './SpatialButton.vue'
import SpatialEmptyState from './SpatialEmptyState.vue'

const props = defineProps({
  columns: { type: Array, required: true },
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
