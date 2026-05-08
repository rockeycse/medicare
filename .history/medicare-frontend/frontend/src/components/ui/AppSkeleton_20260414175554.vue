<script setup lang="ts">
withDefaults(
  defineProps<{
    type?: 'text' | 'card' | 'table' | 'circle' | 'avatar'
    lines?: number
    rows?: number
  }>(),
  { type: 'text', lines: 3, rows: 5 }
)
</script>

<template>
  <!-- Text skeleton -->
  <div v-if="type === 'text'" class="space-y-2 animate-pulse">
    <div
      v-for="i in lines"
      :key="i"
      :class="['h-4 bg-slate-200 rounded', i === lines ? 'w-3/4' : 'w-full']"
    />
  </div>

  <!-- Card skeleton -->
  <div v-else-if="type === 'card'" class="animate-pulse space-y-4">
    <div class="h-6 w-1/3 bg-slate-200 rounded" />
    <div class="space-y-2">
      <div v-for="i in lines" :key="i" class="h-4 bg-slate-200 rounded" />
    </div>
  </div>

  <!-- Table skeleton -->
  <div v-else-if="type === 'table'" class="animate-pulse space-y-3">
    <div class="h-10 bg-slate-100 rounded-lg" />
    <div v-for="i in rows" :key="i" class="h-14 bg-slate-50 rounded-lg border border-slate-100" />
  </div>

  <!-- Circle / avatar skeleton -->
  <div
    v-else-if="type === 'circle' || type === 'avatar'"
    class="h-10 w-10 bg-slate-200 rounded-full animate-pulse"
  />
</template>