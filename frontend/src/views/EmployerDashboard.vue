
<script setup>
import { onMounted, ref, computed } from "vue";
import { Motion } from "@motionone/vue";
import { fetchEmployerStats, fetchMyJobs, toggleJobStatus, deleteJob } from "../api/employer";

const stats = ref(null);
const jobs = ref([]);
const loading = ref(true);

const loadDashboardData = async () => {
  loading.value = true;
  try {
    const [statsData, jobsData] = await Promise.all([fetchEmployerStats(), fetchMyJobs()]);
    stats.value = statsData;
    jobs.value = jobsData;
  } catch (error) {
    console.error("Dashboard Load Error:", error);
  } finally {
    loading.value = false;
  }
};

const handleAction = async (id, action) => {
  try {
    if (action === 'delete') {
      if (confirm('Are you sure you want to delete this job?')) {
        await deleteJob(id);
      }
    } else {
      await toggleJobStatus(id, action);
    }
    await loadDashboardData();
  } catch (error) {
    alert("Action failed. Please try again.");
  }
};

const statCards = computed(() => [
  { label: "Total Jobs", value: stats.value?.total_jobs || 0, color: "text-white" },
  { label: "Published", value: stats.value?.published_jobs || 0, color: "text-indigo-400" },
  { label: "Drafts", value: stats.value?.draft_jobs || 0, color: "text-amber-400" },
  { label: "Total Views", value: stats.value?.total_views || 0, color: "text-emerald-400" },
]);

onMounted(loadDashboardData);
</script>

<template>

  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-5xl mx-auto card-ui p-10 md:p-14 space-y-10">
      <div>
        <h1 class="text-5xl font-[1000] tracking-tighter text-white">Employer Dashboard</h1>
        <p class="text-white/50 mt-4 text-lg">
          This page is reserved for employer actions and tools.
        </p>
      </div>

      <RouterLink
        to="/employer/applications"
        class="inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-indigo-600 text-white font-black tracking-wide hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-900/30"
      >
        Review Applications
      </RouterLink>

  <div class="space-y-12">
    <header class="p-16 rounded-[4rem] border border-white/20 bg-black/45 backdrop-blur-3xl shadow-2xl">
      <h1 class="text-7xl font-[1000] tracking-tighter text-white leading-none">Employer Console</h1>
      <p class="mt-6 text-white/70 text-2xl font-medium max-w-3xl leading-relaxed">
        Manage your listings, track performance, and discover top talent.
      </p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <Motion 
        v-for="(card, index) in statCards" :key="index"
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ delay: index * 0.1 }"
        class="card-ui p-10 flex flex-col items-center text-center"
      >
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-white/40 mb-4">{{ card.label }}</span>
        <h3 class="text-5xl font-[1000] tracking-tighter" :class="card.color">{{ card.value }}</h3>
      </Motion>

    </div>

    <section class="card-ui p-10 md:p-14">
      <div class="flex items-center justify-between mb-12">
        <h2 class="text-4xl font-[1000] tracking-tight text-white">Managed Listings</h2>
        <RouterLink to="/employer/jobs/create" class="btn-ui btn-primary-ui">Post New Job</RouterLink>
      </div>

      <div v-if="loading" class="space-y-4">
        <div v-for="n in 3" :key="n" class="h-24 bg-white/5 animate-pulse rounded-3xl"></div>
      </div>

      <div v-else-if="jobs.length === 0" class="text-center py-20">
        <p class="text-white/40 text-xl font-bold">No jobs found. Start by posting one!</p>
      </div>

      <div v-else class="space-y-6">
        <div v-for="job in jobs" :key="job.id" 
             class="group flex flex-col md:flex-row md:items-center justify-between p-8 rounded-[2.5rem] bg-white/[0.03] border border-white/5 hover:border-white/20 transition-all duration-500">
          
          <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center font-black text-indigo-400">
              {{ job.title[0] }}
            </div>
            <div>
              <h4 class="text-2xl font-black text-white group-hover:text-indigo-300 transition-colors">{{ job.title }}</h4>
              <div class="flex items-center gap-4 mt-2">
                <span class="tag-modern !py-1 !px-4 !text-[9px]">{{ job.status }}</span>
                <span class="text-xs font-bold text-white/30 tracking-widest uppercase">Views: {{ job.views_count || 0 }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3 mt-6 md:mt-0">
            <button v-if="job.status === 'published'" @click="handleAction(job.id, 'unpublish')" class="action-btn-glass">Unpublish</button>
            <button v-if="job.status === 'draft' || job.status === 'unpublished'" @click="handleAction(job.id, 'publish')" class="action-btn-glass">Publish</button>
            <RouterLink :to="`/employer/jobs/${job.id}/edit`" class="action-btn-glass">Edit</RouterLink>
            <button v-if="job.status !== 'archived'" @click="handleAction(job.id, 'archive')" class="action-btn-glass !text-amber-400">Archive</button>
            <button @click="handleAction(job.id, 'delete')" class="action-btn-glass !text-red-400">Delete</button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.action-btn-glass {
  @apply px-6 py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-white/70 hover:bg-white/10 hover:text-white transition-all cursor-pointer;
}
</style>
```