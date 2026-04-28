import { createRouter, createWebHistory } from 'vue-router'
import Jobs from '../views/Jobs.vue'
import JobDetails from '../views/JobDetails.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'jobs', component: Jobs },
    { path: '/jobs/:id', name: 'job-details', component: JobDetails, props: true },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

