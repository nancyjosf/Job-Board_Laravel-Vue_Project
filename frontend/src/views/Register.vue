<template>
  <div class="min-h-screen app-bg flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-2xl card-ui p-12 md:p-16">
      <div class="mb-12 text-center">
        <h1 class="text-5xl md:text-6xl font-[1000] tracking-tighter text-glass">
          Create Account
        </h1>

        <p class="mt-5 text-white/50 text-lg leading-relaxed">
          Join the next generation hiring platform.
        </p>
      </div>

      <form @submit.prevent="register" class="space-y-8">
        <div class="space-y-3">
          <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
            Full Name
          </label>

          <input
            v-model="name"
            type="text"
            placeholder="Enter your full name"
            class="field-ui"
          />
        </div>

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
            Account Type
          </label>

          <select v-model="role" class="field-ui">
            <option value="candidate">Candidate</option>
            <option value="employer">Employer</option>
          </select>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
              Password
            </label>

            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter password"
                class="field-ui pr-14"
              />
              <button
                type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-white"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
              >
                <span class="material-symbols-outlined text-[20px] leading-none align-middle">
                  {{ showPassword ? 'visibility_off' : 'visibility' }}
                </span>
              </button>
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
              Confirm Password
            </label>

            <div class="relative">
              <input
                v-model="passwordConfirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                placeholder="Confirm password"
                class="field-ui pr-14"
              />
              <button
                type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-white"
                @click="showPasswordConfirmation = !showPasswordConfirmation"
                :aria-label="showPasswordConfirmation ? 'Hide confirm password' : 'Show confirm password'"
              >
                <span class="material-symbols-outlined text-[20px] leading-none align-middle">
                  {{ showPasswordConfirmation ? 'visibility_off' : 'visibility' }}
                </span>
              </button>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-ui btn-primary-ui w-full">
          Create Account
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
          Already have an account?
        </p>

        <RouterLink
          to="/login"
          class="mt-3 inline-block text-indigo-400 font-black tracking-wide hover:text-indigo-300 transition"
        >
          Login Instead
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

const name = ref("");
const email = ref("");
const role = ref("candidate");

const password = ref("");
const passwordConfirmation = ref("");
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const error = ref("");

const redirectPathByRole = {
  candidate: "/",
  employer: "/employer/dashboard",
  admin: "/admin/dashboard",
};

const register = async () => {
  error.value = "";

  try {
    const res = await http.post("/register", {
      name: name.value,
      email: email.value,
      role: role.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    localStorage.setItem("token", res.data.token);
    localStorage.setItem("user_role", res.data.user?.role || "");

    const userRole = res.data.user?.role;
    router.push(redirectPathByRole[userRole] || "/profile");

  } catch (err) {
    error.value =
      err.response?.data?.message || "Register failed";
  }
};
</script>