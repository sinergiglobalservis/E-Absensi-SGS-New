<template>
    <ion-page>
        <ion-content :fullscreen="true">

            <div id="bg-container">
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">
                <div class="header">
                    <h2>Daftar Pengajuan</h2>
                </div>
                <ion-card v-for="item in data[0] " :key="item.id">
                    <ion-card-header>
                        <ion-card-title style="color: black;">{{ weekday[new Date(item.absence_date).getDay()] + ', ' + new
                            Date(item.absence_date).getDate() + ' ' + month[(new
                                Date(item.absence_date).getMonth())] + ' ' + new Date(item.absence_date).getFullYear()
                        }}</ion-card-title>
                        <ion-card-subtitle
                            :style="{ 'color': (item.approval_1 == 1 ? 'green' : (item.approval_1 == 2 ? 'red' : '')) }">{{
                                (item.approval_1 == 1 ? 'Disetujui' : (item.approval_1 == 2 ? 'Ditolak' :
                                    'Menunggu')) }}</ion-card-subtitle>
                    </ion-card-header>

                    <ion-card-content>
                        {{ item.absence_name }}
                    </ion-card-content>
                </ion-card>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
// import systems
import { IonContent, IonPage, toastController, IonIcon, IonCard, IonCardHeader, IonCardContent, IonCardSubtitle, IonCardTitle } from '@ionic/vue';
import { defineComponent } from 'vue';
import { arrowBackCircle } from 'ionicons/icons';
import axios from 'axios';


// import components
// import IonButton from "@/components/form/FormButton.vue";
// import IonInput from "@/components/form/FormInput.vue";

export default defineComponent({
    props: [],
    components: { IonContent, IonPage, IonIcon, IonCard, IonCardHeader, IonCardContent, IonCardSubtitle, IonCardTitle },
    data() {
        // const month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        return {
            weekday: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
            month: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            data: [],
            keterangan: null,
            file: null,
            // tahun: new Date().getFullYear(),
            // bulan: month[new Date().getMonth()],
            locate: null,
            coordinates: null
        };
    },
    setup() {
        // console.log('setup');
        return { arrowBackCircle };
    },
    async mounted() {
        this.getData();
        // console.log(process.env.VUE_APP_API_URL)
        // console.log('mounted');
    },
    methods: {
        getData() {
            // hit api
            return axios
                .get(process.env.VUE_APP_API_URL + "trx/absence/getByIdUsers/" + localStorage.getItem('id'), { dataType: "json" })
                .then((response) => {
                    console.log(response)
                    // clearing table
                    this.data = [];
                    // binding data
                    const data = response.data.data;
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
                    await toast.present
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

.header h2 {
    /* margin-left: 1em; */
    text-align: center;
    margin-top: -2em;
    text-shadow: 0 0 3px #a3a3a3;
    margin-bottom: 1em;
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
    font-size: 12px;
    color: var(--ion-color-step-300);
    background: white;
    top: -9px;
    left: 24px;
    padding: 0 1em;
    z-index: 10;
}

ion-col {
    /* background-color: #135d54; */
    border: solid 1px #000000;
    color: #000000;
    text-align: center;
}
</style>
