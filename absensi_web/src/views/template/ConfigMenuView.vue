<template>
    <div id="container" v-if="isLogin == 1">
        <ul>
            <template v-if="menu != null">
                <div class="dropdown">
                    <div id="logo-container" class="dropbtn collapsed" data-bs-toggle="collapse"
                        data-bs-target="#profile-menu" aria-expanded="true">
                        <img src="@/assets/img/profile.png" />
                    </div>
                    <div class="dropdown-content">
                        <li class="collapse ms-3" id="profile-menu">
                            <div>
                                <ul>
                                    <li v-for="(v, k) in menu" :key="k" @click="goto(v)">{{ v }}</li>
                                    <li @click="logout()">Logout</li>
                                </ul>
                            </div>
                        </li>
                    </div>
                </div>
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
    data() {
        return {
            menu: ['profile'],
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
        logout: async function () {
            // localStorage.clear();
            // location.reload();
            loader.show({
                loader: 'bars'
            });
            const post = {
                token: localStorage.getItem('token')
            }

            await axios.post(import.meta.env.VITE_API_PATH + 'auth/out', post)
                .then((r) => {
                    if (r.status) {
                        loader.hide();
                        // localStorage.clear();
                        localStorage.removeItem('token');
                        localStorage.removeItem('id');
                        localStorage.removeItem('email');
                        localStorage.removeItem('username');
                        localStorage.removeItem('mst_roles_id');
                        localStorage.removeItem('customer_code');
                        sessionStorage.clear();
                        location.reload();
                    }
                }).catch((e) => {
                    // if error / fail then show response
                    loader.hide();
                    const err = e.response.data;
                    toast.error(err.message);
                });
        },
        logged(e) {
            console.log(e)
        }
    }
}
</script>

<style scoped>
#container {
    text-transform: capitalize;
    cursor: pointer;
    min-width: 2em;
    position: absolute;
    top: 1em;
    right: 1em;
    /* background-color: red; */
    background-color: var(--vt-color-step-950);
    padding: .5em;
    border-radius: .8em;
    box-shadow: 0 .5em 1em var(--vt-color-step-800);
    /* overflow: auto; */
    z-index: 100;
}

#container #logo-container {
    text-align: center;
}

#container #logo-container img {
    width: 3em;
    height: auto;
}

#container ul {
    margin: 0;
    padding: 0;
}

#container ul li {
    list-style: none;
    /* margin: .2em 0; */
    padding: .2em .4em;
    border-radius: 1em 0 0 1em;
    /* box-shadow: .4em .2em 1em var(--vt-color-step-800); */
    font-size: 14px;
    cursor: pointer;
    transition: all 0.5s ease-out;
    /* background: rgb(183, 146, 255); */
    /* background: linear-gradient(130deg, rgba(183, 146, 255, 1) 76%, rgba(157, 118, 231, 1) 100%); */
    color: black;
    text-transform: capitalize;
    font-weight: 400;
}

#container ul li:hover {
    color: var(--vt-color-step-400);
    /* background-color: var(--vt-color-step-950); */
}


/* dropdown */
/* Style The Dropdown Button */
.dropbtn {
    color: white;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
    position: relative;
    display: inline-block;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
    /* padding: .4em; */
    border-radius: .2em;
    margin-top: 1em;
    margin-right: -.4em;
    right: 0;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {
    background-color: #f1f1f1
}

/* Show the dropdown menu on hover */
.dropdown:hover .dropdown-content {
    display: block;
}
</style>