<template>
    <div id="container">
        <div class="body-container">
            <div class="login-title center">
                <img src="@/assets/img/sgs_logo.png" />
            </div>
            <div class="form-group">
                <Input type="text" v-model="username" placeholder="Nama Pengguna" />
            </div>
            <div class="form-group">
                <Input type="password" v-model="password" placeholder="Kata Sandi" />
            </div>
            <div class="center">
                <Button type="button" @click="submit">Masuk</Button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Input from '@/components/forms/FormInput.vue';
import Button from '@/components/forms/FormButton.vue';
import toast from '@/assets/js/toast';
import { useLoading } from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';

let loader = useLoading();
export default {
    components: {
        Input,
        Button
    },
    setup() {
    },
    data() {
        return {
            title: 'Login Page',
            username: null,
            password: null
        };
    },
    mounted() { },
    methods: {
        goto: function (comp, p) {
            console.log(comp);
            this.$root.goto(comp);
        },
        validation: function () {
            if (this.username == null || this.username == '') {
                toast.warning("Nama pengguna tidak boleh kosong");
                return false;
            } else if (this.password == null) {
                toast.warning("Kata sandi tidak boleh kosong");
                return false;
            }

            return true;
        },
        submit: async function () {
            if (!this.validation()) return false;

            let post = {
                username: this.username,
                password: this.password
            };
            if (this.username.indexOf("@") > 0) {
                post = {
                    email: this.username,
                    password: this.password
                }
            }

            loader.show({
                loader: 'bars'
            });

            await axios.post(import.meta.env.VITE_API_PATH + 'auth/in', post)
                .then((r) => {
                    if (r.status) {
                        // validasi role akses
                        if (r.data.data.mst_roles_id != 1) {
                            loader.hide();
                            var data = r.data.data
                            // localStorage.setItem("auth",JSON.stringify({'id': data.id, 'email':data.email, 'token':data.access_token}))
                            console.log(data)
                            localStorage.setItem('token', data.access_token)
                            localStorage.setItem('email', data.email)
                            localStorage.setItem('id', data.id)
                            localStorage.setItem('customer_code', data.customer_code)
                            localStorage.setItem('mst_roles_id', data.mst_roles_id)
                            sessionStorage.setItem('page', 'dashboard')
                            location.reload();
                            this.$root.goto('dashboard', { parameter: 'kiriman dari hasil login' });
                        } else {
                            loader.hide();
                            toast.warning('Unauthorized');
                            return false;
                        }
                    }
                }).catch((e) => {
                    loader.hide();
                    const err = e.response.data;
                    toast.warning(err.message);
                });
        }
    }
}
</script>

<style scoped>
#container {
    display: flex;
    place-items: center;
    height: 100vh;
}

#container>* {
    margin: 0 auto;
    padding: 2em;
    background: var(--vt-color-step-950);
    box-shadow: 0 1em 4em var(--vt-color-step-100);
    border-radius: 2em;
}

.login-title {
    font-size: 24px;
}

.login-title img {
    width: 100px;
    height: auto;
}
</style>