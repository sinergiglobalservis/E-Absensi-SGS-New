<template>
    <ion-page>
        <ion-content :fullscreen="true">

            <div id="bg-container">
                <!-- <img src="@/assets/img/bg_menu/rekap.jpg" alt="Background image" /> -->
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">

                <div class="header">
                    <h2>Rekap Absensi</h2>
                </div>
                <!-- <div class="row"> -->
                <div class="form mulai">
                    <label>Mulai</label>
                    <IonInput type="date" :min="minDate" v-model="mulai" full />
                </div>
                <div class="form sampai">
                    <label>Sampai</label>
                    <IonInput type="date" v-model="sampai" full />
                </div>
                <div class="tx-center">
                    <IonButton type="button" @click="getData">Lihat</IonButton>
                </div>
                <ion-card v-for="item in data[0] " :key="item.id">
                    <ion-card-header>
                        <ion-card-title style="color: black;">{{ weekday[new Date(item.attendee_date).getDay()] + ', ' + new
                            Date(item.attendee_date).getDate() + ' ' + month[(new
                                Date(item.attendee_date).getMonth())] + ' ' + new Date(item.attendee_date).getFullYear()
                        }}</ion-card-title>
                        <ion-card-subtitle>{{ (item.attendee_time_in == '00:00:00' ? (item.approval_1 == 0 ? 'Menunggu' :
                            (item.approval_1 == 1 ? 'Disetujui' : (item.approval_1 == 2 ? 'Ditolak' : 'Libur Nasional'))) :
                            'Masuk : ' +
                            item.attendee_time_in
                            + ' | ' + 'Pulang : ' +
                            (item.attendee_time_out == null ? '' : item.attendee_time_out))
                        }}</ion-card-subtitle>
                    </ion-card-header>

                    <ion-card-content :style="{ 'color': (item.absence_ref != null ? 'red' : '') }">
                        {{ (item.absence_ref != null ? 'Terlambat ' + item.late_duration.slice(0, -3) : item.deskripsi) }}
                    </ion-card-content>
                </ion-card>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
// import systems
import { IonContent, IonPage, toastController, loadingController, IonCol, IonGrid, IonRow, IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent } from '@ionic/vue';
import axios from 'axios';
import { arrowBackCircle } from 'ionicons/icons';


import { defineComponent } from 'vue';


// import components
import IonButton from "@/components/form/FormButton.vue";
import IonInput from "@/components/form/FormInput.vue";
import IonSelect from "@/components/form/FormSelect.vue";

export default defineComponent({
    props: [],
    components: {
        IonContent, IonPage, IonButton, IonInput, IonIcon,
        IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
        // IonGrid, IonRow, IonCol
    },
    data() {
        return {
            data: [],
            keterangan: null,
            file: null,
            minDate: null,
            mulai: null,
            sampai: null,
            weekday: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
            month: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            locate: null,
            coordinates: null
        };
    },
    setup() {
        // console.log('setup');
        return { arrowBackCircle };
    },
    async mounted() {
        this.getCutoff();
    },
    methods: {
        getCutoff() {
            return axios
                .get(process.env.VUE_APP_API_URL + "mst/masterCutoff/getByCustomerCode/" + localStorage.getItem('customer_code'), {
                    dataType: "json",
                })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    console.log(data)
                    this.mulai = new Date().getFullYear() + '-' + ('0' + new Date().getMonth()).slice(-2) + '-' + data.cutoff_start;
                    this.sampai = new Date().getFullYear() + '-' + ('0' + (new Date().getMonth() + 1)).slice(-2) + '-' + data.cutoff_end;
                    this.minDate = new Date().getFullYear() + '-' + ('0' + (new Date().getMonth() - 1)).slice(-2) + '-' + data.cutoff_start;
                })
                .catch(async (e) => {
                    // if error then show response
                    const err = e.response.data;
                    const toast = await toastController.create({
                        message: err.message,
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                });
        },
        async getData() {
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Mohon tunggu...'
                });
            await loading.present();
            // clearing table
            this.data = [];

            const post = {
                users_id: localStorage.getItem('id'),
                start: this.mulai,
                end: this.sampai
            }

            // hit api
            return axios
                .post(process.env.VUE_APP_API_URL + "trx/attendee/getRekapByPeriode", post)
                .then(async (response) => {

                    loading.dismiss();
                    const data = response.data.data;
                    if (data.length == 0) {
                        const toast = await toastController.create({
                            message: 'Data Kosong',
                            duration: 3000,
                            position: 'top'
                        });
                        await toast.present();
                    }
                    // binding data
                    this.data.push(data);
                    // console.log(response.data);
                })
                .catch(async (e) => {
                    // if error then show response
                    const err = e.response.data;
                    const toast = await toastController.create({
                        message: err.message,
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                });
        },
        back: function () {
            this.$router.back();
        }
    }
});
</script>

<style scoped>
#bg-container {
    position: absolute;
    height: calc(240px + 2em);
    top: 0;
    left: 0;
    right: 0;
    overflow: hidden;
    z-index: -1;
    text-align: center;
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


#dashboard-container {
    background: white;
    height: calc(100% + 2em);
    margin-top: 100px;
    border-radius: 2em 2em 0 0;
    padding: 3em 1em 1em 1em;
    box-shadow: 0 -0.2em 1em grey;
    color: black;
    overflow: auto;
}

#dashboard-container>* {
    margin: 1em 0;
}

#dashboard-container .form {
    position: relative;
}

#dashboard-container .form>label {
    position: absolute;
    font-size: 14px;
    color: var(--ion-color-step-300);
    background: white;
    top: -9px;
    /* left: 24px; */
    padding: 0 1em;
    z-index: 10;
}

ion-col {
    /* background-color: #135d54; */
    border: solid 1px #000000;
    color: #000000;
    text-align: center;
}

.header h2 {
    /* margin-left: 1em; */
    text-align: center;
    margin-top: -2em;
    text-shadow: 0 0 3px #a3a3a3;
    margin-bottom: 1em;
}

.row {
    display: grid;
    grid-template-columns: auto auto auto auto;
    /* grid-gap: 10px; */
    /* padding: 20px; */
    justify-content: center;
}
</style>
