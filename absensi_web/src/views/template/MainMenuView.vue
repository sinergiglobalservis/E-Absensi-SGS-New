<template>
    <div id="container" v-if="isLogin == 1">
        <div id="logo-container">
            <img src="@/assets/img/sgs_logo.png" />
        </div>
        <ul>
            <template v-if="menu != null">
                <li v-for="(v, k) in menu" :key="k" @click="goto(v)">{{ v }}</li>
                <li v-if="mst_roles_id == 2" class="collapsed" data-bs-toggle="collapse" data-bs-target="#menu-master"
                    aria-expanded="true">
                    Master
                </li>
                <li class="collapse ms-3" id="menu-master">
                    <div>
                        <ul>
                            <li v-for="(v, k) in master" :key="k" @click="goto(v)">{{ v }}</li>
                        </ul>
                    </div>
                </li>
            </template>
        </ul>
    </div>
</template>

<script>
import toast from '@/assets/js/toast';
import axios from 'axios';

import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    props: {
        // menu: {
        //     default:null
        // }
    },
    mounted() {
        // console.log(this.menu)
    },
    data() {
        return {
            mst_roles_id: localStorage.getItem('mst_roles_id'),
            menu: (localStorage.getItem('mst_roles_id') == 2 ? [
                'dashboard',
                'karyawan',
                'kehadiran',
                'perizinan',
                'summary',
                'libur nasional',
                'jadwal perusahaan',
                'jadwal karyawan'
            ] : ['kehadiran', 'atur jadwal']),
            master: [
                'jenis absensi',
                'customer',
                'role akses',
                'jadwal kerja',
                'departemen'
            ],
            isLogin: (localStorage.getItem('token') != null ? 1 : 0),
            activemenu: null
        };
    },
    methods: {
        goto: function (comp) {
            if (localStorage.getItem('token') != null) {
                this.$root.goto(comp);
                sessionStorage.setItem('page', comp);
            } else {
                this.$root.goto('login')
            }
        },
        logged(e) {
            console.log(e)
        }
    }
}
</script>

<style scoped>
#container {
    min-width: 15em;
    position: fixed;
    top: 1em;
    left: 1em;
    background-color: var(--vt-color-step-950);
    padding: 1em;
    border-radius: 1em;
    box-shadow: 0 .5em 1em var(--vt-color-step-800);
    height: calc(100% - 2em);
    overflow: auto;
    z-index: 100;
}

#container>* {
    margin: .3em 0;
}

#container #logo-container {
    text-align: center;
}

#container #logo-container img {
    width: 13em;
    height: auto;
}

#container ul {
    margin: 0;
    padding: 0;
}

#container ul li {
    list-style: none;
    margin: .2em 0;
    padding: .2em .4em;
    border-radius: 0 1em 1em 0;
    box-shadow: .4em .2em 1em var(--vt-color-step-800);
    font-size: 14px;
    cursor: pointer;
    transition: all 0.5s ease-out;
    /* background: #4F709C; */
    background: linear-gradient(130deg, #4F709C 76%, #4F709C 100%);
    color: var(--vt-color-step-950);
    text-transform: capitalize;
    font-weight: 400;
}

#container ul li:hover {
    color: var(--vt-color-step-400);
    background-color: var(--vt-color-step-950);
}

#menu-master ul li {
    background: linear-gradient(130deg, rgb(33, 58, 68) 80%, rgb(238, 230, 255) 100%);
    /* color: black; */
}
</style>