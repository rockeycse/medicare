<script setup lang="ts">
import {
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'

withDefaults(
  defineProps<{
    type?: 'success' | 'error' | 'warning' | 'info'
    title?: string
  }>(),
  { type: 'info' }
)

const icons = {
  success: CheckCircleIcon,
  error: XCircleIcon,
  warning: ExclamationTriangleIcon,
  info: InformationCircleIcon
}

const styles = {
  success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  warning: 'bg-amber-50 border-amber-200 text-amber-800',
  info: 'bg-blue-50 border-blue-200 text-blue-800'
}

const iconStyles = {
  success: 'text-emerald-500',
  error: 'text-red-500',
  warning: 'text-amber-500',
  info: 'text-blue-500'
}
</script>

<template>
  <div :class="['flex gap-3 rounded-xl border p-4', styles[type]]">
    <component :is="icons[type]" :class="['h-5 w-5 shrink-0 mt-0.5', iconStyles[type]]" />
    <div class="text-sm">
      <p v-if="title" class="font-semibold mb-0.5">{{ title }}</p>
      <slot />
    </div>
  </div>
</template>