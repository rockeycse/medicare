<script setup lang="ts">
withDefaults(
  defineProps<{
    modelValue?: string
    label?: string
    placeholder?: string
    error?: string
    required?: boolean
    disabled?: boolean
    rows?: number
  }>(),
  { rows: 4 }
)

const emit = defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" class="label-text">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <textarea
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :rows="rows"
      :class="['input-base resize-none', error ? 'border-red-400' : '']"
      @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    />
    <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
  </div>
</template>