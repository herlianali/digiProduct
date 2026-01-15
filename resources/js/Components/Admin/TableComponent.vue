<!--
|--------------------------------------------------------------------------
| DATA TABLE COMPONENT
|--------------------------------------------------------------------------
|
| FEATURES:
| - Search (global, all pages)
| - Sorting (asc / desc)
| - Pagination
|
| PROPS:
| - columns : [{ key, label, sortable }]
| - rows    : Array of data
| - perPage : Number (default 10)
|
|--------------------------------------------------------------------------
-->

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
            :key="index"
            class="border-t hover:bg-gray-50"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              class="px-4 py-3"
            >
              <slot :name="col.key" :row="row">
                {{ row[col.key] }}
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

/*
|--------------------------------------------------------------------------
| FILTER (SEARCH)
|--------------------------------------------------------------------------
| Search dilakukan ke SELURUH DATA (semua halaman)
*/
const filteredData = computed(() => {
  const data = props.rows || []

  if (!search.value) return data

  return data.filter(row =>
    Object.values(row).some(value =>
      String(value).toLowerCase().includes(search.value.toLowerCase())
    )
  )
})


/*
|--------------------------------------------------------------------------
| SORTING
|--------------------------------------------------------------------------
*/
const sortedData = computed(() => {
  if (!sortKey.value) return filteredData.value

  return [...filteredData.value].sort((a, b) => {
    const valA = a[sortKey.value]
    const valB = b[sortKey.value]

    if (valA < valB) return sortDirection.value === 'asc' ? -1 : 1
    if (valA > valB) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })
})

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/
const totalPages = computed(() =>
  Math.ceil(sortedData.value.length / props.perPage)
)

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.perPage
  return sortedData.value.slice(start, start + props.perPage)
})

/*
|--------------------------------------------------------------------------
| METHODS
|--------------------------------------------------------------------------
*/
function sort(col) {
  if (!col.sortable) return

  if (sortKey.value === col.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = col.key
    sortDirection.value = 'asc'
  }
}

/*
|--------------------------------------------------------------------------
| WATCH
|--------------------------------------------------------------------------
| Reset page jika search berubah
*/
watch(search, () => {
  currentPage.value = 1
})
</script>
