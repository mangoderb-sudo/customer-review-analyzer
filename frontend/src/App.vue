<template>
  <div id="app">
    <nav v-if="isLoggedIn">
      <router-link to="/dashboard">Dashboard</router-link>
      <router-link to="/reviews">Avis</router-link>
      <router-link to="/reviews/create">Nouvel avis</router-link>
      <button @click="logout">Déconnexion</button>
    </nav>
    <router-view />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import api from './api/axios'

const router = useRouter()

const isLoggedIn = computed(() => !!localStorage.getItem('token'))

async function logout() {
  try {
    await api.post('/logout')
  } catch (e) {
    // ignore
  }
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: sans-serif;
  background: #f5f5f5;
  color: #222;
}

#app {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
}

nav {
  display: flex;
  gap: 16px;
  align-items: center;
  padding: 12px 0;
  margin-bottom: 24px;
  border-bottom: 1px solid #ddd;
}

nav a {
  text-decoration: none;
  color: #444;
  font-size: 14px;
}

nav a.router-link-active {
  color: #2563eb;
  font-weight: 600;
}

nav button {
  margin-left: auto;
  padding: 6px 14px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

nav button:hover {
  background: #dc2626;
}

h1, h2 {
  margin-bottom: 20px;
  font-size: 22px;
}

.card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 16px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 6px;
  color: #555;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  background: #fafafa;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.btn {
  padding: 8px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.error {
  color: #dc2626;
  font-size: 13px;
  margin-top: 8px;
}

.badge {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.badge-positive { background: #dcfce7; color: #16a34a; }
.badge-negative { background: #fee2e2; color: #dc2626; }
.badge-neutral  { background: #f3f4f6; color: #6b7280; }
</style>
