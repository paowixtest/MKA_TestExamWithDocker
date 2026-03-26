<template>
  <div class="container py-4">
    <h1>Audit Logs</h1>

    <div class="d-flex gap-2 mb-3">
      <button class="btn btn-secondary" @click="goDashboard">
        Back to Dashboard
      </button>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Search by ID or User</label>
        <input
          v-model="searchTerm"
          type="text"
          class="form-control"
          placeholder="Enter log ID or user name"
        />
      </div>
    </div>

    <transition name="fade">
      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>
    </transition>

    <div v-if="paginatedLogs.length">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Event</th>
            <th>User</th>
            <th>Session</th>
            <th>Auditable Type</th>
            <th>Auditable ID</th>
            <th>Occurred At</th>
            <th>Hash</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in paginatedLogs" :key="log.id">
            <td>{{ log.id }}</td>
            <td>{{ log.event_type }}</td>
            <td>{{ log.user?.name || 'N/A' }}</td>
            <td>{{ log.session_id }}</td>
            <td>{{ log.auditable_type }}</td>
            <td>{{ log.auditable_id }}</td>
            <td>{{ log.occurred_at }}</td>
            <td style="max-width: 220px; word-break: break-all;">
              {{ log.current_hash }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <button
          class="btn btn-outline-primary"
          @click="prevPage"
          :disabled="currentPage === 1"
        >
          Previous
        </button>

        <span>
          Page {{ currentPage }} of {{ totalPages }}
        </span>

        <button
          class="btn btn-outline-primary"
          @click="nextPage"
          :disabled="currentPage === totalPages"
        >
          Next
        </button>
      </div>
    </div>

    <div v-else class="alert alert-info">
      No audit logs found.
    </div>
  </div>
</template>

<script setup>
import axios from '../axios'
import { onMounted, ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'

const logs = ref([])
const error = ref('')
const searchTerm = ref('')
const router = useRouter()

const currentPage = ref(1)
const perPage = 5

function goDashboard() {
  router.push('/dashboard')
}

async function loadLogs() {
  try {
    const response = await axios.get('/audit-logs')
    logs.value = response.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load audit logs'
  } finally {
    setTimeout(() => {
      error.value = ''
    }, 2000)
  }
}

const filteredLogs = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()

  if (!term) {
    return logs.value
  }

  return logs.value.filter(log => {
    const idMatch = String(log.id).includes(term)
    const userMatch = (log.user?.name || '').toLowerCase().includes(term)

    return idMatch || userMatch
  })
})

const totalPages = computed(() => {
  return Math.ceil(filteredLogs.value.length / perPage) || 1
})

const paginatedLogs = computed(() => {
  const start = (currentPage.value - 1) * perPage
  const end = start + perPage
  return filteredLogs.value.slice(start, end)
})

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

watch(searchTerm, () => {
  currentPage.value = 1
})

onMounted(loadLogs)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>