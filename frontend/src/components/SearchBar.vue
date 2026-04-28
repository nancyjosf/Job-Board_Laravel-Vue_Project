<script setup>
import { Motion } from "@motionone/vue"

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Search by title, company, or keywords...' },
})

const emit = defineEmits(['update:modelValue', 'search', 'clear'])

function handleInput(event) {
  emit('update:modelValue', event.target.value)
}

function onClear() {
  emit('update:modelValue', '')
  emit('clear')
}
</script>

<template>
  <Motion 
    :initial="{ opacity: 0, y: -20 }"
    :animate="{ opacity: 1, y: 0 }"
    :transition="{ duration: 0.8, easing: 'ease-out' }"
    class="relative z-20 w-full"
  >
    <div class="search-container p-3 rounded-[2.5rem] border border-white/10 bg-slate-900/40 backdrop-blur-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] transition-all duration-500">
      
      <div class="flex flex-col md:flex-row items-center gap-3">
        
        <div class="relative flex-1 w-full group">
          <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-400 group-focus-within:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          
          <input
            type="text"
            :value="props.modelValue"
            :placeholder="props.placeholder"
            class="search-input"
            @input="handleInput"
            @keydown.enter.prevent="emit('search')"
          />

          <transition name="fade">
            <button 
              v-if="props.modelValue" 
              @click="onClear"
              type="button"
              class="absolute right-4 top-1/2 -translate-y-1/2 p-2 hover:bg-white/10 rounded-full transition-all z-30"
            >
              <span class="text-white hover:text-indigo-300 font-bold">✕</span>
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
        </div>

      </div>
    </div>

    <div class="mt-4 flex items-center justify-center gap-6 px-4">
      <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Trending:</span>
      <div class="flex gap-4">
        <button v-for="tag in ['Vue.js', 'Laravel', 'Remote']" :key="tag" 
                class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
          #{{ tag }}
        </button>
      </div>
    </div>
  </Motion>
</template>

<style scoped>
.search-container {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
}

.search-input {
  @apply w-full pl-16 pr-12 py-5 rounded-[2rem] bg-slate-950/60 border border-white/10 text-lg font-bold outline-none transition-all duration-500 focus:bg-slate-900 focus:ring-[12px] focus:ring-indigo-500/10 focus:border-indigo-500/50 shadow-2xl;
  
  color: #ffffff !important;         
  caret-color: #818cf8 !important;   
  -webkit-text-fill-color: white !important;
}

.search-btn-primary {
  @apply relative w-full md:w-44 py-5 rounded-[1.8rem] bg-indigo-600 text-white font-black text-sm uppercase tracking-widest overflow-hidden transition-all duration-500 shadow-xl cursor-pointer;
}

.search-btn-primary:hover {
  @apply bg-indigo-700 -translate-y-1;
  box-shadow: 0 20px 40px rgba(79, 70, 229, 0.3);
}

.btn-glow {
  @apply absolute top-[-50%] left-[-50%] w-[200%] h-[200%] opacity-0 transition-opacity duration-700 pointer-events-none;
  background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
}

.search-btn-primary:hover .btn-glow { @apply opacity-100; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
::placeholder { font-weight: 600; letter-spacing: -0.01em; color: #64748b; }
</style>