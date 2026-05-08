<script setup lang="ts">
withDefaults(
  defineProps<{
    variant?: string
  }>(),
  { variant: 'default' }
)

const variantClasses: Record<string, string> = {
  // Appointment status
  pending: 'bg-amber-100 text-amber-700',
  confirmed: 'bg-blue-100 text-blue-700',
  completed: 'bg-emerald-100 text-emerald-700',
  cancelled: 'bg-red-100 text-red-700',
  // Invoice status
  paid: 'bg-emerald-100 text-emerald-700',
  unpaid: 'bg-amber-100 text-amber-700',
  refunded: 'bg-purple-100 text-purple-700',
  // User roles
  admin: 'bg-purple-100 text-purple-700',
  doctor: 'bg-blue-100 text-blue-700',
  patient: 'bg-emerald-100 text-emerald-700',
  receptionist: 'bg-orange-100 text-orange-700',
  // Generic
  active: 'bg-emerald-100 text-emerald-700',
  inactive: 'bg-slate-100 text-slate-500',
  online: 'bg-sky-100 text-sky-700',
  'in-person': 'bg-violet-100 text-violet-700',
  default: 'bg-slate-100 text-slate-600'
}
</script>

<template>
  <span
    :class="[
      'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize',
      variantClasses[variant] ?? variantClasses.default
    ]"
  >
    <slot />
  </span>
</template>