<script setup lang="ts">
defineProps<{
  modelValue?: string | number
  label?: string
  options: { value: string | number; label: string }[]
  placeholder?: string
  error?: string
  required?: boolean
  disabled?: boolean
}>()

const emit = defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" class="label-text">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <select
      :value="modelValue"
      :required="required"
      :disabled="disabled"
      :class="['input-base', error ? 'border-red-400' : '']"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">
        {{ opt.label }}
      </option>
    </select>
    <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
  </div>
</template>