<template>
  <form @submit.prevent="submitApplication" enctype="multipart/form-data" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Cover Letter</label>
      <textarea v-model="form.cover_letter" class="mt-1 block w-full rounded border-gray-300 shadow-sm" rows="6"></textarea>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Upload CV (PDF only)</label>
      <input type="file" @change="handleFileUpload" accept=".pdf" required class="mt-1" />
    </div>

    <div>
      <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
        {{ loading ? 'Sending...' : 'Apply Now' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { http } from '../api/http';

const props = defineProps({
  jobId: { type: [String, Number], required: true }
});

const loading = ref(false);
const form = ref({
  cover_letter: '',
  resume: null
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file && file.type !== 'application/pdf') {
    alert('Only PDF files allowed.');
    event.target.value = '';
    return;
  }
  form.value.resume = file;
};

const submitApplication = async () => {
  if (!form.value.resume) {
    alert('Please upload your CV (PDF).');
    return;
  }

  loading.value = true;
  const formData = new FormData();
  formData.append('job_id', props.jobId);
  formData.append('cover_letter', form.value.cover_letter);
  formData.append('resume', form.value.resume);

  try {
    await http.post('/applications', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    alert('Application sent successfully!');
    form.value.cover_letter = '';
    form.value.resume = null;
  } catch (error) {
    const msg = error?.response?.data?.message || error.message || 'Error submitting';
    alert(msg);
  } finally {
    loading.value = false;
  }
};
</script>
