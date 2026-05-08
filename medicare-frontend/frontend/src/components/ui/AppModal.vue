<script setup lang="ts">
import { watch, onUnmounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = withDefaults(
  defineProps<{
    modelValue: boolean
    title?: string
    size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl'
  }>(),
  { size: 'md' }
)

const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>()

function close() {
  emit('update:modelValue', false)
}

function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape') close()
}

watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      document.addEventListener('keydown', onKeydown)
      document.body.style.overflow = 'hidden'
    } else {
      document.removeEventListener('keydown', onKeydown)
      document.body.style.overflow = ''
    }
  }
)

onUnmounted(() => {
  document.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})

const sizeClasses = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  '2xl': 'max-w-2xl'
}
</script>

<template>
  <teleport to="body">
    <transition name="page">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
          @click="close"
        />
        <!-- Modal -->
        <div
          :class="['relative w-full bg-white rounded-2xl shadow-xl animate-slide-down', sizeClasses[size]]"
        >
          <!-- Header -->
          <div v-if="title" class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-lg font-semibold text-slate-800">{{ title }}</h3>
            <button
              @click="close"
              class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
            >
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>
          <!-- Body -->
          <div class="p-6">
            <slot />
          </div>
          <!-- Footer -->
          <div v-if="$slots.footer" class="border-t border-slate-100 px-6 py-4">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>