<template>
  <div class="space-y-4">
    <h2 class="text-lg font-semibold">My Applications</h2>

    <div v-if="loading" class="text-sm text-gray-500">Loading applications...</div>

    <div v-else>
      <div v-if="applications.length === 0" class="text-sm text-gray-600">You have not applied to any jobs yet.</div>

      <ul class="space-y-3 mt-2">
        <li v-for="app in applications" :key="app.id" class="p-4 border rounded-md bg-white shadow-sm flex justify-between items-start">
          <div>
            <div class="font-medium text-gray-800">{{ app.job_listing?.title || app.job_title || 'Untitled Job' }}</div>
            <div class="text-sm text-gray-600 mt-1">Submitted: {{ formatDate(app.created_at) }}</div>
            <div v-if="app.cover_letter" class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{{ app.cover_letter }}</div>
            <div v-if="app.status" class="mt-1 text-sm">Status: <span class="font-medium">{{ app.status }}</span></div>
          </div>

          <div class="flex flex-col items-end space-y-2">
            <a v-if="app.resume_url" :href="app.resume_url" target="_blank" class="text-sm text-blue-600 underline">View CV</a>
            <button
              class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
              @click="cancelApplication(app.id)"
              :disabled="cancelingIds.includes(app.id)"
            >
              {{ cancelingIds.includes(app.id) ? 'Cancelling...' : 'Cancel' }}
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const applications = ref([]);
const loading = ref(false);
const cancelingIds = ref([]);

const fetchApplications = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/my-applications');
    // handle both { data: [...] } and raw array responses
    applications.value = res.data?.data ?? res.data ?? [];
  } catch (err) {
    const msg = err?.response?.data?.message || err.message || 'Failed to load applications';
    alert(msg);
  } finally {
    loading.value = false;
  }
};

const cancelApplication = async (id) => {
  if (!confirm('Are you sure you want to cancel this application?')) return;
  if (cancelingIds.value.includes(id)) return;
  cancelingIds.value.push(id);

  try {
    await axios.delete(`/api/applications/${id}`);
    applications.value = applications.value.filter(a => a.id !== id);
  } catch (err) {
    const msg = err?.response?.data?.message || err.message || 'Failed to cancel';
    alert(msg);
  } finally {
    const idx = cancelingIds.value.indexOf(id);
    if (idx !== -1) cancelingIds.value.splice(idx, 1);
  }
};

const formatDate = (iso) => {
  if (!iso) return '-';
  try {
    return new Date(iso).toLocaleString();
  } catch {
    return iso;
  }
};

onMounted(fetchApplications);
</script>

<style scoped>
/* keep styles minimal to fit existing design */
</style>
