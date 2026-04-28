<script setup>
import { Motion } from "@motionone/vue"

const props = defineProps({
  categories: { type: Array, default: () => [] },
  activeId: { type: [String, Number], default: '' },
})

const emit = defineEmits(['select'])

const isActive = (id) => String(props.activeId) === String(id);
</script>

<template>
  <Motion 
    :initial="{ opacity: 0, y: 20 }"
    :animate="{ opacity: 1, y: 0 }"
    :transition="{ duration: 0.8, easing: 'ease-out' }"
    class="relative z-10"
  >
    <div class="filter-style-container p-8 rounded-[2.5rem] border border-white/10 backdrop-blur-2xl shadow-2xl">
      
      <div class="flex items-center justify-between mb-8 px-2">
        <div class="flex items-center gap-4">
          <div class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
          </div>
          <h3 class="text-xl font-[1000] text-white tracking-tighter">Explore Categories</h3>
        </div>
        <div class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em]">
          {{ props.categories.length + 1 }} Options
        </div>
      </div>

      <div class="flex flex-wrap gap-4">
        
        <Motion
          tag="button"
          :hover="{ scale: 1.05 }"
          :press="{ scale: 0.95 }"
          class="cat-btn"
          :class="{ 'is-active-all': isActive('') }"
          @click="emit('select', '')"
        >
          <span class="relative z-10 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
            All Jobs
          </span>
          <div class="glow-effect"></div>
        </Motion>

        <Motion
          v-for="category in props.categories"
          :key="category.id"
          tag="button"
          :hover="{ y: -5, scale: 1.02 }"
          :press="{ scale: 0.98 }"
          class="cat-btn"
          :class="{ 'is-active': isActive(category.id) }"
          @click="emit('select', String(category.id))"
        >
          <div class="relative z-10 flex items-center gap-3">
            <span class="text-sm tracking-tight font-bold">{{ category.name }}</span>
            <span v-if="category.jobsCount" class="count-badge">
              {{ category.jobsCount }}
            </span>
          </div>
          <div class="glow-effect"></div>
        </Motion>
      </div>
    </div>
  </Motion>
</template>

<style scoped>
.filter-style-container {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.cat-btn {
  @apply relative px-7 py-3.5 rounded-2xl font-black text-sm transition-all duration-500 overflow-hidden cursor-pointer outline-none border border-white/10;
  background: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.7);
  /* تم تصحيح السطر هنا بإضافة @apply */
  @apply backdrop-blur-md;
}

.cat-btn.is-active {
  @apply text-white border-transparent;
  background: rgba(79, 70, 229, 0.6);
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

.is-active-all {
  @apply text-white border-transparent;
  background: #ffffff;
  color: #0f172a !important;
  box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
}

.count-badge {
  @apply px-2.5 py-0.5 rounded-lg text-[11px] font-black transition-colors duration-500;
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.6);
}

.is-active .count-badge, 
.is-active-all .count-badge {
  background: rgba(0, 0, 0, 0.2);
  color: white;
}

.glow-effect {
  @apply absolute inset-0 opacity-0 transition-opacity duration-500 pointer-events-none;
  background: radial-gradient(circle at center, rgba(255,255,255,0.15) 0%, transparent 70%);
}

.cat-btn:hover .glow-effect {
  @apply opacity-100;
}

.cat-btn:hover {
  @apply border-white/30 text-white;
  background: rgba(255, 255, 255, 0.1);
}
</style>