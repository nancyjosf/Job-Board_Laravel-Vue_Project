<template>
  <div class="min-h-screen app-bg px-6 py-12">

    <div class="max-w-5xl mx-auto">

      <div class="card-ui p-10 md:p-14">

        <!-- LOADING -->
        <div v-if="loading" class="text-white text-center py-20">
          Loading profile...
        </div>

        <!-- PROFILE -->
        <div v-else>

          <!-- HEADER -->
          <div class="flex flex-col md:flex-row md:items-center gap-8 mb-12">

            <div
              class="w-32 h-32 rounded-[2rem] overflow-hidden border border-white/10 bg-black/40 flex items-center justify-center text-4xl font-black text-white"
            >
              <img
                v-if="user?.image"
                :src="imageUrl(user.image)"
                class="w-full h-full object-cover"
              />

              <span v-else>
                {{ user?.name?.[0] }}
              </span>
            </div>

            <div>
              <h1 class="text-5xl font-[1000] tracking-tighter text-white">
                {{ user?.name }}
              </h1>

              <p class="text-white/40 mt-3 text-lg">
                {{ user?.email }}
              </p>

              <div class="mt-5">
                <span class="tag-modern">
                  {{ user?.role }}
                </span>
              </div>
            </div>
          </div>

          <!-- FORM -->
          <form @submit.prevent="updateProfile" class="space-y-10">

            <div class="grid md:grid-cols-2 gap-8">

              <div class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  Name
                </label>

                <input v-model="form.name" class="field-ui" />
              </div>

              <div class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  Email
                </label>

                <input v-model="form.email" class="field-ui" />
              </div>

              <div class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  Phone
                </label>

                <input v-model="form.phone" class="field-ui" />
              </div>

              <div v-if="user?.role === 'employer'" class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  Company Name
                </label>

                <input v-model="form.company_name" class="field-ui" />
              </div>

            </div>

            <div v-if="user?.role === 'candidate'" class="space-y-3">
              <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                Skills
              </label>

              <textarea v-model="form.skills" rows="5" class="field-ui"></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-8">

              <div class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  Profile Image
                </label>

                <input type="file" @change="handleImage" class="field-ui" />
              </div>

              <div v-if="user?.role === 'candidate'" class="space-y-3">
                <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
                  CV
                </label>

                <input type="file" @change="handleCv" class="field-ui" />
              </div>

            </div>

            <button type="submit" class="btn-ui btn-primary-ui">
              Save Changes
            </button>

            <p v-if="success" class="text-green-400 font-bold">
              {{ success }}
            </p>

            <p v-if="error" class="text-red-400 font-bold">
              {{ error }}
            </p>

          </form>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import {http} from "../api/http";

const user = ref(null);
const loading = ref(true);

const form = reactive({
  name: "",
  email: "",
  phone: "",
  skills: "",
  company_name: "",
  image: null,
  cv: null,
});

const success = ref("");
const error = ref("");

const imageUrl = (path) => {
  return `http://127.0.0.1:8000/storage/${path}`;
};

const fetchProfile = async () => {
  try {
    const res = await http.get("/profile");

    user.value = res.data.user;

    form.name = user.value?.name || "";
    form.email = user.value?.email || "";
    form.phone = user.value?.phone || "";
    form.skills = user.value?.skills || "";
    form.company_name = user.value?.company_name || "";

  } catch (err) {
    error.value = "Failed to load profile";
  } finally {
    loading.value = false;
  }
};

const handleImage = (e) => {
  form.image = e.target.files[0];
};

const handleCv = (e) => {
  form.cv = e.target.files[0];
};

const updateProfile = async () => {
  success.value = "";
  error.value = "";

  try {
    const formData = new FormData();

    formData.append("name", form.name);
    formData.append("email", form.email);
    formData.append("phone", form.phone);

    if (user.value?.role === "candidate") {
      formData.append("skills", form.skills);
    }

    if (user.value?.role === "employer") {
      formData.append("company_name", form.company_name);
    }

    if (form.image) {
      formData.append("image", form.image);
    }

    if (form.cv) {
      formData.append("cv", form.cv);
    }

    formData.append("_method", "PUT");

    await http.post("/profile", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    success.value = "Profile updated successfully";

    fetchProfile();

  } catch (err) {
    error.value = err.response?.data?.message || "Update failed";
  }
};

onMounted(() => {
  fetchProfile();
});
</script>