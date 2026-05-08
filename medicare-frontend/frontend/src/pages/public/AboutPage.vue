<script setup lang="ts">
const team = [
  { name: 'Dr. Kamal Hossain', role: 'Chief Medical Officer', dept: 'Administration' },
  { name: 'Dr. Fatema Begum', role: 'Head of Cardiology', dept: 'Cardiology' },
  { name: 'Dr. Rafiqul Islam', role: 'Head of Neurology', dept: 'Neurology' },
  { name: 'Dr. Sonia Akter', role: 'Head of Pediatrics', dept: 'Pediatrics' },
]
const stats = [
  { val: '15+', label: 'Years of Excellence' },
  { val: '200+', label: 'Expert Doctors' },
  { val: '50K+', label: 'Happy Patients' },
  { val: '98%', label: 'Patient Satisfaction' },
]
</script>

<template>
  <div class="pt-16 min-h-screen">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-primary-700 to-sky-600 py-24">
      <div class="mx-auto max-w-4xl px-6 text-center text-white">
        <h1 class="text-5xl font-bold mb-4">About MediCare</h1>
        <p class="text-xl text-white/80 leading-relaxed">
          We are committed to providing exceptional healthcare services with compassion,
          innovation, and excellence at the heart of everything we do.
        </p>
      </div>
    </div>

    <!-- Mission -->
    <section class="py-24 bg-white">
      <div class="mx-auto max-w-7xl px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div>
            <h2 class="text-4xl font-bold text-slate-800 mb-6">Our Mission</h2>
            <p class="text-slate-600 text-lg leading-relaxed mb-6">
              MediCare was founded with a single mission: to make quality healthcare accessible to everyone.
              We believe that every person deserves the best medical care, regardless of where they are.
            </p>
            <p class="text-slate-600 leading-relaxed">
              Through cutting-edge technology and a team of dedicated professionals,
              we're transforming how patients access and experience healthcare in Bangladesh and beyond.
            </p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="stat in stats" :key="stat.val"
              class="rounded-2xl bg-primary-50 p-8 text-center border border-primary-100">
              <div class="text-4xl font-bold text-primary-600 mb-2">{{ stat.val }}</div>
              <div class="text-sm text-slate-600">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team -->
    <section class="py-24 bg-slate-50">
      <div class="mx-auto max-w-7xl px-6">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-slate-800 mb-4">Our Leadership Team</h2>
          <p class="text-slate-500 text-lg">Experienced professionals dedicated to your health</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="member in team" :key="member.name"
            class="bg-white rounded-2xl p-6 text-center border border-slate-100 hover:shadow-md transition-all">
            <div class="mx-auto mb-4 h-20 w-20 rounded-full bg-primary-100 flex items-center justify-center text-2xl font-bold text-primary-600">
              {{ member.name.charAt(0) }}
            </div>
            <h3 class="font-semibold text-slate-800">{{ member.name }}</h3>
            <p class="text-sm text-primary-600 mt-1">{{ member.role }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ member.dept }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>