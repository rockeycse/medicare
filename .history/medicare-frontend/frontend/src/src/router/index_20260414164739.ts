import { createRouter, createWebHistory } from 'vue-router'
import { setupGuards } from './guards'

const routes = [
  // Public + Auth routes
  {
    path: '/',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('@/pages/public/HomePage.vue') },
      { path: 'doctors', name: 'doctors', component: () => import('@/pages/public/DoctorsPage.vue') },
      { path: 'doctors/:id', name: 'doctor-profile', component: () => import('@/pages/public/DoctorProfilePage.vue') },
      { path: 'about', name: 'about', component: () => import('@/pages/public/AboutPage.vue') },
      { path: 'contact', name: 'contact', component: () => import('@/pages/public/ContactPage.vue') },
      { path: 'login', name: 'login', component: () => import('@/pages/auth/LoginPage.vue'), meta: { guestOnly: true } },
      { path: 'register', name: 'register', component: () => import('@/pages/auth/RegisterPage.vue'), meta: { guestOnly: true } },
    ]
  },

  // Admin routes
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      { path: '', redirect: '/admin/dashboard' },
      { path: 'dashboard', name: 'admin-dashboard', component: () => import('@/pages/admin/DashboardPage.vue') },
      { path: 'doctors', name: 'admin-doctors', component: () => import('@/pages/admin/doctors/DoctorListPage.vue') },
      { path: 'doctors/create', name: 'admin-doctor-create', component: () => import('@/pages/admin/doctors/DoctorCreatePage.vue') },
      { path: 'doctors/:id/edit', name: 'admin-doctor-edit', component: () => import('@/pages/admin/doctors/DoctorEditPage.vue') },
      { path: 'patients', name: 'admin-patients', component: () => import('@/pages/admin/patients/PatientListPage.vue') },
      { path: 'users', name: 'admin-users', component: () => import('@/pages/admin/users/UserListPage.vue') },
      { path: 'users/create', name: 'admin-user-create', component: () => import('@/pages/admin/users/UserCreatePage.vue') },
      { path: 'appointments', name: 'admin-appointments', component: () => import('@/pages/admin/appointments/AppointmentListPage.vue') },
      { path: 'invoices', name: 'admin-invoices', component: () => import('@/pages/admin/invoices/InvoiceListPage.vue') },
      { path: 'medical-records', name: 'admin-medical-records', component: () => import('@/pages/admin/medical-records/MedicalRecordListPage.vue') },
    ]
  },

  // Doctor routes
  {
    path: '/doctor',
    component: () => import('@/layouts/DoctorLayout.vue'),
    meta: { requiresAuth: true, role: 'doctor' },
    children: [
      { path: '', redirect: '/doctor/dashboard' },
      { path: 'dashboard', name: 'doctor-dashboard', component: () => import('@/pages/doctor/DashboardPage.vue') },
      { path: 'appointments', name: 'doctor-appointments', component: () => import('@/pages/doctor/AppointmentListPage.vue') },
      { path: 'prescriptions', name: 'doctor-prescriptions', component: () => import('@/pages/doctor/PrescriptionListPage.vue') },
      { path: 'prescriptions/create', name: 'doctor-prescription-create', component: () => import('@/pages/doctor/PrescriptionCreatePage.vue') },
      { path: 'schedule', name: 'doctor-schedule', component: () => import('@/pages/doctor/SchedulePage.vue') },
    ]
  },

  // Patient routes
  {
    path: '/patient',
    component: () => import('@/layouts/PatientLayout.vue'),
    meta: { requiresAuth: true, role: 'patient' },
    children: [
      { path: '', redirect: '/patient/dashboard' },
      { path: 'dashboard', name: 'patient-dashboard', component: () => import('@/pages/patient/DashboardPage.vue') },
      { path: 'book-appointment', name: 'book-appointment', component: () => import('@/pages/patient/BookAppointmentPage.vue') },
      { path: 'appointments', name: 'patient-appointments', component: () => import('@/pages/patient/AppointmentListPage.vue') },
      { path: 'prescriptions', name: 'patient-prescriptions', component: () => import('@/pages/patient/PrescriptionListPage.vue') },
      { path: 'invoices', name: 'patient-invoices', component: () => import('@/pages/patient/InvoiceListPage.vue') },
      { path: 'medical-records', name: 'patient-medical-records', component: () => import('@/pages/patient/MedicalRecordPage.vue') },
    ]
  },

  // Receptionist routes
  {
    path: '/receptionist',
    component: () => import('@/layouts/ReceptionistLayout.vue'),
    meta: { requiresAuth: true, role: 'receptionist' },
    children: [
      { path: '', redirect: '/receptionist/dashboard' },
      { path: 'dashboard', name: 'receptionist-dashboard', component: () => import('@/pages/receptionist/DashboardPage.vue') },
      { path: 'queue', name: 'receptionist-queue', component: () => import('@/pages/receptionist/AppointmentQueuePage.vue') },
      { path: 'invoices/create', name: 'receptionist-invoice-create', component: () => import('@/pages/receptionist/InvoiceCreatePage.vue') },
    ]
  },

  // Error pages
  { path: '/403', name: 'forbidden', component: () => import('@/pages/errors/ForbiddenPage.vue') },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/pages/errors/NotFoundPage.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 })
})

setupGuards(router)

export default router