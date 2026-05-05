<template>
  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-6xl mx-auto space-y-10">
      <header class="card-ui p-10 md:p-14">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-black uppercase tracking-[0.5em] text-white/30">Employer Workspace</p>
            <h1 class="mt-4 text-5xl md:text-6xl font-[1000] tracking-tighter text-white">Applications Review</h1>
            <p class="mt-4 text-white/55 text-lg max-w-3xl leading-relaxed">
              Review submitted applications, inspect candidate details, and accept or reject with one action.
            </p>
          </div>

          <button @click="fetchApplications" class="btn-ui btn-primary-ui self-start md:self-auto">
            Refresh List
          </button>
        </div>
      </header>

      <section class="card-ui p-10 md:p-14 space-y-8">
        <div v-if="loading" class="text-white/60 py-16 text-center text-lg">
          Loading applications...
        </div>

        <div v-else-if="error" class="py-16 text-center">
          <p class="text-red-400 font-bold text-lg">{{ error }}</p>
          <button @click="fetchApplications" class="mt-6 btn-ui btn-primary-ui">Try Again</button>
        </div>

        <div v-else-if="applications.length === 0" class="py-20 text-center">
          <p class="text-3xl md:text-4xl font-black text-white">No applications yet</p>
          <p class="text-white/40 mt-4 text-lg">Once candidates apply, they will appear here for review.</p>
        </div>

        <div v-else class="grid gap-6">
          <article
            v-for="application in applications"
            :key="application.id"
            class="rounded-[2.5rem] border border-white/10 bg-black/30 backdrop-blur-2xl p-8 md:p-10 shadow-2xl"
          >
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
              <div class="space-y-5 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="tag-modern">#{{ application.id }}</span>
                  <span class="tag-modern">{{ application.status || 'pending' }}</span>
                  <span class="tag-modern">{{ formatDate(application.created_at) }}</span>
                </div>

                <div>
                  <h2 class="text-3xl font-black tracking-tight text-white">
                    {{ application.job?.title || application.job_title || 'Untitled Position' }}
                  </h2>
                  <p class="mt-2 text-white/50 text-lg font-medium">
                    {{ application.candidate?.name || 'Unknown Candidate' }}
                    <span v-if="application.candidate?.email" class="text-white/25">•</span>
                    {{ application.candidate?.email || '' }}
                  </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="p-5 rounded-[1.75rem] bg-white/5 border border-white/5">
                    <p class="text-[11px] font-black uppercase tracking-[0.4em] text-white/25">Cover Letter</p>
                    <p class="mt-3 text-white/75 leading-7 whitespace-pre-wrap">
                      {{ application.cover_letter || 'No cover letter provided.' }}
                    </p>
                  </div>

                  <div class="p-5 rounded-[1.75rem] bg-white/5 border border-white/5 space-y-4">
                    <div>
                      <p class="text-[11px] font-black uppercase tracking-[0.4em] text-white/25">Candidate CV</p>
                      <a
                        v-if="application.resume_url"
                        :href="application.resume_url"
                        target="_blank"
                        class="inline-flex mt-3 text-indigo-300 hover:text-indigo-200 font-bold underline underline-offset-4"
                      >
                        Open Resume
                      </a>
                      <p v-else class="mt-3 text-white/55">No resume link available.</p>
                    </div>

                    <div v-if="application.notes || application.message" class="text-sm text-white/60">
                      {{ application.notes || application.message }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex flex-row lg:flex-col gap-3 lg:min-w-[180px]">
                <button
                  @click="updateStatus(application.id, 'accepted')"
                  :disabled="updatingIds.includes(application.id)"
                  class="btn-ui btn-primary-ui flex-1 lg:flex-none"
                >
                  {{ updatingIds.includes(application.id) ? 'Updating...' : 'Accept' }}
                </button>

                <button
                  @click="updateStatus(application.id, 'rejected')"
                  :disabled="updatingIds.includes(application.id)"
                  class="px-6 py-4 rounded-2xl border border-red-400/30 bg-red-500/10 text-red-200 font-black uppercase tracking-[0.2em] hover:bg-red-500/20 transition-all disabled:opacity-50"
                >
                  Reject
                </button>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { http } from '../api/http';

const applications = ref([]);
const loading = ref(false);
const error = ref('');
const updatingIds = ref([]);

const normalizeApplications = (payload) => payload?.data ?? payload ?? [];

const fetchApplications = async () => {
  loading.value = true;
  error.value = '';

  try {
  const response = await http.get('/employer/applications');
    applications.value = normalizeApplications(response.data);
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to load applications.';
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (applicationId, status) => {
  if (updatingIds.value.includes(applicationId)) return;

  updatingIds.value.push(applicationId);

  try {
    await http.patch(`/applications/${applicationId}/status`, { status });
    alert(`Application ${status}!`);
    await fetchApplications();
  } catch (error) {
    console.error('Failed to update status', error);
    alert(error?.response?.data?.message || 'Failed to update application status.');
  } finally {
    updatingIds.value = updatingIds.value.filter((id) => id !== applicationId);
  }
};

const formatDate = (value) => {
  if (!value) return '-';
  try {
    return new Date(value).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  } catch {
    return value;
  }
};

onMounted(fetchApplications);
</script>