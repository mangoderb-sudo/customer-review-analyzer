<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1>Customer Review Analyzer</h1>

      <div class="tabs">
        <button :class="{ active: mode === 'login' }" @click="mode = 'login'">Connexion</button>
        <button :class="{ active: mode === 'register' }" @click="mode = 'register'">Inscription</button>
      </div>

      <form @submit.prevent="submit">
        <div v-if="mode === 'register'" class="form-group">
          <label>Nom</label>
          <input v-model="form.name" type="text" placeholder="Votre nom" required />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" placeholder="email@exemple.com" required />
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input v-model="form.password" type="password" placeholder="••••••••" required />
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <button type="submit" class="btn btn-primary" style="width:100%">
          {{ mode === 'login' ? 'Se connecter' : "S'inscrire" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axios'

const router = useRouter()
const mode = ref('login')
const error = ref('')

const form = reactive({ name: '', email: '', password: '' })

async function submit() {
  error.value = ''
  try {
    const endpoint = mode.value === 'login' ? '/login' : '/register'
    const payload = mode.value === 'login'
      ? { email: form.email, password: form.password }
      : { name: form.name, email: form.email, password: form.password }

    const { data } = await api.post(endpoint, payload)
    localStorage.setItem('token', data.token)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue.'
  }
}
</script>

<style scoped>
.auth-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
}

.auth-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 32px;
  width: 100%;
  max-width: 420px;
}

h1 {
  font-size: 18px;
  text-align: center;
  margin-bottom: 24px;
  color: #1e3a5f;
}

.tabs {
  display: flex;
  margin-bottom: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.tabs button {
  flex: 1;
  padding: 10px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
  color: #888;
  border-bottom: 2px solid transparent;
}

.tabs button.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
  font-weight: 600;
}
</style>
