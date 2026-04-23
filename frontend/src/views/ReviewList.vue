<template>
  <div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
      <h1>Avis clients</h1>
      <router-link to="/reviews/create" class="btn btn-primary">+ Nouvel avis</router-link>
    </div>

    <p v-if="loading">Chargement…</p>
    <p v-else-if="error" class="error">{{ error }}</p>
    <p v-else-if="reviews.length === 0" style="color:#888">Aucun avis pour l'instant.</p>

    <div v-for="review in reviews" :key="review.id" class="card review-card">
      <div class="review-header">
        <span class="badge" :class="sentimentClass(review.sentiment)">
          {{ review.sentiment || '—' }}
        </span>
        <span class="score" v-if="review.score !== null">Score : {{ review.score }}/100</span>
        <span class="date">{{ formatDate(review.created_at) }}</span>
        <button class="btn btn-danger btn-sm" @click="deleteReview(review.id)">Supprimer</button>
      </div>

      <p class="review-content">{{ review.content }}</p>

      <div v-if="review.topics && review.topics.length" class="topics">
        <span v-for="topic in review.topics" :key="topic" class="topic-tag">{{ topic }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'

const reviews = ref([])
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/reviews')
    reviews.value = data.data ?? data
  } catch (e) {
    error.value = e.response?.data?.message || 'Impossible de charger les avis.'
  } finally {
    loading.value = false
  }
}

async function deleteReview(id) {
  if (!confirm('Supprimer cet avis ?')) return
  try {
    await api.delete(`/reviews/${id}`)
    reviews.value = reviews.value.filter(r => r.id !== id)
  } catch (e) {
    alert('Erreur lors de la suppression.')
  }
}

function sentimentClass(s) {
  if (s === 'positive') return 'badge-positive'
  if (s === 'negative') return 'badge-negative'
  return 'badge-neutral'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(load)
</script>

<style scoped>
.review-card {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.score {
  font-size: 13px;
  color: #555;
}

.date {
  font-size: 12px;
  color: #aaa;
  margin-left: auto;
}

.btn-sm {
  padding: 4px 10px;
  font-size: 12px;
}

.review-content {
  font-size: 14px;
  line-height: 1.6;
  color: #333;
}

.topics {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.topic-tag {
  background: #eff6ff;
  color: #2563eb;
  border-radius: 4px;
  padding: 2px 8px;
  font-size: 12px;
}
</style>
