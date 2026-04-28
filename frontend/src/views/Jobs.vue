<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Motion } from "@motionone/vue"
import CategoryList from '../components/CategoryList.vue'
import FiltersPanel from '../components/FiltersPanel.vue'
import SearchBar from '../components/SearchBar.vue'
import { fetchCategories, fetchJobs } from '../api/jobs'

const route = useRoute()
const router = useRouter()

const categories = ref([])
const jobsResponse = ref(null)
const loading = ref(false)
const error = ref('')

const q = ref('')
const filters = ref({
  category_id: '',
  location: '',
  work_type: '',
  experience_level: '',
  salary_min: '',
  salary_max: '',
  sort: 'newest',
})

const jobs = computed(() => jobsResponse.value?.data ?? [])
const meta = computed(() => jobsResponse.value?.meta ?? null)

function syncLocalFromQuery(query) {
  q.value = query.q ? String(query.q) : ''
  filters.value = {
    category_id: query.category_id ? String(query.category_id) : '',
    location: query.location ? String(query.location) : '',
    work_type: query.work_type ? String(query.work_type) : '',
    experience_level: query.experience_level ? String(query.experience_level) : '',
    salary_min: query.salary_min ? String(query.salary_min) : '',
    salary_max: query.salary_max ? String(query.salary_max) : '',
    sort: query.sort ? String(query.sort) : 'newest',
  }
}

function buildQuery({ page = 1 } = {}) {
  const query = {
    q: q.value.trim() || undefined,
    category_id: filters.value.category_id || undefined,
    location: filters.value.location.trim() || undefined,
    work_type: filters.value.work_type || undefined,
    experience_level: filters.value.experience_level || undefined,
    salary_min: filters.value.salary_min || undefined,
    salary_max: filters.value.salary_max || undefined,
    sort: filters.value.sort || 'newest',
    page: page > 1 ? String(page) : undefined,
  }
  return Object.fromEntries(Object.entries(query).filter(([, value]) => value))
}

async function loadJobsFromRoute() {
  loading.value = true
  error.value = ''
  try {
    jobsResponse.value = await fetchJobs({ ...route.query, per_page: 12 })
  } catch {
    error.value = 'Backend error. Please try again.'
  } finally {
    loading.value = false
  }
}

function updateQuery(next) { router.replace({ query: next }) }
function onSearch() { updateQuery(buildQuery({ page: 1 })) }
function onClearSearch() { q.value = ''; onSearch(); }
function onApplyFilters() { onSearch(); }
function onResetFilters() { onSearch(); }
function onSelectCategory(id) { filters.value.category_id = String(id || ''); onSearch(); }
function goToPage(p) { updateQuery(buildQuery({ page: p })) }

watch(() => route.query, () => { syncLocalFromQuery(route.query); loadJobsFromRoute(); }, { immediate: true, deep: true })
onMounted(async () => { categories.value = await fetchCategories() })
</script>

