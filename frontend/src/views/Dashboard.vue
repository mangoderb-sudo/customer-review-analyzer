<template>
  <div>
    <h1>Tableau de bord</h1>

    <p v-if="loading">Chargement…</p>
    <p v-else-if="error" class="error">{{ error }}</p>

    <div v-else>
      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-label">Avis positifs</div>
          <div class="stat-value positive">{{ stats.positive_percentage ?? '—' }}%</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Avis négatifs</div>
          <div class="stat-value negative">{{ stats.negative_percentage ?? '—' }}%</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Score moyen</div>
          <div class="stat-value">{{ stats.average_score ?? '—' }}<span style="font-size:14px">/100</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total avis</div>
          <div class="stat-value">{{ stats.total_reviews ?? '—' }}</div>
        </div>
      </div>

      <!-- Top thèmes -->
      <div class="card" v-if="stats.top_topics && stats.top_topics.length">
        <h2>Top 3 thèmes détectés</h2>
        <div class="topics-list">
          <div v-for="(topic, i) in stats.top_topics.slice(0, 3)" :key="i" class="topic-item">
            <span class="topic-rank">{{ i + 1 }}</span>
            <span class="topic-name">{{ topic.topic ?? topic }}</span>
            <span v-if="topic.count" class="topic-count">{{ topic.count }} avis</span>
          </div>
        </div>
      </div>

      <!-- Avis récents -->
      <div class="card" v-if="stats.recent_reviews && stats.recent_reviews.length">
        <h2>Avis récents</h2>
        <div v-for="review in stats.recent_reviews" :key="review.id" class="recent-review">
          <span class="badge" :class="sentimentClass(review.sentiment)">{{ review.sentiment || '—' }}</span>
          <p class="review-text">{{ review.content }}</p>
          <span class="date">{{ formatDate(review.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'

const stats = ref({})
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/dashboard')
    stats.value = data
  } catch (e) {
    error.value = e.response?.data?.message || 'Impossible de charger le dashboard.'
  } finally {
    loading.value = false
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
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
}

.stat-label {
  font-size: 12px;
  color: #888;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #1e3a5f;
}

.stat-value.positive { color: #16a34a; }
.stat-value.negative { color: #dc2626; }

.topics-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.topic-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.topic-rank {
  width: 24px;
  height: 24px;
  background: #2563eb;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
}

.topic-name {
  font-size: 14px;
  font-weight: 600;
  flex: 1;
}

.topic-count {
  font-size: 12px;
  color: #888;
}

.recent-review {
  border-bottom: 1px solid #f3f4f6;
  padding: 12px 0;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: flex-start;
}

.recent-review:last-child {
  border-bottom: none;
}

.review-text {
  flex: 1 1 100%;
  font-size: 13px;
  color: #444;
  line-height: 1.5;
}

.date {
  font-size: 11px;
  color: #aaa;
}
</style>
