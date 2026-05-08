<script setup lang="ts">
import AppSkeleton from './AppSkeleton.vue'
import { TableCellsIcon } from '@heroicons/vue/24/outline'

defineProps<{
  columns: { key: string; label: string; class?: string }[]
  data: Record<string, unknown>[]
  loading?: boolean
  emptyMessage?: string
}>()
</script>

<template>
  <div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="min-w-full divide-y divide-slate-100">
      <thead class="bg-slate-50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 text-left label-text', col.class]"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50 bg-white">
        <!-- Loading state -->
        <template v-if="loading">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td v-for="col in columns" :key="col.key" class="px-4 py-3">
              <div class="h-4 bg-slate-100 rounded w-3/4" />
            </td>
          </tr>
        </template>

        <!-- Empty state -->
        <tr v-else-if="!data.length">
          <td :colspan="columns.length" class="px-4 py-16 text-center">
            <div class="flex flex-col items-center gap-2">
              <TableCellsIcon class="h-10 w-10 text-slate-300" />
              <p class="text-sm text-slate-400">{{ emptyMessage || 'No data found' }}</p>
            </div>
          </td>
        </tr>

        <!-- Data rows -->
        <template v-else>
          <tr
            v-for="(row, idx) in data"
            :key="idx"
            class="hover:bg-slate-50/60 transition-colors"
          >
            <td
              v-for="col in columns"
              :key="col.key"
              :class="['px-4 py-3 text-sm text-slate-600', col.class]"
            >
              <slot :name="col.key" :row="row" :value="row[col.key]">
                {{ row[col.key] }}
              </slot>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>