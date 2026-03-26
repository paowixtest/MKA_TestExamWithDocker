<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="mb-4 text-center">Login</h2>

            <transition name="fade">
              <div v-if="error" class="alert alert-danger">
                {{ error }}
              </div>
            </transition>

            <form @submit.prevent="login">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control" />
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control" />
              </div>

              <button type="submit" class="btn btn-primary w-100">
                Login
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from '../axios'
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  email: 'admin@test.com',
  password: 'password123'
})

const error = ref('')

async function login() {
  error.value = ''

  try {
    const response = await axios.post('/auth/login', form)

    localStorage.setItem('token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`

    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  }
  finally {
    setTimeout(() => {
      error.value = ''
    }, 2000)
  }
}
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