<template>
  <div>
    <h1>Soumettre un avis</h1>

    <div class="card">
      <form @submit.prevent="submit">
        <div class="form-group">
          <label>Contenu de l'avis</label>
          <textarea
            v-model="form.content"
            placeholder="Décrivez votre expérience…"
            rows="5"
            required
          ></textarea>
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <div style="display:flex; gap:12px; align-items:center">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Envoi…' : 'Envoyer' }}
          </button>
          <router-link to="/reviews" style="font-size:13px; color:#888">Annuler</router-link>
        </div>
      </form>
    </div>

    <div v-if="result" class="card result-card">
      <h2>Résultat de l'analyse IA</h2>
      <div class="result-row">
        <span class="label">Sentiment</span>
        <span class="badge" :class="sentimentClass(result.sentiment)">{{ result.sentiment }}</span>
      </div>
      <div class="result-row">
        <span class="label">Score</span>
        <span class="score-value">{{ result.score }}/100</span>
      </div>
      <div class="result-row" v-if="result.topics && result.topics.length">
        <span class="label">Thèmes</span>
        <div class="topics">
          <span v-for="t in result.topics" :key="t" class="topic-tag">{{ t }}</span>
        </div>
      </div>
      <router-link to="/reviews" class="btn btn-primary" style="margin-top:16px; display:inline-block">
        Voir tous les avis
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import api from '../api/axios'

const form = reactive({ content: '' })
const loading = ref(false)
const error = ref('')
const result = ref(null)

async function submit() {
  loading.value = true
  error.value = ''
  result.value = null
  try {
    const { data } = await api.post('/reviews', { content: form.content })
    result.value = {
      sentiment: data.sentiment,
      score: data.score,
      topics: data.topics,
    }
    form.content = ''
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de l\'envoi.'
  } finally {
    loading.value = false
  }
}

function sentimentClass(s) {
  if (s === 'positive') return 'badge-positive'
  if (s === 'negative') return 'badge-negative'
  return 'badge-neutral'
}
</script>

<style scoped>
.result-card {
  background: #f0fdf4;
  border-color: #bbf7d0;
}

.result-card h2 {
  font-size: 16px;
  margin-bottom: 16px;
  color: #166534;
}

.result-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}

.label {
  font-size: 13px;
  font-weight: 600;
  color: #555;
  width: 80px;
}

.score-value {
  font-size: 18px;
  font-weight: 700;
  color: #1e3a5f;
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

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
