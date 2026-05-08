<script setup lang="ts">
import type { Toast } from '@/stores/ui'
import {
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

defineProps<{ toasts: Toast[] }>()
const emit = defineEmits<{ remove: [id: string] }>()

const icons = {
  success: CheckCircleIcon,
  error: XCircleIcon,
  warning: ExclamationTriangleIcon,
  info: InformationCircleIcon
}

const colors = {
  success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  warning: 'bg-amber-50 border-amber-200 text-amber-800',
  info: 'bg-blue-50 border-blue-200 text-blue-800'
}

const iconColors = {
  success: 'text-emerald-500',
  error: 'text-red-500',
  warning: 'text-amber-500',
  info: 'text-blue-500'
}
</script>

<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80">
      <transition-group name="slide-right">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="['flex items-start gap-3 rounded-xl border p-4 shadow-lg backdrop-blur-sm', colors[toast.type]]"
        >
          <component
            :is="icons[toast.type]"
            :class="['h-5 w-5 shrink-0 mt-0.5', iconColors[toast.type]]"
          />
          <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
          <button
            @click="emit('remove', toast.id)"
            class="shrink-0 rounded p-0.5 hover:bg-black/10 transition-colors"
          >
            <XMarkIcon class="h-4 w-4" />
          </button>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>