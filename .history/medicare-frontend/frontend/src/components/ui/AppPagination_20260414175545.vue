<script setup lang="ts">
import { computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  currentPage: number
  lastPage: number
  total: number
}>()

const emit = defineEmits<{ pageChange: [page: number] }>()

const pages = computed(() => {
  const range: (number | string)[] = []
  const delta = 2
  for (let i = 1; i <= props.lastPage; i++) {
    if (
      i === 1 ||
      i === props.lastPage ||
      (i >= props.currentPage - delta && i <= props.currentPage + delta)
    ) {
      range.push(i)
    } else if (range[range.length - 1] !== '...') {
      range.push('...')
    }
  }
  return range
})
</script>

<template>
  <div class="flex items-center justify-between mt-4">
    <p class="text-sm text-slate-500">
      Total <span class="font-medium text-slate-700">{{ total }}</span> records
    </p>
    <div class="flex items-center gap-1">
      <button
        :disabled="currentPage === 1"
        @click="emit('pageChange', currentPage - 1)"
        class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      >
        <ChevronLeftIcon class="h-4 w-4 text-slate-600" />
      </button>

      <template v-for="page in pages" :key="page">
        <span v-if="page === '...'" class="px-2 text-slate-400 text-sm">…</span>
        <button
          v-else
          @click="emit('pageChange', page as number)"
          :class="[
            'min-w-[36px] h-9 rounded-lg text-sm font-medium transition-colors',
            page === currentPage
              ? 'bg-primary-500 text-white shadow-sm'
              : 'text-slate-600 hover:bg-slate-100'
          ]"
        >
          {{ page }}
        </button>
      </template>

      <button
        :disabled="currentPage === lastPage"
        @click="emit('pageChange', currentPage + 1)"
        class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
      >
        <ChevronRightIcon class="h-4 w-4 text-slate-600" />
      </button>
    </div>
  </div>
</template>