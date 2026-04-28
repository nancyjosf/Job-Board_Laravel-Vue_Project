<script setup>
import { Motion } from "@motionone/vue"

const props = defineProps({
  categories: { type: Array, default: () => [] },
  modelValue: {
    type: Object,
    default: () => ({
      category_id: '',
      location: '',
      work_type: '',
      experience_level: '',
      salary_min: '',
      salary_max: '',
      sort: 'newest',
    }),
  },
})

const emit = defineEmits(['update:modelValue', 'apply', 'reset'])

function setField(key, value) {
  emit('update:modelValue', { ...props.modelValue, [key]: value })
}

function onReset() {
  emit('update:modelValue', {
    category_id: '',
    location: '',
    work_type: '',
    experience_level: '',
    salary_min: '',
    salary_max: '',
    sort: 'newest',
  })
  emit('reset')
}
</script>

<template>
  <Motion 
    :initial="{ opacity: 0, x: -20 }"
    :animate="{ opacity: 1, x: 0 }"
    :transition="{ duration: 0.8, easing: 'ease-out' }"
    class="relative"
  >
    <div class="filter-card-glass p-8 rounded-[2.5rem] border border-white/20 backdrop-blur-2xl shadow-2xl">
      
      <div class="mb-8 flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-white/10 rounded-xl border border-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
          </div>
          <h2 class="text-2xl font-[1000] text-white tracking-tighter">Filters</h2>
        </div>
        <button 
          class="text-xs font-black text-white/60 uppercase tracking-widest hover:text-red-400 transition-all"
          type="button" 
          @click="onReset"
        >
          Reset
        </button>
      </div>

      <div class="space-y-6">
        
        <div class="filter-group">
          <label class="filter-label">Job Category</label>
          <div class="relative">
            <select class="filter-field appearance-none" :value="modelValue.category_id" @change="setField('category_id', $event.target.value)">
              <option value="" class="bg-slate-900">All Categories</option>
              <option v-for="c in categories" :key="c.id" :value="String(c.id)" class="bg-slate-900">{{ c.name }}</option>
            </select>
            <div class="select-arrow"></div>
          </div>
        </div>

        <div class="filter-group">
          <label class="filter-label">Location</label>
          <div class="relative">
             <input class="filter-field pl-11" :value="modelValue.location" placeholder="City or Remote..." @input="setField('location', $event.target.value)" />
             <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/50 text-lg">📍</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="filter-group">
            <label class="filter-label">Work Type</label>
            <select class="filter-field" :value="modelValue.work_type" @change="setField('work_type', $event.target.value)">
              <option value="" class="bg-slate-900">Any Type</option>
              <option value="remote" class="bg-slate-900">Remote</option>
              <option value="onsite" class="bg-slate-900">On-site</option>
              <option value="hybrid" class="bg-slate-900">Hybrid</option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">Level</label>
            <select class="filter-field" :value="modelValue.experience_level" @change="setField('experience_level', $event.target.value)">
              <option value="" class="bg-slate-900">Any Level</option>
              <option value="junior" class="bg-slate-900">Junior</option>
              <option value="mid" class="bg-slate-900">Mid</option>
              <option value="senior" class="bg-slate-900">Senior</option>
              <option value="lead" class="bg-slate-900">Lead</option>
            </select>
          </div>
        </div>

        <div class="filter-group">
          <label class="filter-label">Salary Range ($)</label>
          <div class="grid grid-cols-2 gap-3">
            <input class="filter-field text-center" :value="modelValue.salary_min" inputmode="numeric" placeholder="Min" @input="setField('salary_min', $event.target.value)" />
            <input class="filter-field text-center" :value="modelValue.salary_max" inputmode="numeric" placeholder="Max" @input="setField('salary_max', $event.target.value)" />
          </div>
        </div>

        <div class="filter-group border-t border-white/10 pt-6">
          <label class="filter-label">Sort Results By</label>
          <select class="filter-field !bg-white !text-slate-950 !font-[1000]" :value="modelValue.sort" @change="setField('sort', $event.target.value)">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="salary_desc">Highest salary</option>
            <option value="salary_asc">Lowest salary</option>
          </select>
        </div>
      </div>

      <Motion
        tag="div"
        :hover="{ scale: 1.02 }"
        :press="{ scale: 0.98 }"
        class="mt-10"
      >
        <button 
          class="w-full py-5 rounded-2xl bg-indigo-600 text-white font-[1000] text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/20 hover:bg-indigo-500 transition-all duration-300"
          type="button" 
          @click="emit('apply')"
        >
          Apply Selection
        </button>
      </Motion>
    </div>
  </Motion>
</template>

<style scoped>
/* 🎨 تنسيق الزجاج الفاتح المطابق للكروت */
.filter-card-glass {
  background: rgba(255, 255, 255, 0.08); /* درجة شفافة فاتحة */
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.filter-group {
  @apply flex flex-col gap-2;
}

.filter-label {
  @apply text-[11px] font-black text-white/70 uppercase tracking-[0.2em] ml-1 mb-1;
}

.filter-field {
  /* الحقول شفافة جداً لتظهر الخلفية من تحتها */
  @apply w-full px-5 py-4 rounded-2xl bg-white/5 border border-white/10 text-sm font-black text-white 
         transition-all duration-300 outline-none backdrop-blur-md;
}

.filter-field:focus {
  @apply bg-white/15 border-white/40 ring-4 ring-white/5;
}

.filter-field::placeholder {
  @apply text-white/30;
}

.select-arrow {
  @apply absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none;
  width: 0; height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 6px solid rgba(255, 255, 255, 0.6);
}

select option {
  @apply bg-slate-900 text-white font-bold py-2;
}

/* السكرول بار */
select::-webkit-scrollbar {
  width: 4px;
}
select::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
</style>