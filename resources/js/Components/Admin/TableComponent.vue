<template>
  <div class="space-y-4">
    <!-- Search -->
    <div class="flex justify-between items-center">
      <input
        v-model="internalSearch"
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
              class="px-4 py-3 cursor-pointer select-none whitespace-nowrap"
              @click="sort(col)"
            >
              <div class="flex items-center gap-1">
                {{ col.label }}
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
            :key="row?.id ?? index"
            class="border-t hover:bg-gray-50"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3 align-middle"
            >
              <!--
                Emit BOTH :row dan :value ke setiap slot.
                - :value  → nilai mentah dari row[col.key], untuk slot yang cukup pakai nilai saja
                - :row    → seluruh object row, untuk slot actions atau yang butuh multi-field
              -->
              <slot
                :name="col.key"
                :row="row"
                :value="getSafeValue(row, col.key)"
              >
                {{ getSafeValue(row, col.key) }}
              </slot>
            </td>
          </tr>

          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length" class="text-center py-6 text-gray-500">
              No data found
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center text-sm">
      <p class="text-gray-600">
        Page {{ currentPage }} of {{ totalPages || 1 }}
        <span class="text-gray-400 ml-2">({{ sortedData.length }} data)</span>
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
          :disabled="currentPage >= totalPages"
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
  columns:  { type: Array,  required: true },
  rows:     { type: Array,  default: () => [] },
  perPage:  { type: Number, default: 10 },
  search:   { type: String, default: '' },   // search dari luar (server-side)
})

const emit = defineEmits(['search'])

// ─── State ────────────────────────────────────────────────────────
const internalSearch  = ref(props.search ?? '')
const currentPage     = ref(1)
const sortKey         = ref('')
const sortDirection   = ref('asc')

// ─── Sync search dari luar ────────────────────────────────────────
watch(() => props.search, (val) => { internalSearch.value = val ?? '' })

// Emit ke parent saat user mengetik (debounce ringan)
let searchTimeout = null
watch(internalSearch, (val) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    emit('search', val)
    currentPage.value = 1
  }, 300)
})

// ─── getSafeValue — handle array, object, boolean, null ──────────
const getSafeValue = (row, key) => {
  if (!row || typeof row !== 'object') return '-'
  const val = row[key]
  if (val === null || val === undefined) return '-'
  // Array (scopes, timelines, tags) → jangan render mentah, biarkan slot tangani
  if (Array.isArray(val)) return val
  // Boolean → biarkan slot tangani (jangan convert ke string di sini)
  if (typeof val === 'boolean') return val
  return val
}

// ─── validRows ────────────────────────────────────────────────────
const validRows = computed(() =>
  (props.rows ?? []).filter(row => row && typeof row === 'object')
)

// ─── Filter (client-side, hanya jika tidak ada server search) ────
const filteredData = computed(() => {
  const q = internalSearch.value?.trim().toLowerCase()
  if (!q) return validRows.value

  return validRows.value.filter(row =>
    Object.values(row).some(val => {
      if (val === null || val === undefined) return false
      if (Array.isArray(val)) return val.some(v => String(v).toLowerCase().includes(q))
      return String(val).toLowerCase().includes(q)
    })
  )
})

// ─── Sort ─────────────────────────────────────────────────────────
const sortedData = computed(() => {
  if (!sortKey.value) return filteredData.value

  return [...filteredData.value].sort((a, b) => {
    const valA = a[sortKey.value]
    const valB = b[sortKey.value]

    if (valA === null || valA === undefined) return 1
    if (valB === null || valB === undefined) return -1

    // Array (scopes/timelines) → sort by length
    if (Array.isArray(valA) && Array.isArray(valB)) {
      return sortDirection.value === 'asc'
        ? valA.length - valB.length
        : valB.length - valA.length
    }

    if (typeof valA === 'string' && typeof valB === 'string') {
      return sortDirection.value === 'asc'
        ? valA.localeCompare(valB)
        : valB.localeCompare(valA)
    }

    return sortDirection.value === 'asc'
      ? (valA < valB ? -1 : valA > valB ? 1 : 0)
      : (valA > valB ? -1 : valA < valB ? 1 : 0)
  })
})

// ─── Pagination ───────────────────────────────────────────────────
const totalPages   = computed(() => Math.max(1, Math.ceil(sortedData.value.length / props.perPage)))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.perPage
  return sortedData.value.slice(start, start + props.perPage)
})

// Reset page saat data berubah
watch(() => props.rows, () => { currentPage.value = 1 })

// ─── Sort handler ─────────────────────────────────────────────────
const sort = (col) => {
  if (!col.sortable) return
  if (sortKey.value === col.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value      = col.key
    sortDirection.value = 'asc'
  }
}
</script>
