import { createRouter, createWebHistory } from 'vue-router'
import Jobs from '../views/Jobs.vue'
import JobDetails from '../views/JobDetails.vue'

import Login from '../views/Login.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'jobs', component: Jobs },
    { path: '/jobs/:id', name: 'job-details', component: JobDetails, props: true },

    { path: '/login', name: 'login', component: Login },

    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})