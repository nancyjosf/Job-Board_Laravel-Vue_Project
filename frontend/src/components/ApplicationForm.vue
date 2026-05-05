<template>
  <form @submit.prevent="submitApplication" enctype="multipart/form-data" class="space-y-6">
    
    <!-- Cover Letter Section -->
    <div class="space-y-3">
      <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
        Cover Letter (Optional)
      </label>
      <textarea
        v-model="form.cover_letter"
        placeholder="Tell the employer why you're a great fit for this role..."
        class="field-ui min-h-32 resize-none"
      ></textarea>
    </div>

    <!-- File Upload Section -->
    <div class="space-y-3">
      <label class="text-xs font-black uppercase tracking-[0.3em] text-white/40">
        Upload Resume/CV (PDF only, Max 2MB)
      </label>
      
      <div class="relative">
        <input
          type="file"
          ref="fileInput"
          @change="handleFileUpload"
          accept=".pdf"
          required
          class="sr-only"
        />
        
        <button
          type="button"
          @click="$refs.fileInput?.click()"
          class="w-full px-6 py-4 rounded-2xl border-2 border-dashed border-white/20 bg-white/5 text-white/70 font-bold uppercase tracking-[0.2em] hover:border-indigo-500 hover:bg-white/10 transition-all group"
        >
          <span class="group-hover:text-white transition">
            📄 {{ form.resume ? `Selected: ${form.resume.name}` : 'Click to upload PDF' }}
          </span>
        </button>
      </div>

      <!-- File Info -->
      <div v-if="form.resume" class="flex items-center gap-3 p-4 rounded-2xl bg-green-500/10 border border-green-500/20">
        <span class="text-2xl">✅</span>
        <div class="text-sm">
          <p class="text-green-200 font-bold">{{ form.resume.name }}</p>
          <p class="text-green-300/60">{{ (form.resume.size / 1024).toFixed(1) }} KB</p>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-200 text-sm font-bold">
      ❌ {{ error }}
    </div>

    <!-- Submit Button -->
    <button
      type="submit"
      :disabled="loading || !form.resume"
      class="btn-ui btn-primary-ui w-full"
    >
      <span v-if="loading">⏳ Submitting...</span>
      <span v-else>✈️ Submit Application</span>
    </button>

    <!-- Success Message -->
    <div v-if="success" class="p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-200 text-sm font-bold text-center">
      ✅ {{ success }}
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { http } from '../api/http'

const props = defineProps({
  jobId: { type: [String, Number], required: true }
})

const emit = defineEmits(['submitted'])

const loading = ref(false)
const error = ref('')
const success = ref('')
const fileInput = ref(null)

const form = ref({
  cover_letter: '',
  resume: null
})

const handleFileUpload = (event) => {
  const file = event.target.files?.[0]
  
  if (!file) return

  // Validate file type
  if (file.type !== 'application/pdf') {
    error.value = 'Only PDF files are allowed.'
    form.value.resume = null
    return
  }

  // Validate file size (2MB)
  if (file.size > 2048 * 1024) {
    error.value = 'File size must be less than 2MB.'
    form.value.resume = null
    return
  }

  error.value = ''
  form.value.resume = file
}

const submitApplication = async () => {
  error.value = ''
  success.value = ''

  if (!form.value.resume) {
    error.value = 'Please upload your CV/Resume (PDF).'
    return
  }

  loading.value = true

  const formData = new FormData()
  formData.append('job_id', props.jobId)
  formData.append('cover_letter', form.value.cover_letter)
  formData.append('resume', form.value.resume)

  try {
    await http.post('/applications', formData)
    success.value = '✅ Application sent successfully! We will contact you soon.'
    
    // Reset form
    form.value.cover_letter = ''
    form.value.resume = null
    if (fileInput.value) fileInput.value.value = ''
    
    // Emit success event
    emit('submitted')
    
    // Auto-hide success message after 3 seconds
    setTimeout(() => {
      success.value = ''
    }, 3000)
  } catch (err) {
    const msg = err?.response?.data?.message || err.message || 'Error submitting application'
    error.value = msg
  } finally {
    loading.value = false
  }
}
</script>
