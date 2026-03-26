<template>
  <div style="padding:20px;">
    <h1>IP Address Management</h1>

    <button @click="goDashboard">Back to Dashboard</button>

    <hr />

    <h2>Add New IP</h2>
    <form @submit.prevent="createIp">
      <div style="margin-bottom:10px;">
        <label>IP Address</label>
        <input v-model="form.ip_address" type="text" style="width:100%; padding:8px;" />
      </div>

      <div style="margin-bottom:10px;">
        <label>Label</label>
        <input v-model="form.label" type="text" style="width:100%; padding:8px;" />
      </div>

      <div style="margin-bottom:10px;">
        <label>Comment</label>
        <textarea v-model="form.comment" style="width:100%; padding:8px;"></textarea>
      </div>

      <button type="submit">Create IP</button>
    </form>

    <p v-if="message" style="color:green;">{{ message }}</p>
    <p v-if="error" style="color:red;">{{ error }}</p>

    <hr />

    <h2>Recorded IPs</h2>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>IP Address</th>
          <th>Label</th>
          <th>Comment</th>
          <th>Created By</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ip in ips" :key="ip.id">
          <td>{{ ip.id }}</td>
          <td>{{ ip.ip_address }}</td>
          <td>
            <input v-model="ip.label" />
          </td>
          <td>
            <input v-model="ip.comment" />
          </td>
          <td>{{ ip.creator?.name }}</td>
          <td>
            <button @click="updateIp(ip)">Update</button>
            <button @click="deleteIp(ip.id)" style="margin-left:10px;">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import axios from '../axios'
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const ips = ref([])
const message = ref('')
const error = ref('')

const form = reactive({
  ip_address: '',
  label: '',
  comment: ''
})

function goDashboard() {
  router.push('/dashboard')
}

async function loadIps() {
  const response = await axios.get('/ips')
  ips.value = response.data
}

async function createIp() {
  message.value = ''
  error.value = ''

  try {
    await axios.post('/ips', form)
    form.ip_address = ''
    form.label = ''
    form.comment = ''
    message.value = 'IP created successfully'
    await loadIps()

  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create IP'
  }
  finally {
    setTimeout(() => {
      message.value = ''
      error.value = ''
    }, 2000)
  }
}

async function updateIp(ip) {
  message.value = ''
  error.value = ''

  try {
    await axios.put(`/ips/${ip.id}`, {
      label: ip.label,
      comment: ip.comment
    })
    message.value = 'IP updated successfully'
    await loadIps()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update IP'
  }
  finally {
    setTimeout(() => {
      message.value = ''
      error.value = ''
    }, 2000)
  }
}

async function deleteIp(id) {
  message.value = ''
  error.value = ''

  try {
    await axios.delete(`/ips/${id}`)
    message.value = 'IP deleted successfully'
    await loadIps()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete IP'
  }
  finally {
    setTimeout(() => {
      message.value = ''
      error.value = ''
    }, 2000)
  }
}

onMounted(loadIps)
</script>