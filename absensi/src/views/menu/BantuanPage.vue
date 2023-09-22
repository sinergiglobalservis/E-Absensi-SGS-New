<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <ion-header>
                <ion-toolbar>
                    <ion-buttons slot="start">
                        <ion-back-button default-href="#" @click="back()"></ion-back-button>
                    </ion-buttons>
                    <ion-title>Bantuan</ion-title>
                </ion-toolbar>
            </ion-header>
            <div class="container">
                <ion-list>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanKehadiran')">
                        <ion-label>Cara absen masuk dan pulang</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanCuti')">
                        <ion-label>Cara pengajuan cuti</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanSakit')">
                        <ion-label>Cara pengajuan sakit dengan surat dokter</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanIzin')">
                        <ion-label>Cara pengajuan izin</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanRekap')">
                        <ion-label>Cara melihat rekap absen</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                    <ion-item class="ion-activatable ripple-parent rounded-rectangle"
                        @click="() => router.push('/bantuanUbahProfile')">
                        <ion-label>Mengubah Profil</ion-label>
                        <ion-ripple-effect></ion-ripple-effect>
                    </ion-item>
                </ion-list>
            </div>
        </ion-content>
    </ion-page>
</template>

<script>
/* eslint-disable */
import { IonPage, IonContent, useBackButton, IonList, IonLabel, IonRippleEffect, IonItem, IonBackButton, IonButtons, IonTitle, IonToolbar, IonHeader } from '@ionic/vue';
import { useRouter } from 'vue-router';
import { App } from '@capacitor/app';
import { } from 'ionicons/icons';

export default {
    components: {
        IonPage, IonContent, IonList, IonLabel, IonRippleEffect, IonItem, IonBackButton, IonButtons, IonTitle, IonToolbar, IonHeader
    },
    setup() {
        const router = useRouter();
        return { router };
    },
    methods: {
        back: function () {
            this.$router.go(-1);
        }
    },
}
</script>

<style scoped>
.container {
    margin: auto;
    /* background-color: red; */
    width: 90%;
    height: 5em;
}

.container h2 {
    font-size: 1.5em;
    font-weight: 700;
}

ion-item {
    --ion-item-background: transparent;
    box-shadow: none !important;
}

ion-list {
    --ion-item-background: transparent;
    box-shadow: none !important;
}

ion-item ion-label {
    /* font-size: 1em !important; */
    font-weight: 400;
}
</style>