<template>
  <div class="max-w-[1550px] mx-auto px-6 py-12 space-y-16 min-h-screen font-sans">
    
    <header class="p-16 rounded-[4rem] border border-white/20 bg-black/45 backdrop-blur-3xl shadow-2xl">
      <h1 class="text-7xl font-[1000] tracking-tighter text-white leading-none">Explore Careers</h1>
      <p class="mt-6 text-white/70 text-2xl font-medium max-w-3xl leading-relaxed">
        Premium opportunities filtered by experts, designed for the next generation of talent.
      </p>
    </header>

    <div class="grid gap-12 lg:grid-cols-[420px,1fr] items-start">
      
      <aside class="space-y-10 sticky top-12">
        <div class="p-12 rounded-[3.5rem] border border-white/10 bg-black/55 backdrop-blur-3xl shadow-2xl">
          <h3 class="text-white font-black text-xs uppercase tracking-[0.4em] mb-10 opacity-40">Discovery Tool</h3>
          <SearchBar v-model="q" @search="onSearch" @clear="onClearSearch" />
          <div class="mt-12 pt-12 border-t border-white/10">
            <FiltersPanel v-model="filters" :categories="categories" @apply="onApplyFilters" @reset="onResetFilters" />
          </div>
          <div class="mt-12 pt-12 border-t border-white/10">
            <CategoryList :categories="categories" :active-id="filters.category_id" @select="onSelectCategory" />
          </div>
        </div>
      </aside>

      <section class="space-y-12">
        <div class="flex items-center justify-between px-10">
          <div>
            <h2 class="text-5xl font-[1000] tracking-tight text-white">Live Opportunities</h2>
            <p v-if="meta" class="text-sm font-black text-indigo-400 uppercase tracking-[0.5em] mt-4">
              {{ meta.total }} Results Discovered
            </p>
          </div>
          <button @click="loadJobsFromRoute" class="p-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 transition-all text-white shadow-xl shadow-indigo-900/40 transform hover:scale-105">
            <svg :class="{ 'animate-spin': loading }" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>

        <div v-if="loading" class="grid gap-10 md:grid-cols-2">
          <div v-for="n in 6" :key="n" class="h-[450px] rounded-[4rem] bg-white/5 animate-pulse border border-white/5"></div>
        </div>

        <div v-else class="grid gap-10 md:grid-cols-1 xl:grid-cols-2 items-stretch">
          <Motion 
            v-for="(job, index) in jobs" 
            :key="job.id"
            :initial="{ opacity: 0, y: 40 }"
            :animate="{ opacity: 1, y: 0 }"
            :transition="{ delay: index * 0.05 }"
            class="flex" 
          >
            <RouterLink
              :to="{ name: 'job-details', params: { id: job.id }, query: route.query }"
              class="group relative flex flex-col w-full min-h-[450px] p-12 rounded-[4rem] border border-white/10 bg-black/30 hover:bg-black/45 backdrop-blur-3xl transition-all duration-500 hover:-translate-y-2 shadow-2xl overflow-hidden"
            >
              <div class="relative z-10 flex flex-col h-full">
                <div class="flex justify-between items-start mb-10">
                  <div class="w-20 h-20 rounded-[1.5rem] bg-indigo-600 flex items-center justify-center text-white text-4xl font-[1000] shadow-2xl flex-shrink-0 overflow-hidden">
                    <img 
                      v-if="job.company_logo" 
                      :src="job.company_logo" 
                      class="w-full h-full object-cover"
                      alt="Logo"
                    />
                    <span v-else>{{ job.companyName?.[0] || 'J' }}</span>
                  </div>
                  <span class="px-6 py-2 rounded-2xl bg-indigo-500/30 border border-indigo-400/40 text-[11px] font-[1000] text-indigo-100 uppercase tracking-widest shadow-lg backdrop-blur-md">
                    {{ job.workType || 'Remote' }}
                  </span>
                </div>

                <div class="space-y-4 mb-8 flex-grow">
                  <h3 class="text-4xl font-black text-white leading-tight tracking-tighter group-hover:text-indigo-300 transition-colors line-clamp-2 min-h-[5.5rem]">
                    {{ job.title }}
                  </h3>
                  <p class="text-xl font-bold text-white/50">{{ job.companyName || 'Top Company' }}</p>
                </div>

                <div class="flex flex-wrap gap-4 mt-auto">
                  <span v-if="job.location" class="tag-modern">📍 {{ job.location }}</span>
                  <span v-if="job.experienceLevel" class="tag-modern">🎓 {{ job.experienceLevel }}</span>
                </div>
              </div>

              <div class="absolute bottom-12 right-12 opacity-0 group-hover:opacity-100 translate-x-6 group-hover:translate-x-0 transition-all duration-500">
                <div class="w-16 h-16 rounded-full bg-white text-indigo-900 flex items-center justify-center shadow-2xl">
                  <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </div>
              </div>
            </RouterLink>
          </Motion>
        </div>

        <div v-if="meta && meta.last_page > 1" class="pt-20 flex items-center justify-center gap-10">
          <button @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="nav-control">Prev</button>
          <div class="flex items-center gap-6 bg-black/55 backdrop-blur-2xl px-10 py-5 rounded-[2.5rem] border border-white/10 shadow-2xl">
             <span class="text-2xl font-black text-white">{{ meta.current_page }}</span>
             <span class="text-xs font-black text-white/25 uppercase tracking-[0.3em]">of {{ meta.last_page }}</span>
          </div>
          <button @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="nav-control">Next</button>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.tag-modern {
  @apply px-5 py-2.5 rounded-2xl bg-black/40 border border-white/5 text-xs font-black text-white/80 uppercase tracking-tighter backdrop-blur-2xl transition-all group-hover:border-white/20 group-hover:text-white group-hover:bg-black/60;
}

.nav-control {
  @apply px-12 py-6 rounded-[2.5rem] bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.4em] shadow-2xl transition-all hover:bg-indigo-500 hover:-translate-y-1 disabled:opacity-5 disabled:grayscale;
}

h1, h2, h3 {
  letter-spacing: -0.04em;
  -webkit-font-smoothing: antialiased;
  text-shadow: none !important;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>