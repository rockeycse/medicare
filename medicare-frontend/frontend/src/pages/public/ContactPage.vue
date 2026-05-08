<script setup lang="ts">
import { reactive, ref } from 'vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import { PhoneIcon, EnvelopeIcon, MapPinIcon, ClockIcon } from '@heroicons/vue/24/outline'

const form = reactive({ name: '', email: '', subject: '', message: '' })
const submitted = ref(false)
const loading = ref(false)

async function handleSubmit() {
  loading.value = true
  await new Promise(r => setTimeout(r, 1000))
  submitted.value = true
  loading.value = false
}

const contacts = [
  { icon: PhoneIcon, label: 'Phone', value: '+880 1700-000000', color: 'bg-blue-50 text-blue-500' },
  { icon: EnvelopeIcon, label: 'Email', value: 'info@medicare.com', color: 'bg-emerald-50 text-emerald-500' },
  { icon: MapPinIcon, label: 'Address', value: 'Dhaka, Bangladesh', color: 'bg-purple-50 text-purple-500' },
  { icon: ClockIcon, label: 'Hours', value: 'Sat–Thu: 8am–8pm', color: 'bg-orange-50 text-orange-500' },
]
</script>

<template>
  <div class="pt-16 min-h-screen bg-slate-50">
    <div class="bg-gradient-to-r from-primary-600 to-sky-500 py-16">
      <div class="mx-auto max-w-4xl px-6 text-center text-white">
        <h1 class="text-4xl font-bold mb-3">Contact Us</h1>
        <p class="text-white/80">We're here to help. Reach out anytime.</p>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-16">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
        <!-- Contact info -->
        <div class="lg:col-span-2 space-y-4">
          <h2 class="text-2xl font-bold text-slate-800 mb-6">Get in Touch</h2>
          <div v-for="c in contacts" :key="c.label" class="flex items-center gap-4 bg-white rounded-xl p-4 border border-slate-100">
            <div :class="['flex h-10 w-10 items-center justify-center rounded-lg shrink-0', c.color]">
              <component :is="c.icon" class="h-5 w-5" />
            </div>
            <div>
              <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">{{ c.label }}</p>
              <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ c.value }}</p>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-8">
          <AppAlert v-if="submitted" type="success" class="mb-6">
            Message sent! We'll get back to you within 24 hours.
          </AppAlert>
          <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <AppInput v-model="form.name" label="Your Name" placeholder="John Doe" required />
              <AppInput v-model="form.email" label="Email" type="email" placeholder="you@example.com" required />
            </div>
            <AppInput v-model="form.subject" label="Subject" placeholder="How can we help?" required />
            <AppTextarea v-model="form.message" label="Message" placeholder="Write your message here..." :rows="5" required />
            <AppButton type="submit" variant="primary" size="lg" :loading="loading" class="w-full">
              Send Message
            </AppButton>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>