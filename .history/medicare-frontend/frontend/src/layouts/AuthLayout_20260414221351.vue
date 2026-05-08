<script setup lang="ts">
import { ref } from 'vue'
import { useScroll } from '@vueuse/core'
import { useAuthStore } from '@/stores/auth'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import AppFooter from '@/components/layout/AppFooter.vue'

const authStore = useAuthStore()
const mobileOpen = ref(false)
const { y } = useScroll(window)

const navLinks = [
  { label: 'Home', to: '/' },
  { label: 'Doctors', to: '/doctors' },
  { label: 'About', to: '/about' },
  { label: 'Contact', to: '/contact' },
]
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav
      :class="[
        'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
        y > 20
          ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-100'
          : 'bg-transparent'
      ]"
    >
      <div class="mx-auto max-w-7xl px-6">
        <div class="flex h-16 items-center justify-between">
          <!-- Logo -->
          <router-link to="/" class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-500">
              <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M13 7h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
              </svg>
            </div>
            <span :class="['font-bold text-lg', y > 20 ? 'text-slate-800' : 'text-white']">MediCare</span>
          </router-link>

          <!-- Desktop nav links -->
          <div class="hidden md:flex items-center gap-1">
            <router-link
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                y > 20
                  ? 'text-slate-600 hover:text-primary-600 hover:bg-primary-50'
                  : 'text-white/80 hover:text-white hover:bg-white/10'
              ]"
            >
              {{ link.label }}
            </router-link>
          </div>

          <!-- Desktop auth buttons -->
          <div class="hidden md:flex items-center gap-2">
            <template v-if="!authStore.isAuthenticated">
              <router-link
                to="/login"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                  y > 20 ? 'text-slate-600 hover:bg-slate-100' : 'text-white/80 hover:text-white hover:bg-white/10'
                ]"
              >
                Login
              </router-link>
              <router-link
                to="/register"
                class="px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
              >
                Register
              </router-link>
            </template>
            <template v-else>
              <router-link
                :to="authStore.dashboardRoute"
                class="px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
              >
                Dashboard
              </router-link>
            </template>
          </div>

          <!-- Mobile hamburger -->
          <button
            @click="mobileOpen = !mobileOpen"
            :class="['md:hidden p-2 rounded-lg', y > 20 ? 'text-slate-600' : 'text-white']"
          >
            <XMarkIcon v-if="mobileOpen" class="h-6 w-6" />
            <Bars3Icon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <transition name="slide-down">
        <div v-if="mobileOpen" class="md:hidden bg-white border-t border-slate-100 px-6 py-4 space-y-2">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            @click="mobileOpen = false"
            class="block px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50"
          >
            {{ link.label }}
          </router-link>
          <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
            <template v-if="!authStore.isAuthenticated">
              <router-link to="/login" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Login</router-link>
              <router-link to="/register" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium text-center">Register</router-link>
            </template>
            <template v-else>
              <router-link :to="authStore.dashboardRoute" @click="mobileOpen = false" class="block px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium text-center">Dashboard</router-link>
            </template>
          </div>
        </div>
      </transition>
    </nav>

    <!-- Page content -->
    <main class="flex-1">
      <router-view v-slot="{ Component, route }">
        <transition name="page" mode="out-in">
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>

    <AppFooter />
  </div>
</template>