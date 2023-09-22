import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import LoginPage from '../views/LoginPage.vue';
import HomePage from '../views/menu/HomePage.vue';
import ProfilePage from '../views/menu/ProfilePage.vue';
import UbahProfilePage from '../views/menu/UbahProfilePage.vue';
import BantuanPage from '../views/menu/BantuanPage.vue';
import DashboardPage from '../views/DashboardPage.vue';
import AbsensiPage from '../views/transaction/AbsensiPage.vue';
import AbsensiOutPage from '../views/transaction/AbsensiOutPage.vue';
import AbsensiIjinPage from '../views/transaction/AbsensiIjinPage.vue';
import AbsensiCutiPage from '../views/transaction/AbsensiCutiPage.vue';
import PengajuanPage from '../views/transaction/PengajuanPage.vue';
import AbsensiRekapPage from '../views/transaction/AbsensiRekapPage.vue';
import AbsensiPerizinan from '../views/transaction/AbsensiPerizinan.vue';
import AbsensiSakit from '../views/transaction/AbsensiSakit.vue';
import TabsPage from '../views/TabsPage.vue'
import BantuanKehadiran from '../views/help/BantuanKehadiran.vue';
import BantuanCuti from '../views/help/BantuanCuti.vue';
import BantuanSakit from '../views/help/BantuanSakit.vue';
import BantuanIzin from '../views/help/BantuanIzin.vue';
import BantuanRekap from '../views/help/BantuanRekap.vue';
import BantuanUbahProfile from '../views/help/BantuanUbahProfile.vue';

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
    path: '/',
    component: TabsPage,
    children: [
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: DashboardPage
      },
      
      {
        path: '/home',
        name: 'Home',
        component: HomePage
      },
      {
        path: '/profile',
        name: 'Profile',
        component: ProfilePage
      },
    ]
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
    path: '/absensi/pengajuan',
    name: 'Pengajuan',
    component: PengajuanPage
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
    path: '/ubahProfile',
    name: 'Ubah Profil',
    component: UbahProfilePage
  },
  {
    path: '/bantuan',
    name: 'Bantuan',
    component: BantuanPage
  },
  {
    path: '/bantuanKehadiran',
    name: 'Cara Absen Masuk dan Pulang',
    component: BantuanKehadiran
  },
  {
    path: '/bantuanCuti',
    name: 'Cara Mengajukan Cuti',
    component: BantuanCuti
  },
  {
    path: '/bantuanSakit',
    name: 'Cara Mengajukan Sakit Dengan Surat Dokter',
    component: BantuanSakit
  },
  {
    path: '/bantuanIzin',
    name: 'Cara Mengajukan Izin',
    component: BantuanIzin
  },
  {
    path: '/bantuanRekap',
    name: 'Cara Rekap Absen',
    component: BantuanRekap
  },
  {
    path: '/bantuanUbahProfile',
    name: 'Cara Mengubah Data Akun',
    component: BantuanUbahProfile
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
