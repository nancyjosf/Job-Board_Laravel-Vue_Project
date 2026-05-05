<template>
  <span :class="badgeClasses">
    <span v-if="statusIcon" class="inline-block mr-2">{{ statusIcon }}</span>
    {{ statusLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'pending',
    validator: (val) => ['pending', 'accepted', 'rejected', 'canceled'].includes(val)
  }
})

const statusLabel = computed(() => {
  const labels = {
    pending: 'Pending',
    accepted: 'Accepted',
    rejected: 'Rejected',
    canceled: 'Cancelled'
  }
  return labels[props.status] || 'Unknown'
})

const statusIcon = computed(() => {
  const icons = {
    pending: '⏳',
    accepted: '✅',
    rejected: '❌',
    canceled: '🚫'
  }
  return icons[props.status] || ''
})

const badgeClasses = computed(() => {
  const base = 'inline-flex items-center px-4 py-2 rounded-2xl font-bold text-xs uppercase tracking-[0.2em] transition-all'
  
  const variants = {
    pending: 'bg-yellow-500/20 text-yellow-200 border border-yellow-500/30 hover:bg-yellow-500/30',
    accepted: 'bg-green-500/20 text-green-200 border border-green-500/30 hover:bg-green-500/30',
    rejected: 'bg-red-500/20 text-red-200 border border-red-500/30 hover:bg-red-500/30',
    canceled: 'bg-gray-500/20 text-gray-200 border border-gray-500/30 hover:bg-gray-500/30'
  }
  
  return `${base} ${variants[props.status] || variants.pending}`
})
</script>
