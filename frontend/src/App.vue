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
  } catch (error) {
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
  } catch (error) {
    // Silent catch
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
  <div class="min-h-screen relative font-['Plus_Jakarta_Sans'] selection:bg-indigo-500/30 text-slate-200">
    
    <!-- Background Elements -->
    <div class="fixed inset-0 z-[-1] overflow-hidden bg-[#020617]">
      <img 
        src="https://png.pngtree.com/thumb_back/fh260/background/20230611/pngtree-employees-at-tables-in-this-purple-office-image_2961756.jpg" 
        class="w-full h-full object-cover scale-100 brightness-[1.4] contrast-[1.1] saturate-[1.1]"
        alt="Premium Office"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#020617]/20 to-[#020617]/80"></div>
      <div class="absolute top-[-10%] left-[-5%] w-[700px] h-[700px] bg-white/5 rounded-full blur-[140px] animate-pulse"></div>
      <div class="absolute bottom-[-5%] right-[-5%] w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[120px] animate-bounce"></div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-white/[0.05] bg-white/[0.01] backdrop-blur-3xl transition-all duration-500">
      <div class="max-w-[1400px] mx-auto px-8 h-24 flex items-center justify-between">
        
        <RouterLink :to="isAuthenticated && currentUser?.role === 'employer' ? '/employer/dashboard' : '/'" class="flex items-center gap-5">
          <div class="w-14 h-14 bg-slate-950/40 rounded-2xl flex items-center justify-center border border-white/10">
            <span class="text-white font-black text-2xl">JB</span>
          </div>
          <h1 class="text-3xl font-black text-white tracking-tighter">JOB<span class="text-indigo-300">BOARD</span></h1>
        </RouterLink>

        <nav v-if="!isAuthPage || isAuthenticated" class="hidden lg:flex items-center gap-1 bg-black/20 p-1.5 rounded-2xl border border-white/5 backdrop-blur-2xl">
          <RouterLink to="/" class="px-8 py-2.5 rounded-xl text-sm font-black bg-white text-slate-950 shadow-lg hover:bg-slate-200 transition-all duration-300">
            Explore
          </RouterLink>

          <!-- Candidate Navigation -->
          <RouterLink v-if="isAuthenticated && currentUser?.role === 'candidate'" to="/applications" class="px-6 py-2.5 rounded-xl text-sm font-black text-white hover:bg-white/10 transition-all duration-300">
            My Applications
          </RouterLink>
          
          <!-- Employer Navigation -->
          <template v-if="isAuthenticated && currentUser?.role === 'employer'">
            <RouterLink to="/employer/applications" class="px-6 py-2.5 rounded-xl text-sm font-black text-white hover:bg-white/10 transition-all duration-300">
              Applications
            </RouterLink>
            <RouterLink to="/employer/dashboard" class="px-6 py-2.5 rounded-xl text-sm font-black text-white hover:bg-white/10 transition-all duration-300">
              Dashboard
            </RouterLink>
          </template>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-4">
          <RouterLink v-if="isAuthenticated && currentUser" to="/profile" class="hidden md:flex items-center gap-3 rounded-2xl border border-white/10 bg-black/30 px-4 py-2 backdrop-blur-2xl">
            <div class="w-10 h-10 rounded-xl overflow-hidden bg-white/10 flex items-center justify-center text-sm font-black text-white">
              <img v-if="currentUser.image" :src="imageUrl(currentUser.image)" class="w-full h-full object-cover" alt="Profile" />
              <span v-else>{{ currentUser?.name?.[0] || "U" }}</span>
            </div>
            <span class="text-sm font-bold text-white/90">{{ currentUser.name }}</span>
          </RouterLink>

          <button v-if="isAuthenticated" @click="logout" class="hidden md:block text-sm font-bold text-slate-400 hover:text-white transition-all hover:tracking-widest uppercase">
            Logout
          </button>

          <RouterLink v-if="isAuthenticated && currentUser?.role === 'employer'" to="/employer/jobs/create" class="relative group px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 transition-all hover:scale-105 active:scale-95 shadow-[0_20px_40px_-10px_rgba(79,70,229,0.5)]">
            <span class="text-white font-black text-sm tracking-wide uppercase">Post a Job</span>
          </RouterLink>
        </div>
      </div>
    </header>

    <main class="relative z-10 max-w-[1400px] mx-auto px-8 py-20">
      <RouterView />
    </main>

    <footer class="relative z-10 py-20 border-t border-white/[0.05] bg-slate-950/90 backdrop-blur-xl">
      <div class="max-w-7xl mx-auto px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-black text-sm text-white">JB</div>
              <h3 class="text-xl font-black text-white tracking-tighter uppercase">JobBoard</h3>
            </div>
            <p class="text-slate-400 text-sm leading-relaxed font-medium max-w-sm">
              The world's leading platform for tech recruitment. We connect the next generation of innovators with industry-leading companies worldwide.
            </p>
          </div>

          <div v-for="col in [{ title: 'Platform', links: ['Browse Jobs', 'Companies', 'Pricing', 'API'] }, { title: 'Support', links: ['Help Center', 'Privacy', 'Terms', 'Security'] }]" :key="col.title">
            <h4 class="text-white font-bold text-xs uppercase tracking-[0.3em] mb-8">{{ col.title }}</h4>
            <ul class="space-y-4">
              <li v-for="item in col.links" :key="item">
                <a href="#" class="text-slate-500 hover:text-indigo-400 text-sm font-bold transition-all duration-300">{{ item }}</a>
              </li>
            </ul>
          </div>
        </div>

        <div class="pt-10 border-t border-white/[0.03] flex flex-col md:flex-row justify-between items-center gap-6">
          <span class="text-[10px] font-black text-slate-600 tracking-[0.2em] uppercase">
            © 2026 JOBBOARD PROJECT — ALL RIGHTS RESERVED
          </span>
          <div class="flex gap-8">
            <a href="#" class="text-[10px] font-black text-slate-500 hover:text-white transition-colors tracking-widest">GITHUB</a>
            <a href="#" class="text-[10px] font-black text-slate-500 hover:text-white transition-colors tracking-widest">LINKEDIN</a>
            <a href="#" class="text-[10px] font-black text-slate-500 hover:text-white transition-colors tracking-widest">TWITTER</a>
          </div>
        </div>
      </div>
    </footer>
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