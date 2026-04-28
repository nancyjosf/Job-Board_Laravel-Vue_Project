<script setup>
import { Motion } from "@motionone/vue"

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Search by title, company, or keywords...' },
})

const emit = defineEmits(['update:modelValue', 'search', 'clear'])

function onInput(event) {
  emit('update:modelValue', event.target.value)
}
</script>

<template>
  <Motion 
    :initial="{ opacity: 0, y: -20 }"
    :animate="{ opacity: 1, y: 0 }"
    :transition="{ duration: 0.8, easing: 'ease-out' }"
    class="relative z-20 w-full"
  >
    <div class="search-container p-3 rounded-[2.5rem] border border-white/50 bg-white/30 backdrop-blur-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 hover:shadow-[0_40px_80px_-20px_rgba(79,70,229,0.15)]">
      
      <div class="flex flex-col md:flex-row items-center gap-3">
        
        <div class="relative flex-1 w-full group">
          <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-focus-within:text-indigo-600 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          
          <input
            :value="props.modelValue"
            :placeholder="props.placeholder"
            class="search-input"
            @input="onInput"
            @keydown.enter.prevent="emit('search')"
          />

          <transition name="fade">
            <button 
              v-if="props.modelValue" 
              @click="emit('clear')"
              class="absolute right-4 top-1/2 -translate-y-1/2 p-2 hover:bg-slate-100 rounded-full transition-all"
            >
              <span class="text-slate-400">✕</span>
            </button>
          </transition>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
          <Motion
            tag="button"
            :hover="{ scale: 1.05 }"
            :press="{ scale: 0.95 }"
            class="search-btn-primary"
            @click="emit('search')"
          >
            <span class="relative z-10 flex items-center justify-center gap-2">
              <span>Find Jobs</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </span>
            <div class="btn-glow"></div>
          </Motion>

          <button 
            class="hidden md:flex p-4 rounded-2xl bg-white/80 border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm"
            title="Advanced Search"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
          </button>
        </div>

      </div>
    </div>

    <div class="mt-4 flex items-center justify-center gap-6 px-4">
      <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Trending:</span>
      <div class="flex gap-4">
        <button v-for="tag in ['Vue.js', 'Laravel', 'Remote']" :key="tag" 
                class="text-[11px] font-bold text-indigo-600/70 hover:text-indigo-600 transition-colors">
          #{{ tag }}
        </button>
      </div>
    </div>
  </Motion>
</template>

<style scoped>
/* 🪄 التصميم البصري (Advanced Styling) */
.search-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.3) 100%);
}

.search-input {
  @apply w-full pl-16 pr-12 py-5 rounded-[2rem] bg-white/40 border border-white/60 text-lg font-bold text-slate-900 
         placeholder:text-slate-400 outline-none transition-all duration-500 focus:bg-white focus:ring-[12px] focus:ring-indigo-500/5 focus:border-indigo-500/30 shadow-inner;
}

.search-btn-primary {
  @apply relative w-full md:w-44 py-5 rounded-[1.8rem] bg-indigo-600 text-white font-black text-sm uppercase tracking-widest overflow-hidden transition-all duration-500 shadow-xl shadow-indigo-200 cursor-pointer;
}

.search-btn-primary:hover {
  @apply bg-indigo-700 -translate-y-1;
  box-shadow: 0 20px 40px rgba(79, 70, 229, 0.3);
}

.btn-glow {
  @apply absolute top-[-50%] left-[-50%] w-[200%] h-[200%] opacity-0 transition-opacity duration-700 pointer-events-none;
  background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
}

.search-btn-primary:hover .btn-glow {
  @apply opacity-100;
}

/* حركة التلاشي */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* تحسين الخط في الـ placeholder */
::placeholder {
  font-weight: 600;
  letter-spacing: -0.01em;
}
</style>