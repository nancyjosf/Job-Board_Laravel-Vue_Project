<template>
  <div class="min-h-screen app-bg px-6 py-12">
    <div class="max-w-5xl mx-auto card-ui p-10 md:p-14 space-y-10">
      <div>
        <h1 class="text-5xl font-[1000] tracking-tighter text-white">Employer Dashboard</h1>
        <p class="text-white/50 mt-4 text-lg">
          This page is reserved for employer actions and tools.
        </p>
      </div>

      <RouterLink
        to="/employer/applications"
        class="inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-indigo-600 text-white font-black tracking-wide hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-900/30"
      >
        Review Applications
      </RouterLink>
    </div>
  </div>
</template>
