import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
// import router from './router';

// date picker
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import '@/assets/css/custom-datepicker.css';

import './assets/main.css';
import 'vue3-toastify/dist/index.css';

// bootstrap
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.min';

// swal
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// datatable
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// vue select
import vSelect from 'vue-select' //import vue-select
import "vue-select/dist/vue-select.css"; //import css vue-select
import '@/assets/css/custom-vue-select.css';

const app = createApp(App);

app.use(createPinia());
app.use(VueSweetalert2);
// app.use(router);

// added component
app.component('VueDatePicker', VueDatePicker);
app.component('EasyDataTable', Vue3EasyDataTable);
app.component('v-select', vSelect);


app.mount('#app');
