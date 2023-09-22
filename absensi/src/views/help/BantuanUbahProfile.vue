<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <ion-header>
                <ion-toolbar>
                    <ion-buttons slot="start">
                        <ion-back-button default-href="#" @click="back()"></ion-back-button>
                    </ion-buttons>
                    <ion-title>Cara Mengubah Data Profil</ion-title>
                </ion-toolbar>
            </ion-header>
            <div class="container">
                <div class="pages">
                    <h5>1. Halaman Dashboard</h5>
                    <img class="gambar" src="@/assets/img/help/dasbor_page.png" alt="img-dashboard">
                    <div class="deskripsi">
                        <p>Pada halaman Dashboard, pilih menu Profile di bawah kanan</p>
                    </div>
                </div>
                <div class="pages">
                    <h5>2. Halaman Perizinan</h5>
                    <img class="gambar" src="@/assets/img/help/profile_page.png" alt="img-cuti-page">
                    <div class="deskripsi">
                        <p>Pada halaman Profile, pilih Ubah Data Profile</p>
                    </div>
                </div>
                <div class="pages">
                    <h5>3. Halaman Ubah Profil</h5>
                    <img class="gambar" src="@/assets/img/help/ubah_profile_page.png" alt="img-cuti-page">
                    <div class="deskripsi">
                        <ul>
                            <li>Pilih data yang akan anda ubah</li>
                            <li>Jika anda tidak ingin merubah password, biarkan kolom password kosong</li>
                            <li>Tekan tombol simpan untuk menyimpan perubahan</li>
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

.pages {
    position: relative;
    width: 100%;
    margin: 10px auto 3em;
}

.pages h5 {
    font-weight: 700;
}

.pages ul li {
    font-size: 14px;
    padding-bottom: 10px;
    margin-left: -2em;
    list-style: square;
}

.pages p {
    font-size: 14px;
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
