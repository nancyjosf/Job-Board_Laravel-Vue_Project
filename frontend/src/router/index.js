import { createRouter, createWebHistory } from "vue-router";
import Jobs from "../views/Jobs.vue";
import JobDetails from "../views/JobDetails.vue";

import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Profile from "../views/Profile.vue";
import EmployerDashboard from "../views/EmployerDashboard.vue";
import EmployerApplications from "../views/EmployerApplications.vue";
import AdminDashboard from "../views/AdminDashboard.vue";

const defaultPathByRole = {
  candidate: "/",
  employer: "/employer/dashboard",
  admin: "/admin/dashboard",
};

const getDefaultPathForRole = (role) => defaultPathByRole[role] || "/profile";

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      name: "jobs",
      component: Jobs,
      meta: { requiresAuth: true, roles: ["candidate"] },
    },
    {
      path: "/jobs/:id",
      name: "job-details",
      component: JobDetails,
      props: true,
      meta: { requiresAuth: true, roles: ["candidate"] },
    },
    {
      path: "/employer/dashboard",
      name: "employer-dashboard",
      component: EmployerDashboard,
      meta: { requiresAuth: true, roles: ["employer"] },
    },
    {
      path: "/employer/applications",
      name: "employer-applications",
      component: EmployerApplications,
      meta: { requiresAuth: true, roles: ["employer"] },
    },
    {
      path: "/applications",
      name: "my-applications",
      component: () => import("../views/MyApplications.vue"),
      meta: { requiresAuth: true, roles: ["candidate"] },
    },
    {
      path: "/admin/dashboard",
      name: "admin-dashboard",
      component: AdminDashboard,
      meta: { requiresAuth: true, roles: ["admin"] },
    },

    {
      path: "/login",
      name: "login",
      component: Login,
      meta: { guestOnly: true },
    },
    {
      path: "/register",
      name: "register",
      component: Register,
      meta: { guestOnly: true },
    },
    {
      path: "/profile",
      name: "profile",
      component: Profile,
      meta: { requiresAuth: true },
    },

    {
      path: "/employer/jobs/create",
      name: "create-job",
      component: () => import("../views/CreateJob.vue"),
      meta: { requiresAuth: true, roles: ["employer"] },
    },
    {
      path: "/employer/jobs/:id/edit",
      name: "edit-job",
      component: () => import("../views/EditJob.vue"),
      meta: { requiresAuth: true, roles: ["employer"] },
    },
    { path: "/:pathMatch(.*)*", redirect: "/login" },
  ],
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  const role = localStorage.getItem("user_role");
  const defaultPath = getDefaultPathForRole(role);

  if (to.meta.requiresAuth && !token) {
    next("/login");
  } else if (to.meta.guestOnly && token) {
    next(defaultPath);
  } else if (to.meta.roles && !to.meta.roles.includes(role)) {
    next(defaultPath);
  } else {
    next();
  }
});
