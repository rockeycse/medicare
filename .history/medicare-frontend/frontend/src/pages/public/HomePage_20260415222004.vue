<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import doctorService from '@/services/doctor.service'
import type { Doctor } from '@/types/doctor'
import AppButton from '@/components/ui/AppButton.vue'
import AppCard from '@/components/ui/AppCard.vue'
import {
  ShieldCheckIcon,
  BeakerIcon,
  ClockIcon,
  StarIcon,
  CalendarDaysIcon,
  UserGroupIcon,
  HeartIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const doctors = ref<Doctor[]>([])
const loadingDoctors = ref(true)

onMounted(async () => {
  try {
    const res = await doctorService.getAll({ per_page: 6 })
    doctors.value = res.data || []
  } finally {
    loadingDoctors.value = false
  }
})

function handleBooking() {
  if (authStore.isAuthenticated && authStore.isPatient) {
    router.push('/patient/book-appointment')
  } else {
    router.push('/login')
  }
}

const features = [
  {
    icon: ShieldCheckIcon,
    title: 'Expert Doctors',
    desc: 'Our team of certified specialists provides world-class medical care.',
    color: 'bg-blue-50 text-blue-500'
  },
  {
    icon: BeakerIcon,
    title: 'Modern Equipment',
    desc: 'State-of-the-art technology for accurate diagnosis and treatment.',
    color: 'bg-emerald-50 text-emerald-500'
  },
  {
    icon: ClockIcon,
    title: '24/7 Support',
    desc: 'Round-the-clock medical assistance whenever you need it.',
    color: 'bg-purple-50 text-purple-500'
  }
]

const steps = [
  { num: '01', title: 'Register', desc: 'Create your free account in minutes' },
  { num: '02', title: 'Find Doctor', desc: 'Browse our specialist doctors' },
  { num: '03', title: 'Book', desc: 'Choose your preferred time slot' },
  { num: '04', title: 'Consult', desc: 'In-person or online consultation' },
]

const services = [
  { icon: '🫀', name: 'Cardiology' },
  { icon: '🧠', name: 'Neurology' },
  { icon: '🦷', name: 'Dentistry' },
  { icon: '👁️', name: 'Ophthalmology' },
  { icon: '🦴', name: 'Orthopedics' },
  { icon: '🤰', name: 'Gynecology' },
  { icon: '👶', name: 'Pediatrics' },
  { icon: '🧬', name: 'Oncology' },
]

const testimonials = [
  { name: 'Sarah Ahmed', role: 'Patient', text: 'MediCare made booking appointments incredibly easy. The doctors are excellent and the service is top-notch.', rating: 5 },
  { name: 'Rahim Khan', role: 'Patient', text: 'I love how I can access my prescriptions and medical records anytime. Very professional system.', rating: 5 },
  { name: 'Nadia Islam', role: 'Patient', text: 'The online consultation feature saved me so much time. Highly recommend MediCare to everyone.', rating: 5 },
]
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="relative min-h-screen flex items-center bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800 overflow-hidden pt-16">
      <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-10 h-96 w-96 rounded-full bg-primary-400 blur-3xl" />
        <div class="absolute bottom-20 right-10 h-80 w-80 rounded-full bg-sky-400 blur-3xl" />
      </div>
      <div class="relative z-10 mx-auto max-w-7xl px-6 py-24">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 rounded-full bg-primary-500/20 px-4 py-2 mb-8 border border-primary-500/30">
            <div class="h-2 w-2 rounded-full bg-primary-400 animate-pulse" />
            <span class="text-primary-300 text-sm font-medium">Trusted by 50,000+ patients</span>
          </div>
          <h1 class="text-5xl md:text-7xl font-bold text-white leading-tight mb-6">
            Your Health,<br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-sky-300">
              Our Priority
            </span>
          </h1>
          <p class="text-xl text-slate-300 leading-relaxed mb-10 max-w-xl">
            Experience world-class healthcare with seamless appointment booking, digital prescriptions, and expert medical care — all in one place.
          </p>
          <div class="flex flex-wrap gap-4">
            <AppButton size="lg" variant="primary" @click="handleBooking" class="shadow-lg shadow-primary-500/30">
              <CalendarDaysIcon class="h-5 w-5" />
              Book Appointment
            </AppButton>
            <router-link to="/doctors">
              <AppButton size="lg" variant="ghost" class="text-white border border-white/20 hover:bg-white/10">
                Find Doctors
              </AppButton>
            </router-link>
          </div>
          <!-- Stats bar -->
          <div class="mt-16 grid grid-cols-3 gap-6 max-w-lg">
            <div v-for="stat in [{ val: '200+', label: 'Expert Doctors' }, { val: '50K+', label: 'Patients Served' }, { val: '15+', label: 'Years Experience' }]" :key="stat.label">
              <div class="text-3xl font-bold text-white">{{ stat.val }}</div>
              <div class="text-sm text-slate-400 mt-1">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-24 bg-white">
      <div class="mx-auto max-w-7xl px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-slate-800 mb-4">Why Choose MediCare?</h2>
          <p class="text-slate-500 text-lg max-w-xl mx-auto">We combine medical expertise with cutting-edge technology to deliver exceptional healthcare experiences.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="f in features" :key="f.title" class="text-center p-8 rounded-2xl border border-slate-100 hover:shadow-lg transition-all duration-300 group">
            <div :class="['mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl transition-transform group-hover:scale-110', f.color]">
              <component :is="f.icon" class="h-8 w-8" />
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-3">{{ f.title }}</h3>
            <p class="text-slate-500">{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section class="py-24 bg-slate-50">
      <div class="mx-auto max-w-7xl px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-slate-800 mb-4">How It Works</h2>
          <p class="text-slate-500 text-lg">Simple steps to get the care you need</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div v-for="(step, i) in steps" :key="step.num" class="relative text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-500 text-white text-xl font-bold shadow-lg shadow-primary-500/30">
              {{ step.num }}
            </div>
            <div v-if="i < steps.length - 1" class="absolute top-8 left-[calc(50%+2rem)] right-[calc(-50%+2rem)] h-0.5 bg-primary-200 hidden md:block" />
            <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ step.title }}</h3>
            <p class="text-sm text-slate-500">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Doctors -->
    <section class="py-24 bg-white">
      <div class="mx-auto max-w-7xl px-6">
        <div class="flex items-end justify-between mb-12">
          <div>
            <h2 class="text-4xl font-bold text-slate-800 mb-2">Our Top Doctors</h2>
            <p class="text-slate-500">Meet our experienced medical professionals</p>
          </div>
          <router-link to="/doctors" class="text-primary-600 font-medium text-sm hover:text-primary-700">
            View all →
          </router-link>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loadingDoctors" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="rounded-2xl border border-slate-100 p-6 animate-pulse">
            <div class="flex items-center gap-4 mb-4">
              <div class="h-16 w-16 rounded-full bg-slate-200" />
              <div class="flex-1 space-y-2">
                <div class="h-4 bg-slate-200 rounded w-3/4" />
                <div class="h-3 bg-slate-100 rounded w-1/2" />
              </div>
            </div>
            <div class="h-3 bg-slate-100 rounded mb-2" />
            <div class="h-3 bg-slate-100 rounded w-2/3" />
          </div>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="doc in doctors"
            :key="doc.id"
            class="rounded-2xl border border-slate-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
          >
            <div class="flex items-center gap-4 mb-4">
              <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-2xl font-bold text-primary-600 shrink-0">
                {{ doc.user.name.charAt(0) }}
              </div>
              <div>
                <h3 class="font-semibold text-slate-800">Dr. {{ doc.user.name }}</h3>
                <p class="text-sm text-primary-600">{{ doc.specialization?.name }}</p>
              </div>
            </div>
            <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
              <span>{{ doc.experience_years }} yrs exp</span>
              <span>•</span>
              <span>৳{{ doc.consultation_fee }}</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex gap-1">
                <StarIcon v-for="i in 5" :key="i" class="h-4 w-4 text-amber-400 fill-amber-400" />
              </div>
              <AppButton size="sm" variant="primary" @click="handleBooking">
                Book Now
              </AppButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services -->
    <section class="py-24 bg-slate-50">
      <div class="mx-auto max-w-7xl px-6">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-slate-800 mb-4">Our Services</h2>
          <p class="text-slate-500 text-lg">Comprehensive healthcare across all specializations</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div
            v-for="s in services"
            :key="s.name"
            class="bg-white rounded-2xl p-6 text-center hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 border border-slate-100 cursor-pointer"
          >
            <div class="text-4xl mb-3">{{ s.icon }}</div>
            <p class="text-sm font-medium text-slate-700">{{ s.name }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-white">
      <div class="mx-auto max-w-7xl px-6">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-slate-800 mb-4">What Patients Say</h2>
          <p class="text-slate-500 text-lg">Real experiences from our valued patients</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="t in testimonials"
            :key="t.name"
            class="rounded-2xl bg-slate-50 p-8 border border-slate-100"
          >
            <div class="flex gap-1 mb-4">
              <StarIcon v-for="i in t.rating" :key="i" class="h-4 w-4 text-amber-400 fill-amber-400" />
            </div>
            <p class="text-slate-600 mb-6 leading-relaxed">"{{ t.text }}"</p>
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-sm font-bold text-primary-600">
                {{ t.name.charAt(0) }}
              </div>
              <div>
                <p class="font-semibold text-slate-800 text-sm">{{ t.name }}</p>
                <p class="text-xs text-slate-400">{{ t.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-gradient-to-br from-primary-600 to-sky-500">
      <div class="mx-auto max-w-3xl px-6 text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Ready to Book Your Appointment?</h2>
        <p class="text-white/80 text-lg mb-8">Join thousands of patients who trust MediCare for their healthcare needs.</p>
        <div class="flex gap-4 justify-center">
          <router-link to="/register">
            <AppButton size="lg" class="bg-white text-primary-600 hover:bg-slate-50 shadow-lg">
              Get Started Free
            </AppButton>
          </router-link>
          <router-link to="/doctors">
            <AppButton size="lg" variant="ghost" class="text-white border border-white/30 hover:bg-white/10">
              Browse Doctors
            </AppButton>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>