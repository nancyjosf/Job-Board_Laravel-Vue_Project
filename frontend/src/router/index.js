import { createRouter, createWebHistory } from 'vue-router'
import Jobs from '../views/Jobs.vue'
import JobDetails from '../views/JobDetails.vue'

import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'jobs', component: Jobs, meta: { requiresAuth: true } },
    { path: '/jobs/:id', name: 'job-details', component: JobDetails, props: true, meta: { requiresAuth: true } },

    { path: '/login', name: 'login', component: Login, meta: { guestOnly: true } },
    { path: '/register', name: 'register', component: Register, meta: { guestOnly: true } },
    { path: '/profile', name: 'profile', component: Profile, meta: { requiresAuth: true } },

    { path: '/:pathMatch(.*)*', redirect: '/login' },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.meta.guestOnly && token) {
    next('/profile')
  } else {
    next()
  }
})