<template>
  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-6xl mx-auto space-y-10">
      
      <!-- Header -->
      <header class="card-ui p-10 md:p-14">
        <div class="space-y-4">
          <p class="text-xs font-black uppercase tracking-[0.5em] text-white/30">Candidate Workspace</p>
          <h1 class="text-5xl md:text-6xl font-[1000] tracking-tighter text-white">My Applications</h1>
          <p class="text-white/55 text-lg max-w-3xl leading-relaxed">
            Track all your job applications, view their status, and manage your submissions.
          </p>
        </div>
      </header>

      <!-- Content Section -->
      <section class="card-ui p-10 md:p-14 space-y-8">
        
        <!-- Loading State -->
        <div v-if="loading" class="py-20 text-center">
          <p class="text-white/60 text-lg">⏳ Loading your applications...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="applications.length === 0" class="py-20 text-center">
          <p class="text-3xl md:text-4xl font-black text-white">No Applications Yet</p>
          <p class="text-white/40 mt-4 text-lg">
            Start applying to jobs to track your progress here.
          </p>
          <RouterLink to="/jobs" class="inline-block mt-6 btn-ui btn-primary-ui">
            Browse Jobs
          </RouterLink>
        </div>

        <!-- Applications List -->
        <div v-else class="grid gap-6">
          <article
            v-for="app in applications"
            :key="app.id"
            class="rounded-[2.5rem] border border-white/10 bg-black/30 backdrop-blur-2xl p-8 md:p-10 shadow-2xl hover:bg-black/40 transition-all"
          >
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
              
              <!-- Job Details -->
              <div class="space-y-5 flex-1">
                
                <!-- Status & Date -->
                <div class="flex flex-wrap items-center gap-3">
                  <ApplicationStatusBadge :status="app.status || 'pending'" />
                  <span class="text-xs font-bold text-white/40 uppercase tracking-[0.2em]">
                    {{ formatDate(app.created_at) }}
                  </span>
                </div>

                <!-- Job Title & Company -->
                <div>
                  <h2 class="text-3xl font-black tracking-tight text-white">
                    {{ app.job?.title || app.job_title || 'Untitled Job' }}
                  </h2>
                  <p class="mt-2 text-white/50 text-lg font-medium">
                    {{ app.job?.company_name || app.job?.companyName || 'Unknown Company' }}
                  </p>
                </div>

                <!-- Cover Letter -->
                <div v-if="app.cover_letter" class="p-5 rounded-[1.75rem] bg-white/5 border border-white/5">
                  <p class="text-[11px] font-black uppercase tracking-[0.4em] text-white/25">Cover Letter</p>
                  <p class="mt-3 text-white/75 leading-7 whitespace-pre-wrap line-clamp-3">
                    {{ app.cover_letter }}
                  </p>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex flex-col gap-3 lg:min-w-[200px]">
                <!-- View CV Button -->
                <a
                  v-if="app.resume_url"
                  :href="app.resume_url"
                  target="_blank"
                  class="px-6 py-3 rounded-2xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-200 font-bold uppercase tracking-[0.2em] text-center hover:bg-indigo-500/20 transition-all"
                >
                  📄 View CV
                </a>

                <!-- View Resume Path (fallback) -->
                <div v-else-if="app.resume_path" class="px-6 py-3 rounded-2xl border border-white/10 bg-white/5 text-white/70 font-bold uppercase tracking-[0.2em] text-center text-sm">
                  {{ app.resume_path }}
                </div>

                <!-- Cancel Button -->
                <button
                  @click="cancelApplication(app.id)"
                  :disabled="cancelingIds.includes(app.id)"
                  class="px-6 py-3 rounded-2xl border border-red-500/30 bg-red-500/10 text-red-200 font-bold uppercase tracking-[0.2em] hover:bg-red-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ cancelingIds.includes(app.id) ? '⏳ Cancelling...' : '🗑️ Cancel' }}
                </button>
              </div>
            </div>
          </article>
        </div>

        <!-- Error State -->
        <div v-if="error" class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-200 font-bold">
          ❌ {{ error }}
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { http } from '../api/http'
import ApplicationStatusBadge from '../components/ApplicationStatusBadge.vue'

const applications = ref([])
const loading = ref(false)
const error = ref('')
const cancelingIds = ref([])

const fetchApplications = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await http.get('/applications')
    applications.value = res.data?.data ?? res.data ?? []
  } catch (err) {
    const msg = err?.response?.data?.message || err.message || 'Failed to load applications'
    error.value = msg
    applications.value = []
  } finally {
    loading.value = false
  }
}

const cancelApplication = async (id) => {
  if (!confirm('Are you sure you want to cancel this application? This action cannot be undone.')) {
    return
  }

  if (cancelingIds.value.includes(id)) return

  cancelingIds.value.push(id)

  try {
    await http.delete(`/applications/${id}`)
    applications.value = applications.value.filter(a => a.id !== id)
  } catch (err) {
    const msg = err?.response?.data?.message || err.message || 'Failed to cancel'
    error.value = msg
  } finally {
    const idx = cancelingIds.value.indexOf(id)
    if (idx !== -1) cancelingIds.value.splice(idx, 1)
  }
}

const formatDate = (iso) => {
  if (!iso) return '-'
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return iso
  }
}

onMounted(fetchApplications)
</script>
