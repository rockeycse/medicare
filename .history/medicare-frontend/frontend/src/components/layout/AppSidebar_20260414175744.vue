<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUIStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { ChevronDownIcon, ChevronRightIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'
import type { Component } from 'vue'

export interface MenuItem {
  label: string
  icon: Component
  route?: string
  children?: { label: string; route: string }[]
  badge?: number
}

const props = defineProps<{ menuItems: MenuItem[] }>()

const route = useRoute()
const router = useRouter()
const uiStore = useUIStore()
const authStore = useAuthStore()

const expanded = ref<string[]>([])

const collapsed = computed(() => uiStore.sidebarCollapsed)

function isActive(item: MenuItem): boolean {
  if (item.route) return route.path.startsWith(item.route)
  if (item.children) return item.children.some(c => route.path.startsWith(c.route))
  return false
}

function toggleExpand(label: string) {
  const idx = expanded.value.indexOf(label)
  if (idx === -1) expanded.value.push(label)
  else expanded.value.splice(idx, 1)
}

function isExpanded(label: string) {
  return expanded.value.includes(label)
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <aside
    :class="[
      'flex flex-col bg-slate-900 transition-all duration-300 shrink-0',
      collapsed ? 'w-16' : 'w-64'
    ]"
  >
    <!-- Logo -->
    <div class="flex h-16 items-center gap-3 px-4 border-b border-white/10">
      <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-500">
        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
          <path d="M11 6h2v2h-2zM11 16h2v2h-2z" opacity=".3"/>
          <path d="M13 7h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
        </svg>
      </div>
      <transition name="page">
        <span v-if="!collapsed" class="text-white font-bold text-lg tracking-tight">MediCare</span>
      </transition>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
      <template v-for="item in menuItems" :key="item.label">
        <!-- Item with children -->
        <div v-if="item.children">
          <button
            @click="toggleExpand(item.label)"
            :class="[
              'sidebar-link w-full',
              isActive(item) ? 'text-white' : ''
            ]"
            :title="collapsed ? item.label : ''"
          >
            <component :is="item.icon" class="h-5 w-5 shrink-0" />
            <transition name="page">
              <span v-if="!collapsed" class="flex-1 text-left">{{ item.label }}</span>
            </transition>
            <transition name="page">
              <component
                v-if="!collapsed"
                :is="isExpanded(item.label) ? ChevronDownIcon : ChevronRightIcon"
                class="h-4 w-4"
              />
            </transition>
          </button>
          <transition name="page">
            <div v-if="!collapsed && isExpanded(item.label)" class="mt-1 ml-3 space-y-1 border-l border-white/10 pl-3">
              <router-link
                v-for="child in item.children"
                :key="child.route"
                :to="child.route"
                :class="[
                  'block rounded-lg px-3 py-2 text-xs font-medium transition-colors',
                  route.path === child.route
                    ? 'text-white bg-white/10'
                    : 'text-slate-400 hover:text-white hover:bg-white/5'
                ]"
              >
                {{ child.label }}
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Simple item -->
        <router-link
          v-else-if="item.route"
          :to="item.route"
          :class="['sidebar-link', isActive(item) ? 'active' : '']"
          :title="collapsed ? item.label : ''"
        >
          <component :is="item.icon" class="h-5 w-5 shrink-0" />
          <transition name="page">
            <span v-if="!collapsed" class="flex-1">{{ item.label }}</span>
          </transition>
          <transition name="page">
            <span
              v-if="!collapsed && item.badge"
              class="rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white min-w-[20px] text-center"
            >
              {{ item.badge }}
            </span>
          </transition>
        </router-link>
      </template>
    </nav>

    <!-- Bottom: collapse toggle + logout -->
    <div class="border-t border-white/10 p-2 space-y-1">
      <button
        @click="handleLogout"
        class="sidebar-link w-full text-red-400 hover:text-red-300 hover:bg-red-500/10"
        :title="collapsed ? 'Logout' : ''"
      >
        <ArrowRightOnRectangleIcon class="h-5 w-5 shrink-0" />
        <transition name="page">
          <span v-if="!collapsed">Logout</span>
        </transition>
      </button>
      <button
        @click="uiStore.toggleSidebar()"
        class="sidebar-link w-full"
        :title="collapsed ? 'Expand' : 'Collapse'"
      >
        <svg
          :class="['h-5 w-5 shrink-0 transition-transform', collapsed ? '' : 'rotate-180']"
          fill="none" viewBox="0 0 24 24" stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        <transition name="page">
          <span v-if="!collapsed">Collapse</span>
        </transition>
      </button>
    </div>
  </aside>
</template>