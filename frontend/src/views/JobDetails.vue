<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Motion } from "@motionone/vue"
import { fetchJobById } from '../api/jobs'

const route = useRoute()
const router = useRouter()

const job = ref(null)
const loading = ref(false)
const error = ref('')

const backQuery = computed(() => {
  const query = { ...route.query }
  delete query.job_id
  return query
})

async function load() {
  loading.value = true
  error.value = ''
  try {
    job.value = await fetchJobById(route.params.id)
  } catch (e) {
    error.value = 'Job not found.'
    job.value = null
  } finally {
    loading.value = false
  }
}

function back() {
  router.push({ name: 'jobs', query: backQuery.value })
}

watch(() => route.params.id, () => load())
onMounted(load)
</script>

<template>
  <div class="min-h-screen bg-transparent py-16 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-12">
      
      <Motion 
        :initial="{ opacity: 0, y: -20 }"
        :animate="{ opacity: 1, y: 0 }"
      >
        <div class="glass-card rounded-[3.5rem] p-10 md:p-14 border border-white/20 shadow-2xl relative overflow-hidden">
          <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="flex flex-col md:flex-row items-center gap-10">
              
              <div class="w-24 h-24 rounded-[2rem] bg-indigo-600 flex items-center justify-center text-4xl font-black text-white shadow-xl overflow-hidden border border-white/10">
                <img 
                  v-if="job?.company_logo" 
                  :src="job.company_logo" 
                  class="w-full h-full object-cover"
                  alt="Company Logo"
                />
                <span v-else>{{ job?.companyName?.[0] || 'J' }}</span>
              </div>
              <div class="text-center md:text-left text-white">
                <nav class="flex items-center justify-center md:justify-start gap-4 mb-3">
                  <button @click="back" class="text-xs font-black text-indigo-300 uppercase tracking-[0.3em] hover:text-white transition-all">← Back to Portal</button>
                  <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
                  <span class="text-xs font-black text-white/60 uppercase tracking-[0.3em]">{{ job?.category?.name || 'Loading' }}</span>
                </nav>
                <h1 class="text-5xl md:text-6xl font-[1000] tracking-tighter leading-[1.1]">
                  {{ job?.title ?? 'Job Title' }}
                </h1>
                <p class="mt-2 text-xl font-bold text-white/50">{{ job?.companyName }}</p>
              </div>
            </div>

            <div class="flex items-center gap-6">
              <button @click="back" class="px-10 py-5 rounded-2xl border border-white/10 bg-white/5 text-white font-black text-sm uppercase tracking-widest hover:bg-white/10 transition-all">
                Dismiss
              </button>
              <button class="px-14 py-5 rounded-2xl bg-indigo-600 text-white font-[1000] text-sm uppercase tracking-[0.2em] shadow-xl hover:bg-indigo-500 transition-all transform hover:-translate-y-1">
                Apply Now
              </button>
            </div>
          </div>
        </div>
      </Motion>

      <div class="grid grid-cols-1 lg:grid-cols-[1fr,420px] gap-12">
        
        <Motion 
          :initial="{ opacity: 0, x: -20 }"
          :animate="{ opacity: 1, x: 0 }"
          :transition="{ delay: 0.2 }"
        >
          <div class="glass-card rounded-[4rem] p-12 md:p-16 border border-white/10 shadow-xl space-y-20">
            <div v-if="job" class="space-y-24">
              <div class="relative">
                <h2 class="text-3xl font-black text-white mb-8 flex items-center gap-6">
                  <span class="w-12 h-2 bg-indigo-500 rounded-full"></span>
                  Overview
                </h2>
                <p class="text-2xl leading-[1.8] text-white font-bold opacity-95">
                  {{ job.description }}
                </p>
              </div>

              <div v-for="(val, key) in { Responsibilities: job.responsibilities, Requirements: job.requirements }" :key="key">
                <div v-if="val" class="pt-12 border-t border-white/10">
                  <h3 class="text-3xl font-black text-white mb-10">{{ key }}</h3>
                  <div class="p-10 rounded-[3rem] bg-black/20 border border-white/5 backdrop-blur-md">
                    <p class="whitespace-pre-wrap text-xl text-white/80 font-black leading-relaxed tracking-tight">
                      {{ val }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Motion>

        <Motion 
          :initial="{ opacity: 0, x: 20 }"
          :animate="{ opacity: 1, x: 0 }"
          :transition="{ delay: 0.4 }"
        >
          <aside class="sticky top-12 space-y-10">
            <div class="glass-card p-12 rounded-[4rem] border border-white/10">
              <h3 class="text-2xl font-black text-white mb-12">Summary</h3>
              
              <div class="space-y-6">
                <div v-for="(val, label) in { 'Location': job?.location, 'Type': job?.workType, 'Experience': job?.experienceLevel }" :key="label" 
                     class="flex flex-col gap-2 p-6 rounded-3xl bg-white/5 border border-white/5">
                  <span class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em]">{{ label }}</span>
                  <span class="text-xl font-black text-white">{{ val || 'N/A' }}</span>
                </div>

                <div class="mt-12 p-10 rounded-[3rem] bg-indigo-600 shadow-2xl text-center border border-white/10">
                  <p class="text-xs font-black uppercase tracking-[0.4em] text-indigo-100 mb-4">Salary Range</p>
                  <p class="text-3xl font-[1000] text-white tracking-tighter">
                    {{ job?.salaryMin }} - {{ job?.salaryMax }}
                  </p>
                </div>
              </div>
            </div>
          </aside>
        </Motion>
      </div>
    </div>
  </div>
</template>

<style scoped>
.glass-card {
  background: rgba(20, 20, 30, 0.45); 
  backdrop-filter: blur(30px) saturate(180%);
  -webkit-backdrop-filter: blur(30px) saturate(180%);
}

h1, h2, h3, p, span {
  text-shadow: none !important;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.whitespace-pre-wrap {
  word-break: break-word;
}
</style>