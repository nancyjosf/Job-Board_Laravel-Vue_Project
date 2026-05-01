<template>
  <div class="min-h-screen flex items-center justify-center px-6 py-12">
    
    <div class="w-full max-w-2xl card-ui p-12 md:p-16">
      
      <div class="mb-12 text-center">
        <h1 class="text-5xl md:text-6xl font-[1000] tracking-tighter text-glass">
          Welcome Back
        </h1>

        <p class="mt-5 text-white/50 text-lg leading-relaxed">
          Access premium opportunities and continue your journey.
        </p>
      </div>

      <form @submit.prevent="login" class="space-y-8">
        
        <div class="space-y-3">
          <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
            Email Address
          </label>

          <input
            v-model="email"
            type="email"
            placeholder="Enter your email"
            class="field-ui"
          />
        </div>

        <div class="space-y-3">
          <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
            Password
          </label>

          <input
            v-model="password"
            type="password"
            placeholder="Enter your password"
            class="field-ui"
          />
        </div>

        <button
          type="submit"
          class="btn-ui btn-primary-ui w-full"
        >
          Login
        </button>

        <p
          v-if="error"
          class="rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-4 text-sm font-bold text-red-300"
        >
          {{ error }}
        </p>
      </form>

      <div class="mt-10 border-t border-white/10 pt-8 text-center">
        <p class="text-white/40 text-sm">
          Don’t have an account?
        </p>

        <RouterLink
          to="/register"
          class="mt-3 inline-block text-indigo-400 font-black tracking-wide hover:text-indigo-300 transition"
        >
          Create Account
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import {http} from "../api/http";

const router = useRouter();

const email = ref("");
const password = ref("");
const error = ref("");

const login = async () => {
  error.value = "";

  try {
    const res = await http.post("/login", {
      email: email.value,
      password: password.value,
    });

    localStorage.setItem("token", res.data.token);

    router.push("/profile");

  } catch (err) {
    error.value =
      err.response?.data?.message || "Login failed";
  }
};
</script>