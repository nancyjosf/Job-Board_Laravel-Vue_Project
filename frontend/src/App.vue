<script setup>
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { http } from "./api/http";

const route = useRoute();
const router = useRouter();

const token = ref(localStorage.getItem("token"));
const currentUser = ref(null);

const isAuthPage = computed(() => ["/login", "/register"].includes(route.path));
const isAuthenticated = computed(() => Boolean(token.value));

const imageUrl = (path) => {
  if (!path) return "";
  const apiBase = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000/api";
  const backendBase = apiBase.replace(/\/api\/?$/, "");
  return `${backendBase}/storage/${path}`;
};

const syncAuthState = () => {
  token.value = localStorage.getItem("token");
};

const loadCurrentUser = async () => {
  if (!isAuthenticated.value) {
    currentUser.value = null;
    return;
  }
  try {
    const res = await http.get("/profile");
    currentUser.value = res.data.user;
    localStorage.setItem("user_role", res.data.user?.role || "");
  } catch {
    localStorage.removeItem("token");
    localStorage.removeItem("user_role");
    token.value = null;
    currentUser.value = null;
    if (!isAuthPage.value) router.push("/login");
  }
};

const logout = async () => {
  try {
    await http.post("/logout");
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user_role");
    token.value = null;
    currentUser.value = null;
    router.push("/login");
  }
};

watch(() => route.fullPath, async () => {
  syncAuthState();
  await loadCurrentUser();
}, { immediate: true });
</script>

<template>
  <div class="min-h-screen relative font-['Plus_Jakarta_Sans'] text-slate-200">
    <!-- Background -->
    <div class="fixed inset-0 z-[-1] overflow-hidden bg-[#020617]">
      <img src="https://png.pngtree.com/thumb_back/fh260/background/20230611/pngtree-employees-at-tables-in-this-purple-office-image_2961756.jpg" class="w-full h-full object-cover brightness-[1.4]" />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#020617]"></div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-white/[0.05] bg-white/[0.01] backdrop-blur-3xl">
      <div class="max-w-[1400px] mx-auto px-8 h-24 flex items-center justify-between">
        
       
        <RouterLink :to="isAuthenticated && currentUser?.role === 'employer' ? '/employer/dashboard' : '/'" class="flex items-center gap-5">
          <div class="w-14 h-14 bg-slate-950/40 rounded-2xl flex items-center justify-center border border-white/10">
            <span class="text-white font-black text-2xl">JB</span>
          </div>
          <h1 class="text-3xl font-black text-white tracking-tighter">JOB<span class="text-indigo-300">BOARD</span></h1>
        </RouterLink>

        
        <nav v-if="!isAuthPage || isAuthenticated" class="hidden lg:flex items-center gap-1 bg-black/20 p-1.5 rounded-2xl border border-white/5 backdrop-blur-2xl">
          <RouterLink 
            to="/" 
            class="px-8 py-2.5 rounded-xl text-sm font-black bg-white text-slate-950 shadow-lg hover:bg-slate-200 transition-all duration-300"
          >
            Explore
          </RouterLink>
          
          <template v-if="isAuthenticated && currentUser?.role === 'employer'">
            <RouterLink 
              to="/employer/applications" 
              class="px-6 py-2.5 rounded-xl text-sm font-black text-white/70 hover:text-white hover:bg-white/10 transition-all duration-300"
            >
              Applications
            </RouterLink>
            
          </template>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-4">
          <button v-if="isAuthenticated" @click="logout" class="text-sm font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors">
            Logout
          </button>
          
          <RouterLink 
            v-if="isAuthenticated && currentUser?.role === 'employer'" 
            to="/employer/jobs/create" 
            class="px-8 py-3.5 rounded-2xl bg-indigo-600 text-white font-black text-sm uppercase tracking-wide hover:bg-indigo-500 shadow-[0_10px_30px_-10px_rgba(79,70,229,0.6)] transition-all"
          >
            Post a Job
          </RouterLink>
        </div>
      </div>
    </header>

    <main class="relative z-10 max-w-[1400px] mx-auto px-8 py-20">
      <RouterView />
    </main>
  </div>
</template>

<style>

body { 
  background-color: #020617; 
  -webkit-font-smoothing: antialiased;
}


.field-ui, select {
  @apply w-full bg-slate-900/50 border border-white/10 rounded-2xl px-6 py-4 text-white focus:outline-none focus:border-indigo-500/50 transition-all duration-300 backdrop-blur-md;
}


select option {
  background-color: #0f172a; 
  color: white;
}


::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #020617; }
::-webkit-scrollbar-thumb { 
  background: #334155; 
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover { background: #6366f1; }
</style>