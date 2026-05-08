<script setup lang="ts">
import AppSpinner from './AppSpinner.vue'

const props = withDefaults(
  defineProps<{
    variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline' | 'success'
    size?: 'xs' | 'sm' | 'md' | 'lg'
    loading?: boolean
    disabled?: boolean
    type?: 'button' | 'submit' | 'reset'
  }>(),
  {
    variant: 'primary',
    size: 'md',
    loading: false,
    disabled: false,
    type: 'button'
  }
)

const emit = defineEmits<{ click: [event: MouseEvent] }>()

const variantClasses = {
  primary: 'bg-primary-500 hover:bg-primary-600 text-white shadow-sm shadow-primary-500/25 focus:ring-primary-500',
  secondary: 'bg-slate-100 hover:bg-slate-200 text-slate-700 focus:ring-slate-300',
  danger: 'bg-red-500 hover:bg-red-600 text-white shadow-sm shadow-red-500/25 focus:ring-red-500',
  ghost: 'hover:bg-slate-100 text-slate-600 focus:ring-slate-300',
  outline: 'border border-primary-500 text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
  success: 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-500/25 focus:ring-emerald-500'
}

const sizeClasses = {
  xs: 'px-2.5 py-1 text-xs',
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2 text-sm',
  lg: 'px-6 py-3 text-base'
}
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'btn-base',
      variantClasses[variant],
      sizeClasses[size],
      (disabled || loading) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
    ]"
    @click="emit('click', $event)"
  >
    <AppSpinner v-if="loading" class="h-4 w-4" />
    <slot />
  </button>
</template>