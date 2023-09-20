<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="bg-container">
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>
            <div id="container">
                <div id="image-profile">
                    <ion-icon :icon="personCircle"></ion-icon>
                </div>
                <div id="data-profile">
                    <ion-item>
                        <ion-label position="floating">Nama Lengkap</ion-label>
                        <ion-input v-model="profile_name"></ion-input>
                        <ion-input v-model="nik" hidden></ion-input>
                    </ion-item>
                    <ion-item>
                        <ion-label position="floating">Nomor Telepon</ion-label>
                        <ion-input v-model="profile_phone"></ion-input>
                    </ion-item>
                    <ion-item>
                        <ion-select interface="action-sheet" placeholder="Jenis Kelamin" v-model="profile_gender">
                            <ion-select-option value="L">Laki-laki</ion-select-option>
                            <ion-select-option value="P">Perempuan</ion-select-option>
                        </ion-select>
                    </ion-item>
                    <ion-item>
                        <ion-label position="floating">Alamat</ion-label>
                        <ion-textarea placeholder="Type something here" :auto-grow="true" v-model="profile_address">
                        </ion-textarea>
                    </ion-item>

                    <ion-item>
                        <ion-label position="floating">Ubah Password</ion-label>
                        <ion-input v-if="showPassword" type="text" v-model="password" maxlength="8"></ion-input>
                        <ion-input v-else type="password" v-model="password" maxlength="8"></ion-input>
                        <ion-icon class="showPass" :icon="eyeOutline" slot="end" @click="show()"></ion-icon>
                    </ion-item>
                    <p>*Maksimal password 8 karakter</p>
                    <p>*tidak perlu diisi jika tidak merubah password</p>
                </div>
                <div id="button">
                    <ion-button color="primary" @click="submit()">Simpan</ion-button>
                    <!-- <ion-button color="danger" @click="back">Kembali</ion-button> -->
                </div>
            </div>
        </ion-content>
    </ion-page>
</template>

<script>
/* eslint-disable */
import { IonPage, IonContent, IonIcon, IonInput, IonTextarea, IonButton, IonSelect, IonSelectOption, IonItem, IonLabel, useBackButton, useIonRouter, toastController, loadingController, IonIcons } from '@ionic/vue';
import { useRouter } from 'vue-router';
import { App } from '@capacitor/app';
import { mail, logIn, logOut, mailOpen, location, document, personCircle, eyeOutline, arrowBackCircle } from 'ionicons/icons';
import axios from 'axios';

export default {
    components: {
        IonPage,
        IonContent,
        IonIcon,
        IonInput,
        IonTextarea,
        IonButton,
        IonSelect,
        IonSelectOption,
        IonItem,
        IonLabel
    },
    data() {
        return {
            nik: null,
            profile_name: null,
            profile_address: null,
            profile_phone: null,
            profile_gender: null,
            username: null,
            password: null,
            showPassword: false,
        }
    },
    setup() {
        return { logIn, mail, mailOpen, logOut, location, document, personCircle, eyeOutline, arrowBackCircle };
    },
    mounted() {
        this.fetchProfile();
    },
    methods: {
        show() {
            this.showPassword = !this.showPassword;
        },
        back: function () {
            this.$router.go(-1);
        },
        fetchProfile() {
            return axios.get(process.env.VUE_APP_API_URL + 'mst/user/getByUserId/' + localStorage.getItem('id'), {
                dataType: "json",
                headers: {
                    Authorization: 'bearer ' + localStorage.getItem('token')
                }
            })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    this.nik = data.nik;
                    this.profile_name = data.profile_name;
                    this.profile_address = data.profile_address;
                    this.profile_phone = data.profile_phone;
                    this.profile_gender = data.profile_gender;
                    this.username = data.username;
                }).catch((e) => {
                    console.log(e)
                    // const err = e.response.data;
                    // toast.error(err.message);
                });
        },
        submit: async function () {
            // run validation
            if (this.profile_name == null || this.profile_name == '') {
                const toast = await toastController.create({
                    message: 'Nama tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return false;
            } else if (localStorage.getItem('token') == null) {
                const toast = await toastController.create({
                    message: 'Error. Silahkan login',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                this.$router.push('/login');
            }

            const loading = await loadingController
                .create({
                    cssClass: 'my-custom-class',
                    message: 'Please wait...'
                });
            await loading.present();

            if (this.profile_name == null) {
                loading.dismiss()
                const toast = await toastController.create({
                    message: 'Nama tidak boleh kosong',
                    duration: 3000,
                    position: 'bottom'
                });
                await toast.present();
                return false;
            }

            const post = {
                id: localStorage.getItem('id'),
                email: localStorage.getItem('email'),
                nik: this.nik,
                username: this.username,
                profile_name: this.profile_name,
                profile_address: this.profile_address,
                profile_phone: this.profile_phone,
                profile_gender: this.profile_gender,
                updated_by: localStorage.getItem('id'),
                password: this.password
            }
            // console.log(post);
            // loading.dismiss();
            await axios.post(process.env.VUE_APP_API_URL + 'mst/user/edit', post,
                {
                    headers: {
                        Authorization: 'bearer ' + localStorage.getItem('token')
                    }
                }
            ).then(async (res) => {
                if (res.status) {
                    const toast = await toastController.create({
                        message: 'Berhasil mengubah profil',
                        duration: 3000,
                        position: 'bottom'
                    });

                    loading.dismiss()
                    await toast.present();
                    setTimeout(async () => { this.$router.push('/profile'); }, 3000);
                }
            }).catch((e) => {
                loading.dismiss()
                console.log(e)
                // const err = e.response.data;
                // toast.error(err.message);
            });
        },
    },
}
</script>

<style scoped>
#container {
    width: 80%;
    margin: 0 auto;
    padding-bottom: 3em;
}

#bg-container {
    margin-bottom: -4.5em;
}

#bg-container ion-icon {
    margin: 10px 10px;
    font-size: 3em;
    color: grey;
    transition: .2s ease-in;
}

#bg-container ion-icon:hover {
    color: black;
}

#container #image-profile {
    text-align: center;
}

#container #image-profile ion-icon {
    color: rgb(67, 67, 67) !important;
    margin: 0 auto;
    font-size: 8em;
}

#data-profile {
    all: unset;
}

#container #data-profile ion-item ion-label {
    color: #5b5b5b;
    background-color: transparent;
}

#container #data-profile ion-item {
    margin: 1em 0;
    --ion-item-background: transparent;
    /* --ion-background-color: white !important; */
}

#container #data-profile p {
    margin-top: -10px;
    color: red;
    font-size: 12px;
}

.showPass {
    font-size: 2em;
}
</style>