<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { BellIcon, CheckIcon } from '@heroicons/vue/24/outline'
import { useNotificationStore } from '@/stores/notification'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

const store = useNotificationStore()
const open = ref(false)

onMounted(() => store.fetchNotifications())

function toggle() { open.value = !open.value }

function closeOnOutside() { open.value = false }
</script>

<template>
  <div class="relative" v-click-outside="closeOnOutside">
    <button
      @click="toggle"
      class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors"
    >
      <BellIcon class="h-5 w-5" />
      <span
        v-if="store.unreadCount > 0"
        class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
      >
        {{ store.unreadCount > 9 ? '9+' : store.unreadCount }}
      </span>
    </button>

    <transition name="slide-down">
      <div
        v-if="open"
        class="absolute right-0 top-12 z-50 w-80 rounded-xl border border-slate-100 bg-white shadow-xl"
      >
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
          <h3 class="font-semibold text-slate-800 text-sm">Notifications</h3>
          <button
            v-if="store.unreadCount > 0"
            @click="store.markAllAsRead()"
            class="flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium"
          >
            <CheckIcon class="h-3.5 w-3.5" />
            Mark all read
          </button>
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
          <div
            v-if="!store.notifications.length"
            class="py-10 text-center text-sm text-slate-400"
          >
            No notifications
          </div>
          <div
            v-for="n in store.notifications.slice(0, 5)"
            :key="n.id"
            :class="['px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors', !n.read_at ? 'bg-primary-50/50' : '']"
            @click="store.markAsRead(n.id)"
          >
            <div class="flex gap-3 items-start">
              <div :class="['mt-1 h-2 w-2 rounded-full shrink-0', !n.read_at ? 'bg-primary-500' : 'bg-slate-200']" />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-700 truncate">{{ n.title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ n.message }}</p>
                <p class="text-xs text-slate-300 mt-1">{{ dayjs(n.created_at).fromNow() }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>