<template>
  <div class="space-y-4">
    <!-- Search -->
    <div class="flex justify-between items-center">
      <input
        v-model="search"
        type="text"
        placeholder="Search..."
        class="border rounded-lg px-3 py-2 text-sm w-64 focus:ring focus:ring-indigo-200"
      />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto border rounded-xl">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 cursor-pointer select-none"
              @click="sort(col)"
            >
              <div class="flex items-center gap-1">
                {{ col.label }}

                <!-- Sorting icon -->
                <span v-if="col.sortable">
                  <span v-if="sortKey === col.key">
                    {{ sortDirection === 'asc' ? '▲' : '▼' }}
                  </span>
                  <span v-else class="text-gray-400">⇅</span>
                </span>
              </div>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, index) in paginatedData"
            :key="row?.id || index"
            class="border-t hover:bg-gray-50"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3"
            >
              <slot :name="col.key" :row="row">
                {{ getSafeValue(row, col.key) }}
              </slot>
            </td>
          </tr>

          <tr v-if="paginatedData.length === 0">
            <td
              :colspan="columns.length"
              class="text-center py-6 text-gray-500"
            >
              No data found
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center text-sm">
      <p class="text-gray-600">
        Page {{ currentPage }} of {{ totalPages }}
      </p>

      <div class="flex gap-2">
        <button
          class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          Prev
        </button>

        <button
          class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        default: () => [],
    },
    perPage: {
        type: Number,
        default: 10,
    },
})

const search = ref('')
const currentPage = ref(1)
const sortKey = ref('')
const sortDirection = ref('asc')

// Helper function untuk mendapatkan value dengan aman
const getSafeValue = (row, key) => {
  if (!row || typeof row !== 'object') return '-'
  const value = row[key]
  return value !== null && value !== undefined ? value : '-'
}

// Filter dan validasi data
const validRows = computed(() => {
  const data = props.rows || []
  return data.filter(row => row && typeof row === 'object')
})

const filteredData = computed(() => {
  if (!search.value) return validRows.value

  return validRows.value.filter(row => {
    // Cek apakah row valid
    if (!row || typeof row !== 'object') return false
    
    // Cari di semua properti yang ada
    return Object.values(row).some(value => {
      if (value === null || value === undefined) return false
      return String(value).toLowerCase().includes(search.value.toLowerCase())
    })
  })
})

const sortedData = computed(() => {
  if (!sortKey.value) return filteredData.value

  return [...filteredData.value].sort((a, b) => {
    // Handle null/undefined values
    const valA = a[sortKey.value]
    const valB = b[sortKey.value]
    
    // Jika salah satu null, taruh di akhir
    if (valA === null || valA === undefined) return 1
    if (valB === null || valB === undefined) return -1
    
    // Compare values
    if (typeof valA === 'string' && typeof valB === 'string') {
      return sortDirection.value === 'asc' 
        ? valA.localeCompare(valB)
        : valB.localeCompare(valA)
    }
    
    // For numbers and other types
    if (valA < valB) return sortDirection.value === 'asc' ? -1 : 1
    if (valA > valB) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })
})

const totalPages = computed(() =>
  Math.ceil(sortedData.value.length / props.perPage)
)

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.perPage
  return sortedData.value.slice(start, start + props.perPage)
})

function sort(col) {
  if (!col.sortable) return

  if (sortKey.value === col.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = col.key
    sortDirection.value = 'asc'
  }
}

watch(search, () => {
  currentPage.value = 1
})

// Debug log untuk melihat data
watch(() => props.rows, (newRows) => {
  console.log('TableComponent received rows:', newRows)
  console.log('Valid rows count:', validRows.value.length)
  
  // Cek jika ada null/undefined
  if (newRows) {
    const nullRows = newRows.filter(row => row === null || row === undefined)
    if (nullRows.length > 0) {
      console.warn('Found null/undefined rows:', nullRows.length)
    }
  }
}, { immediate: true, deep: true })
</script>