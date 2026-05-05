<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { fetchCategories } from "../api/jobs";
import { createJob } from "../api/employer";

const router = useRouter();
const categories = ref([]);
const loading = ref(false);
const error = ref("");
const success = ref("");

const form = reactive({
  category_id: "",
  title: "",
  company_name: "",
  description: "",
  location: "",
  work_type: "remote",
  experience_level: "junior",
  salary_min: "",
  salary_max: "",
  deadline: "",
});

onMounted(async () => {
  categories.value = await fetchCategories();
});

const submitJob = async () => {
  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    await createJob(form);
    success.value = "Job created successfully as draft!";
    setTimeout(() => router.push("/employer/dashboard"), 2000);
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to create job. Please check your inputs.";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-5xl mx-auto">
      
      <header class="mb-12">
        <button @click="router.back()" class="text-xs font-black text-indigo-300 uppercase tracking-[0.3em] hover:text-white transition-all mb-6">← Cancel & Return</button>
        <h1 class="text-6xl font-[1000] tracking-tighter text-white">Post New Opportunity</h1>
      </header>

      <div class="card-ui p-10 md:p-14">
        <form @submit.prevent="submitJob" class="space-y-10">
          
          <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Job Title</label>
              <input v-model="form.title" type="text" placeholder="e.g. Senior Vue Developer" class="field-ui" required />
            </div>

            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Category</label>
              <select v-model="form.category_id" class="field-ui" required>
                <option value="" disabled>Select a category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Description</label>
            <textarea v-model="form.description" rows="6" placeholder="Describe the role, mission, and impact..." class="field-ui" required></textarea>
          </div>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Work Type</label>
              <select v-model="form.work_type" class="field-ui">
                <option value="remote">Remote</option>
                <option value="onsite">On-site</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>

            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Level</label>
              <select v-model="form.experience_level" class="field-ui">
                <option value="junior">Junior</option>
                <option value="mid">Mid</option>
                <option value="senior">Senior</option>
                <option value="lead">Lead</option>
              </select>
            </div>

            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Location</label>
              <input v-model="form.location" type="text" placeholder="e.g. Cairo, Egypt" class="field-ui" />
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Min Salary ($)</label>
              <input v-model="form.salary_min" type="number" class="field-ui" />
            </div>
            <div class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Max Salary ($)</label>
              <input v-model="form.salary_max" type="number" class="field-ui" />
            </div>
          </div>

          <div class="pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
            <p v-if="error" class="text-red-400 font-bold text-sm">{{ error }}</p>
            <p v-if="success" class="text-green-400 font-bold text-sm">{{ success }}</p>
            <div v-else></div>

            <button type="submit" :disabled="loading" class="btn-ui btn-primary-ui w-full md:w-auto px-20">
              <span v-if="loading">Processing...</span>
              <span v-else>Save as Draft</span>
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>