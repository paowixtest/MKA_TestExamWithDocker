<template>
  <div class="container py-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="mb-3">Dashboard</h1>

        <p class="mb-2">Welcome, <strong>{{ user?.name }}</strong></p>
        <p class="mb-4">
          Role:
          <span class="badge bg-primary">{{ roleName }}</span>
        </p>

        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-success" @click="goIps">
            Go to IP Management
          </button>

          <button
            v-if="roleName === 'super-admin'"
            class="btn btn-warning"
            @click="goAudit"
          >
            Go to Audit Logs
          </button>

          <button class="btn btn-danger" @click="logout">
            Logout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from '../axios'
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = JSON.parse(localStorage.getItem('user'))

const roleName = computed(() => user?.roles?.[0]?.name || '')

function goIps() {
  router.push('/ips')
}

function goAudit() {
  router.push('/audit')
}

async function logout() {
  try {
    await axios.post('/auth/logout')
  } catch (e) {
  }

  localStorage.removeItem('token')
  localStorage.removeItem('user')
  delete axios.defaults.headers.common['Authorization']

  router.push('/login')
}
</script>