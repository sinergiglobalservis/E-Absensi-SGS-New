<template>
  <component :is="activeRoute" :params="params"></component>
  <component :is="mainMenu" :menu="menus"></component>
  <component :is="configMenu" :menu="configMenus"></component>
</template>

<script>
import { markRaw } from 'vue';
import Login from '@/views/auth/LoginView.vue';
import Verify from '@/views/auth/VerifyView.vue';
import MainMenu from '@/views/template/MainMenuView.vue';
import ConfigMenu from '@/views/template/ConfigMenuView.vue';
import Dashboard from '@/views/DashboardView.vue';
import Attendee from '@/views/pages/AttendeeView.vue';
import Users from '@/views/pages/UsersView.vue';
import Cuti from '@/views/pages/CutiView.vue';
import MasterAbsence from '@/views/pages/MasterAbsenceView.vue';
import MasterRoles from '@/views/pages/MasterRolesView.vue';
import MasterCustomer from '@/views/pages/MasterCustomerView.vue';
// import MasterWorkhour from '@/views/pages/MasterWorkhourView.vue';
import MasterSchedule from '@/views/pages/MasterScheduleView.vue';
import MasterDepartemen from '@/views/pages/MasterDepartemenView.vue';
import Summary from '@/views/pages/SummaryView.vue';
import Holiday from '@/views/pages/HolidayView.vue';
import Profile from '@/views/pages/ProfileView.vue';
import ScheduleView from './views/pages/ScheduleView.vue';
import CustomerScheduleView from './views/pages/CustomerScheduleView.vue';

const routeComponent = {
  // menu
  'login': Login,
  'verify': Verify,
  'dashboard': Dashboard,
  'karyawan': Users,
  'kehadiran': Attendee,
  'perizinan': Cuti,
  'summary': Summary,
  'libur nasional': Holiday,
  'jadwal perusahaan': CustomerScheduleView,
  'jadwal karyawan': ScheduleView,

  // submenu master
  'jenis absensi': MasterAbsence,
  'role akses': MasterRoles,
  'customer': MasterCustomer,
  'jadwal kerja': MasterSchedule,
  // 'jadwal kerja': MasterWorkhour,
  'departemen': MasterDepartemen,

  // config menu
  'profile': Profile
};

const currentUrl = window.location.pathname;

export default {
  data() {
    return {
      activeRoute: (sessionStorage.getItem('page') != null && localStorage.getItem('token') != null ? markRaw(routeComponent[sessionStorage.getItem('page')]) : (sessionStorage.getItem('page') == null && localStorage.getItem('token') != null ? markRaw(routeComponent['dashboard']) : (localStorage.getItem('token') == null && currentUrl == '/verify' ? markRaw(routeComponent['verify']) : markRaw(routeComponent['login'])))),
      mainMenu: markRaw(MainMenu),
      configMenu: markRaw(ConfigMenu),
      params: null,
      menus: routeComponent,
      configMenus: routeComponent
    };
  },
  // beforeMount() {
  //   console.log(location)
  //   if (window.location.protocol !== "https:") {
  //     window.location = "https://" + import.meta.env.VITE_DOMAIN;
  //     // exit;
  //     throw new Error('Wrong Path Protocol');
  //   }
  // },
  methods: {
    goto: function (comp, p) {
      if (typeof p != 'undefined') {
        this.params = p;
      }

      this.activeRoute = markRaw(routeComponent[comp]);
    }
  }
}
</script>


<style scoped></style>
