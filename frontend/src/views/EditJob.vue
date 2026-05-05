<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { fetchCategories, fetchJobById } from "../api/jobs";
import { updateJob } from "../api/employer";

const route = useRoute();
const router = useRouter();
const categories = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref("");

const form = reactive({
  category_id: "",
  title: "",
  description: "",
  location: "",
  work_type: "",
  experience_level: "",
  salary_min: "",
  salary_max: "",
});

onMounted(async () => {
  try {
    const [cats, jobData] = await Promise.all([
      fetchCategories(),
      fetchJobById(route.params.id)
    ]);
    categories.value = cats;
    
    Object.assign(form, {
      category_id: String(jobData.category_id), 
      title: jobData.title,
      description: jobData.description,
      location: jobData.location,
      work_type: jobData.work_type,
      experience_level: jobData.experience_level,
      salary_min: jobData.salary_min,
      salary_max: jobData.salary_max,
    });
  } catch (err) {
    error.value = "Could not load job data.";
  } finally {
    loading.value = false;
  }
});

const handleUpdate = async () => {
  saving.value = true;
  try {
    await updateJob(route.params.id, form);
    router.push("/employer/dashboard");
  } catch (err) {
    error.value = err.response?.data?.message || "Update failed.";
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-5xl mx-auto">
      <header class="mb-12">
        <button @click="router.back()" class="text-xs font-black text-indigo-300 uppercase tracking-[0.3em] mb-6">← Discard Changes</button>
        <h1 class="text-6xl font-[1000] tracking-tighter text-white">Edit Listing</h1>
      </header>

      <div v-if="loading" class="card-ui p-20 text-center text-white font-bold">Loading Job Details...</div>

      <div v-else class="card-ui p-10 md:p-14">
        <form @submit.prevent="handleUpdate" class="space-y-10">
          <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Job Title</label>
              <input v-model="form.title" type="text" class="field-ui" required />
            </div>
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Category</label>
              <select v-model="form.category_id" class="field-ui" required>
                <option value="" disabled>Select a category</option>
                <option v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                  {{ cat.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Description</label>
            <textarea v-model="form.description" rows="6" class="field-ui" required></textarea>
          </div>

          <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Min Salary</label>
              <input v-model="form.salary_min" type="number" class="field-ui" />
            </div>
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Max Salary</label>
              <input v-model="form.salary_max" type="number" class="field-ui" />
            </div>
          </div>

          <div class="pt-6 border-t border-white/10 flex justify-end gap-6">
            <p v-if="error" class="text-red-400 font-bold self-center">{{ error }}</p>
            <button type="submit" :disabled="saving" class="btn-ui btn-primary-ui px-16">
              {{ saving ? 'Saving...' : 'Update Job' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>