import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import HomePage from '../views/menu/HomePage.vue';
import LoginPage from '../views/LoginPage.vue';
import DashboardPage from '../views/DashboardPage.vue';
import AbsensiPage from '../views/transaction/AbsensiPage.vue';
import AbsensiOutPage from '../views/transaction/AbsensiOutPage.vue';
import AbsensiIjinPage from '../views/transaction/AbsensiIjinPage.vue';
import AbsensiCutiPage from '../views/transaction/AbsensiCutiPage.vue';
import AbsensiRekapPage from '../views/transaction/AbsensiRekapPage.vue';
import AbsensiPerizinan from '../views/transaction/AbsensiPerizinan.vue';
import AbsensiSakit from '../views/transaction/AbsensiSakit.vue';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: (localStorage.getItem('token') != null ? '/dashboard' : '/login')
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardPage
  },
  {
    path: '/absensi',
    name: 'Absensi',
    component: AbsensiPage
  },
  {
    path: '/absensi/out',
    name: 'Absensi Out',
    component: AbsensiOutPage
  },
  {
    path: '/absensi/ijin',
    name: 'Ijin',
    component: AbsensiIjinPage
  },
  {
    path: '/absensi/cuti',
    name: 'Cuti',
    component: AbsensiCutiPage
  },
  {
    path: '/absensi/sakit',
    name: 'Sakit',
    component: AbsensiSakit
  },
  {
    path: '/absensi/perizinan',
    name: 'Perizinan',
    component: AbsensiPerizinan
  },
  {
    path: '/absensi/rekap',
    name: 'Rekapitulasi',
    component: AbsensiRekapPage
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
