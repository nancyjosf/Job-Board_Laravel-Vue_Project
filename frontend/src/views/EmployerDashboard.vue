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
  <div class="space-y-10">
    <!-- Header Section -->
    <header class="p-12 rounded-[3rem] border border-white/10 bg-white/[0.02] backdrop-blur-3xl shadow-2xl">
      <h1 class="text-6xl font-[1000] tracking-tighter text-white leading-none">Employer Console</h1>
      <p class="mt-4 text-white/50 text-xl font-medium max-w-2xl leading-relaxed">
        Control your active listings and monitor performance analytics in real-time.
      </p>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <Motion 
        v-for="(card, index) in statCards" :key="index"
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ delay: index * 0.1 }"
        class="bg-white/[0.03] border border-white/10 p-8 rounded-[2rem] flex flex-col items-center text-center backdrop-blur-xl"
      >
        <span class="text-[9px] font-black uppercase tracking-[0.3em] text-white/30 mb-3">{{ card.label }}</span>
        <h3 class="text-4xl font-[1000] tracking-tighter" :class="card.color">{{ card.value }}</h3>
      </Motion>
    </div>

    <!-- Management Section -->
    <section class="bg-white/[0.02] border border-white/5 rounded-[3rem] p-8 md:p-12 backdrop-blur-2xl">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
          <h2 class="text-3xl font-[1000] tracking-tight text-white">Managed Listings</h2>
          <p class="text-white/40 text-sm font-bold mt-1 uppercase tracking-widest">Active Recruitment Campaigns</p>
        </div>
        <RouterLink to="/employer/jobs/create" class="px-10 py-4 rounded-2xl bg-indigo-600 text-white font-black text-sm uppercase tracking-widest hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all active:scale-95 text-center">
          Post New Job
        </RouterLink>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="space-y-4">
        <div v-for="n in 3" :key="n" class="h-28 bg-white/5 animate-pulse rounded-[2rem]"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="jobs.length === 0" class="text-center py-24 border-2 border-dashed border-white/5 rounded-[2rem]">
        <p class="text-white/20 text-lg font-black uppercase tracking-[0.2em]">No job listings found</p>
      </div>

      <!-- Jobs List -->
      <div v-else class="space-y-4">
        <div v-for="job in jobs" :key="job.id" 
             class="group flex flex-col lg:flex-row lg:items-center justify-between p-6 rounded-[2rem] bg-white/[0.02] border border-white/[0.05] hover:bg-white/[0.05] hover:border-white/20 transition-all duration-500">
          
          <div class="flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center font-black text-indigo-400 text-xl shadow-inner">
              {{ job.title ? job.title[0] : 'J' }}
            </div>
            <div>
              <h4 class="text-xl font-black text-white group-hover:text-indigo-300 transition-colors">{{ job.title }}</h4>
              <div class="flex items-center gap-4 mt-1.5">
                <span class="px-3 py-1 rounded-lg bg-white/5 text-[9px] font-black uppercase tracking-widest text-indigo-300 border border-white/5">{{ job.status }}</span>
                <span class="text-[10px] font-bold text-white/20 uppercase tracking-tighter">Views: {{ job.views_count || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap items-center gap-2 mt-6 lg:mt-0">
            <button v-if="job.status === 'published'" @click="handleAction(job.id, 'unpublish')" class="action-btn">Unpublish</button>
            <button v-if="job.status === 'draft' || job.status === 'unpublished'" @click="handleAction(job.id, 'publish')" class="action-btn">Publish</button>
            
            <RouterLink :to="`/employer/jobs/${job.id}/edit`" class="action-btn !text-white/90">Edit</RouterLink>
            
            <button v-if="job.status !== 'archived'" @click="handleAction(job.id, 'archive')" class="action-btn !text-amber-400/70 hover:!text-amber-400">Archive</button>
            <button @click="handleAction(job.id, 'delete')" class="action-btn !text-red-400/70 hover:!text-red-400">Delete</button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.action-btn {
  @apply px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-[9px] font-black uppercase tracking-widest text-white/50 hover:bg-white/10 hover:text-white transition-all cursor-pointer;
}


@media (max-width: 768px) {
  header { @apply p-8 rounded-[2rem]; }
  .text-6xl { @apply text-4xl; }
}
</style>