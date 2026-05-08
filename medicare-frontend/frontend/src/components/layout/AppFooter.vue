<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <footer class="border-t border-slate-100 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center gap-2 mb-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-500">
              <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M13 7h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
              </svg>
            </div>
            <span class="font-bold text-slate-800 text-lg">MediCare</span>
          </div>
          <p class="text-sm text-slate-500 max-w-xs">
            Your trusted hospital management system for seamless healthcare experiences.
          </p>
        </div>
        <div>
          <h4 class="font-semibold text-slate-700 mb-3 text-sm">Quick Links</h4>
          <ul class="space-y-2">
            <li><router-link to="/" class="text-sm text-slate-400 hover:text-primary-500 transition-colors">Home</router-link></li>
            <li><router-link to="/doctors" class="text-sm text-slate-400 hover:text-primary-500 transition-colors">Find Doctors</router-link></li>
            <li><router-link to="/about" class="text-sm text-slate-400 hover:text-primary-500 transition-colors">About Us</router-link></li>
            <li><router-link to="/contact" class="text-sm text-slate-400 hover:text-primary-500 transition-colors">Contact</router-link></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-slate-700 mb-3 text-sm">Contact</h4>
          <ul class="space-y-2">
            <li class="text-sm text-slate-400">📞 +880 1700-000000</li>
            <li class="text-sm text-slate-400">📧 info@medicare.com</li>
            <li class="text-sm text-slate-400">📍 Dhaka, Bangladesh</li>
          </ul>
        </div>
      </div>
      <div class="mt-8 border-t border-slate-100 pt-6 text-center">
        <p class="text-sm text-slate-400">© {{ year }} MediCare. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>