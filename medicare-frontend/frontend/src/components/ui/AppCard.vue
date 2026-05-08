<script setup lang="ts">
withDefaults(
  defineProps<{
    padding?: string
    class?: string
  }>(),
  { padding: 'p-6' }
)
</script>

<template>
  <div :class="['card', padding, $props.class]">
    <slot />
  </div>
</template>