import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import MonthDetail from '../components/MonthDetail.vue';
import PrefixManagement from '../components/PrefixManagement.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/month/:year/:month',
    name: 'MonthDetail',
    component: MonthDetail,
    props: true
  },
  {
    path: '/prefixes',
    name: 'PrefixManagement',
    component: PrefixManagement
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
