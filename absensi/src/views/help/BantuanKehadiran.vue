<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <ion-header>
                <ion-toolbar>
                    <ion-buttons slot="start">
                        <ion-back-button default-href="#" @click="back()"></ion-back-button>
                    </ion-buttons>
                    <ion-title>Cara Absen Masuk dan Pulang</ion-title>
                </ion-toolbar>
            </ion-header>
            <div class="container">
                <div class="pages">
                    <h5>1. Halaman Dashboard</h5>
                    <img class="gambar" src="@/assets/img/help/dasbor_page.png" alt="kbp_logo">
                    <div class="deskripsi">
                        <p>Pada halaman Dashboard, pilih menu Masuk atau Pulang.</p>
                    </div>
                </div>
                <div class="pages">
                    <h5>2. Halaman Absen Masuk</h5>
                    <img class="gambar" src="@/assets/img/help/masuk_page.png" alt="kbp_logo">
                    <div class="deskripsi">
                        <ul>
                            <li>Setelah masuk ke halaman Absen Masuk, tunggu hingga lokasi saat ini ditemukan</li>
                            <li>Tekan icon kameran untuk membuka kamera, setelah itu foto wajah anda</li>
                            <li>tekan tombol Hadir untuk Absen Masuk</li>
                        </ul>
                    </div>
                </div>
                <div class="pages">
                    <h5>3. Halaman Absen Pulang</h5>
                    <img class="gambar" src="@/assets/img/help/pulang_page.png" alt="kbp_logo">
                    <div class="deskripsi">
                        <ul>
                            <li>Setelah masuk ke halaman Absen Pulang, tunggu hingga lokasi saat ini ditemukan</li>
                            <li>Tekan icon kameran untuk membuka kamera, setelah itu foto wajah anda</li>
                            <li>tekan tombol Pulang untuk Absen Pulang</li>
                        </ul>
                    </div>
                </div>
                <IonButton class="back-button" type="button" @click="back">Mengerti</IonButton>
            </div>
        </ion-content>
    </ion-page>
</template>

<script>
/* eslint-disable */
import { IonContent, IonPage, toastController, loadingController, useBackButton, useIonRouter, IonBackButton, IonButtons, IonHeader, IonTitle, IonToolbar } from '@ionic/vue';
// import IonInput from "../components/form/FormInput.vue";
import IonButton from "@/components/form/FormButton.vue";

export default {
    props: [],
    components: { IonContent, IonPage, IonBackButton, IonButtons, IonHeader, IonTitle, IonToolbar, IonButton },
    methods: {
        back: function () {
            this.$router.go(-1);
        }
    },
}
</script>

<style scoped>
ion-title {
    font-size: 16px;
}

.container {
    width: 90%;
    margin: auto;
}

.pages h5 {
    font-weight: 700;
}

.pages {
    position: relative;
    width: 100%;
    margin: 10px auto 3em;
}

.pages ul li {
    font-size: 14px;
    padding-bottom: 10px;
    margin-left: -2em;
    list-style: square;
}

.gambar {
    width: 50%;
    border-radius: 20px;
    border: 2px solid #000000;
}

.back-button {
    margin-bottom: 20px;
}
</style>
