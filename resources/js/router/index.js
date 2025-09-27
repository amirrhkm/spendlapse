import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import MonthDetail from '../components/MonthDetail.vue';
import PrefixDetail from '../components/PrefixDetail.vue';
import PrefixManagement from '../components/PrefixManagement.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import { authService } from '../services/AuthService';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/month/:year/:month',
    name: 'MonthDetail',
    component: MonthDetail,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/prefix/:prefixName',
    name: 'PrefixDetail',
    component: PrefixDetail,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/prefixes',
    name: 'PrefixManagement',
    component: PrefixManagement,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  if (!authService.getCurrentUser()) {
    try {
      await authService.getUser();
    } catch (error) {
      console.log('User not authenticated');
    }
  }

  const isAuthenticated = authService.isAuthenticated();

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;
