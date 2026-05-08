@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  * {
    @apply border-slate-200;
  }

  body {
    @apply font-sans text-slate-700 antialiased;
    font-feature-settings: 'cv11', 'ss01';
  }

  h1, h2, h3, h4, h5, h6 {
    @apply font-semibold text-slate-800;
  }
}

@layer components {
  .page-title {
    @apply text-2xl font-bold text-slate-800;
  }

  .section-title {
    @apply text-lg font-semibold text-slate-700;
  }

  .label-text {
    @apply text-xs font-medium text-slate-500 uppercase tracking-wide;
  }

  .input-base {
    @apply w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700
           placeholder-slate-400 shadow-sm transition-all duration-200
           focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20
           disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400;
  }

  .btn-base {
    @apply inline-flex items-center justify-center gap-2 rounded-lg font-medium
           transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2
           disabled:cursor-not-allowed disabled:opacity-50;
  }

  .card {
    @apply bg-white rounded-xl shadow-sm border border-slate-100;
  }

  .sidebar-link {
    @apply flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
           text-slate-400 transition-all duration-200 hover:bg-white/10 hover:text-white;
  }

  .sidebar-link.active {
    @apply bg-primary-500 text-white shadow-lg shadow-primary-500/25;
  }
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  @apply bg-slate-100;
}
::-webkit-scrollbar-thumb {
  @apply bg-slate-300 rounded-full;
}
::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-400;
}

/* Transitions */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.page-enter-from {
  opacity: 0;
  transform: translateY(8px);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.slide-right-enter-from {
  transform: translateX(100%);
  opacity: 0;
}
.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}