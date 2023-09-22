<template>
    <ion-page>
        <ion-content :fullscreen="true">

            <div id="container">
                <div class="login-container">
                    <div class="logos">
                        <img src="@/assets/img/sgs_logo.png" />
                    </div>

                    <div class="login-form">
                        <div>
                            <label for="">Nama pengguna</label>
                            <IonInput v-model="username" type="text" full />
                        </div>
                        <div>
                            <label for="">Kata Sandi</label>
                            <IonInput v-model="password" type="password" full />
                        </div>
                        <div class="tx-center">
                            <IonButton type="button" @click.prevent="login">Masuk</IonButton>
                        </div>
                    </div>
                </div>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
import { IonContent, IonPage, toastController, loadingController, useBackButton } from '@ionic/vue';
import IonInput from "../components/form/FormInput.vue";
import IonButton from "../components/form/FormButton.vue";
import axios from 'axios';
import { App } from '@capacitor/app';
import { Device } from '@capacitor/device';


export default {
    props: [],
    components: { IonContent, IonPage, IonInput, IonButton },
    setup() {
        // const ionRouter = useIonRouter();
        useBackButton(-1, () => {
            // if (!ionRouter.canGoBack()) {
            App.exitApp();
            // }
        });

        return {
            validation: async function () {
                if (this.password == null || this.username == '') {
                    const toast = await toastController.create({
                        message: 'Username tidak boleh kosong',
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                    return;
                    // toast.warning("Nama pengguna tidak boleh kosong");
                    // return false;
                } else if (this.password == null || this.password == '') {
                    const toast = await toastController.create({
                        message: 'Password tidak boleh kosong',
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                    return;
                }

                return true;
            },
            useBackButton
        };
    },
    data() {
        return {
            username: null,
            password: null,
            deviceId: null,
        }
    },
    // mounted() {
    // },
    methods: {
        // login: async function(){
        //     this.$router.push('/dashboard');
        // }
        login: async function () {
            // if(!this.validation()) return;
            if (this.username == null || this.username == '') {
                const toast = await toastController.create({
                    message: 'Username tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return;
            } else if (this.password == null || this.password == '') {
                const toast = await toastController.create({
                    message: 'Password tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return;
            }

            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Please wait...',
                });
            await loading.present();


            const deviceInfo = await Device.getId();
            let post = {
                username: this.username,
                password: this.password,
                device_id: deviceInfo.identifier,

            };
            if (this.username.indexOf("@") > 0) {
                post = {
                    email: this.username,
                    password: this.password,
                    device_id: deviceInfo.identifier,
                }
            }

            await axios.post(process.env.VUE_APP_API_URL + 'auth/in', post)
                .then(async (r) => {
                    if (r.status) {
                        const data = r.data.data
                        localStorage.setItem('token', data.access_token)
                        localStorage.setItem('id', data.id)
                        localStorage.setItem('username', data.username)
                        localStorage.setItem('email', data.email)
                        localStorage.setItem('customer_code', data.customer_code)
                        loading.dismiss()
                        const toast = await toastController.create({
                            message: 'Berhasil login',
                            duration: 3000,
                            position: 'top'
                        });
                        await toast.present();
                        this.$router.replace('/dashboard', 'forward');
                    }
                }).catch(async (e) => {
                    loading.dismiss()
                    const err = e.response.data;

                    const toast = await toastController.create({
                        message: err.message,
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                    return;
                });
        }
    }
}
</script>

<style scoped>
#container {
    text-align: center;
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
}

#container .login-container {
    margin: 1em;
    background: white;
    border-radius: 2em;
    box-shadow: 0 0.2em 1em grey;
    max-width: 480px;
    position: relative;
    min-height: 240px;
    padding-top: 60px;
}

#container .login-container .logos {
    background: white;
    position: absolute;
    border-radius: 60px;
    width: 120px;
    height: 120px;
    top: -60px;
    left: calc((100% - 120px) / 2);
    padding: 1em;
}

#container .login-container .logos>img {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    padding: 1em;
    transform: translateY(-50%);
}

#container .login-container .login-form {
    padding: 1em;
}

#container .login-container .login-form div {
    position: relative;
    margin-bottom: 1em;
}

#container .login-container .login-form div label {
    position: absolute;
    font-size: 12px;
    color: grey;
    background: white;
    top: -9px;
    left: 24px;
    padding: 0 1em;
}

#container a {
    text-decoration: none;
}
</style>
