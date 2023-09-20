<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="container">
                <div id="image-profile">
                    <ion-icon :icon="personCircle"></ion-icon>
                </div>
                <!-- <div class="heading">
                    <h2>Hi, Rizky</h2>
                </div> -->
                <div id="list">
                    <ion-list lines="full">
                        <ion-item @click="() => router.push('/ubahProfile')">
                            <ion-icon :icon="settingsOutline"></ion-icon>
                            <ion-label>Ubah Data Profile</ion-label>
                        </ion-item>
                        <ion-item @click="() => router.push('/bantuan')">
                            <ion-icon :icon="helpCircleOutline"></ion-icon>
                            <ion-label>Bantuan</ion-label>
                        </ion-item>
                        <ion-item @click="logout()">
                            <ion-icon :icon="logOutOutline"></ion-icon>
                            <ion-label>Keluar</ion-label>
                        </ion-item>
                    </ion-list>
                </div>
            </div>
        </ion-content>
    </ion-page>
</template>

<script>
import { IonPage, IonContent, IonIcon, IonItem, IonLabel, IonList, toastController, loadingController, useBackButton, useIonRouter } from '@ionic/vue';
import { useRouter } from 'vue-router';
import { App } from '@capacitor/app';
import { personCircle, settingsOutline, logOutOutline, helpCircleOutline } from 'ionicons/icons';
import axios from 'axios';

export default {
    components: {
        IonPage,
        IonContent,
        IonIcon,
        IonItem,
        IonLabel,
        IonList
    },
    setup() {
        const ionRouter = useIonRouter();
        useBackButton(-1, () => {
            if (!ionRouter.canGoBack()) {
                App.exitApp();
            }
        });

        const router = useRouter();
        return { useBackButton, router, personCircle, settingsOutline, logOutOutline, helpCircleOutline };
    },
    methods: {
        logout: async function () {
            const loading = await loadingController
                .create({
                    message: 'Harap tunggu...',
                    spinner: 'circles',
                });
            loading.present();

            const post = {
                token: localStorage.getItem('token')
            }

            await axios.post(process.env.VUE_APP_API_URL + 'auth/out', post)
                .then(async (r) => {
                    if (r.status) {
                        this.$router.replace('/login', 'forward');
                        localStorage.clear();
                        const toast = await toastController.create({
                            message: 'Berhasil logout',
                            duration: 3000,
                            position: 'top'
                        });
                        await toast.present();
                        loading.dismiss();
                    }
                }).catch((e) => {
                    // if error / fail then show response
                    const err = e.response.data;
                    loading.dismiss();
                });
        }
    },
}
</script>

<style scoped>
#container {
    width: 100%;
    margin: 0 auto;
}

#container #image-profile {
    text-align: center;
}

#container #image-profile ion-icon {
    color: rgb(67, 67, 67) !important;
    margin: 0 auto;
    font-size: 8em;
}

#list {
    --ion-item-background: #bbe0e0;
}

ion-item ion-icon {
    color: rgb(58, 58, 58) !important;
    margin: 0 .5em 0 0;
}

ion-label {
    --color: rgb(58, 58, 58) !important;
}

.heading h2 {
    text-align: center;
    font-weight: 600;
}
</style>