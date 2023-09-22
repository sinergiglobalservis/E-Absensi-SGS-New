<template>
    <div id="container">
        <div class="body-container">
            <div class="verify-title center">
                <img src="@/assets/img/logo.png" />
            </div>
            <div class="form-group" v-if="verified == null">
                <a @click="verify">Klik disini untuk verifikasi akun anda</a>
            </div>
            <div class="form-group" v-if="verified == 1">
                <p>Silahkan cek kembali email anda untuk melihat username dan password</p>
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
        let url = document.URL;
        const splitToken = url.split("=");
        const getToken = splitToken[1];
        return {
            title: 'Verify Your Account',
            token: getToken,
            verified: null,
        };
    },
    mounted() {
        // console.log(this.token)
    },
    methods: {
        goto: function (comp, p) {
            console.log(comp);
            this.$root.goto(comp);
        },
        verify: async function () {
            loader.show({
                loader: 'bars'
            });

            await axios.get(import.meta.env.VITE_API_PATH + 'mst/user/valid?token=' + this.token)
                .then((r) => {
                    if (r.status == 200) {
                        loader.hide();
                        this.verified = 1;
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

.verify-title {
    font-size: 24px;
}

.verify-title img {
    width: 100px;
    height: auto;
}

.form-group {
    text-align: center;
    padding: 50px;
}

.form-group a {
    font-size: 24px;
    cursor: pointer;
}

.form-group p {
    font-size: 24px;
    /* cursor: pointer; */
}
</style>