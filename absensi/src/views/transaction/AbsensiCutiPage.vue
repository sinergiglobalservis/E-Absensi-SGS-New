<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="bg-container">
                <!-- <img src="@/assets/img/bg_menu/cuti.jpg" class="image-selfie" alt="selfie image" /> -->
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">
                <div class="header">
                    <h2>Form Cuti</h2>
                </div>
                <div class="form">
                    <h5>Sisa cuti Anda : {{ sisaCuti }}</h5>
                </div>
                <div class="form">
                    <label><span>*</span> Jenis Cuti</label>
                    <IonSelect v-model="jenis" :options="jenisOptions" full />
                </div>
                <div class="form">
                    <label><span>*</span> Mulai</label>
                    <IonInput type="date" v-model="startDate"
                        @change="dateStrict(); checkDate(); getHoliday(); checkRemainingLeave();" full />
                </div>
                <div class="form">
                    <label><span>*</span> Sampai</label>
                    <IonInput type="date" v-model="endDate"
                        @change="dateStrict(); checkDate(); getHoliday(); checkRemainingLeave();" full />
                </div>
                <div class="form">
                    <label>Total Hari</label>
                    <IonInput type="text" v-model="absence_duration" readonly full />
                </div>
                <div class="form">
                    <label><span>*</span> Keterangan</label>
                    <IonTextarea v-model="keterangan" full />
                </div>

                <div class="form" v-if="jenis != null">
                    <ion-item lines="none">
                        <ion-label slot="end">Tambah Lampiran</ion-label>
                        <IonCheckbox @click="showInputAttachment()" />
                    </ion-item>
                </div>

                <div v-if="imgDocs != null" class="img-document">
                    <img :src="imgDocs" alt="Foto Dokumen" />
                </div>

                <div v-if="attachment == true" class="btn-camera" @click.prevent="takePic">
                    <label>Ambil gambar</label>
                    <img src="@/assets/img/cam.png" alt="Camera" />
                </div>

                <div class="tx-center">
                    <IonButton v-if="startDate != null && endDate != null && jenis != null && keterangan != null"
                        type="button" @click="submit">
                        Kirimkan</IonButton>
                </div>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
/* eslint-disable */
// import systems
import { IonContent, IonPage, toastController, loadingController, IonDatetimeButton, IonModal, IonDatetime, IonCheckbox, IonItem, IonLabel, IonIcon } from '@ionic/vue';
import axios from 'axios';
import { arrowBackCircle } from 'ionicons/icons';
import { Camera, CameraResultType } from '@capacitor/camera';


// import components
import IonTextarea from "@/components/form/FormTextarea.vue";
import IonInput from "@/components/form/FormInput.vue";
import IonSelect from "@/components/form/FormSelect.vue";
import IonButton from "@/components/form/FormButton.vue";

export default {
    props: [],
    components: { IonContent, IonPage, IonButton, IonInput, IonTextarea, IonSelect, IonCheckbox, IonItem, IonLabel, IonIcon },
    data() {
        return {
            tanggal_pengajuan: null,
            keterangan: null,
            file: null,
            startDate: null,
            endDate: null,
            jenis: null,
            jenisOptions: [],
            attachment: false,
            absence_duration: null,
            sisaCuti: null,
            imgDocs: null,
            dateLeaves: [],
            totalHoliday: null,
            holidayDate: [],
        };
    },
    setup() {
        // camera setup
        (async () => {
            Camera.checkPermissions().then(async (r) => {
                const toast = await toastController.create({
                    message: r.camera,
                    duration: 3000,
                    position: 'bottom'
                });
                // await toast.present();
                if (r.camera == 'denied') {
                    Camera.requestPermissions();
                }
            }).catch(async (e) => {
                const toast = await toastController.create({
                    message: e,
                    duration: 3000,
                    position: 'bottom'
                });
                await toast.present();
            });
        })();

        const takePicture = async () => {
            return await Camera.getPhoto({
                quality: 90,
                width: 1080,
                height: 1920,
                allowEditing: false,
                resultType: CameraResultType.Uri,
                direction: 'FRONT',
                source: 'CAMERA'
            });
        }
        return { takePicture, arrowBackCircle }
    },
    mounted() {
        this.getHoliday();
        this.getSisaCuti();
        this.getJenis();
    },
    methods: {
        // check remaining leave
        async checkRemainingLeave() {
            if (this.jenis != 'MTA006') {
                if (this.sisaCuti < this.absence_duration) {
                    const toast = await toastController.create({
                        message: 'Sisa cuti anda tidak mencukupi',
                        duration: 3000,
                        position: 'top'
                    });

                    await toast.present();
                    // this.startDate = null;
                    // this.endDate = null;
                    // this.absence_duration = null;
                }
            }
        },
        // upload methods
        takePic: async function (e) {
            const pict = await this.takePicture();
            this.imgDocs = pict.webPath;

            const jenis_cuti = this.jenis;

            const dt = new Date();
            const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

            // create loading overlay
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Mohon tunggu...'
                });

            // convert blob to base64
            axios.get(this.imgDocs, { responseType: "blob" })
                .then(async function (response) {
                    // await loading.present();
                    const reader = new FileReader();
                    reader.readAsDataURL(response.data);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        const formData = new FormData();

                        const splitting = base64data.split(";");
                        const post = {
                            imgDocs: splitting[1].substr(7),
                            name: localStorage.getItem('id') + '_' + jenis_cuti + '_' + date + '.jpg'
                        }
                        // console.log(post);

                        axios.post(process.env.VUE_APP_API_URL + 'trx/absence/upload', post)
                            .then((res) => {
                                loading.dismiss();
                                // console.log(res)
                            })
                            .catch((err) => {
                                console.log(err);
                            });
                    };
                });
        },
        // get tanggal cuti pengecualian hari libur
        // getLeaveDate() {
        //     const getBusinessDays = (startDate, endDate) => {
        //         const start = new Date(startDate);
        //         const end = new Date(endDate);
        //         const current = new Date(startDate);
        //         const dates = [];

        //         while (current <= end) {
        //             if (current.getDay() !== 6 && current.getDay() !== 0) {
        //                 dates.push(new Date(current).getFullYear() + '-' + (new Date(current).getMonth() + 1) + '-' + new Date(current).getDate());
        //             }

        //             current.setDate(+current.getDate() + 1);
        //         }

        //         return dates;
        //     }
        //     const stdt = new Date(this.startDate);
        //     const eddt = new Date(this.endDate);

        //     const businessDays = getBusinessDays(stdt, eddt);
        //     const holidayDates = this.holidayDate;
        //     this.dateLeaves = businessDays;
        //     console.log(this.dateLeaves);
        //     console.log(holidayDates);
        // },
        async checkDate() {
            if (this.startDate > this.endDate) {
                const toast = await toastController.create({
                    message: 'Tanggal cuti tidak boleh mundur',
                    duration: 3000,
                    position: 'top'
                });

                await toast.present();

                this.startDate = null;
                this.endDate = null;
            }
        },
        async dateStrict() {
            const dtToday = new Date();

            let month = dtToday.getMonth() + 1;
            let day = dtToday.getDate();
            const year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            const minDate = year + '-' + month + '-' + day;

            if (this.startDate < minDate) {
                const toast = await toastController.create({
                    message: 'Tanggal tidak boleh kurang dari hari ini',
                    duration: 3000,
                    position: 'top'
                });

                await toast.present();
                this.startDate = null;
            }
            if (this.endDate < minDate) {
                const toast = await toastController.create({
                    message: 'Tanggal tidak boleh kurang dari hari ini',
                    duration: 3000,
                    position: 'top'
                });

                await toast.present();
                this.endDate = null;
            }
        },
        showInputAttachment() {
            if (this.attachment == false) {
                this.attachment = true;
            } else {
                this.attachment = false;
            }
        },
        getSisaCuti() {
            return axios.get(process.env.VUE_APP_API_URL + 'mst/user/getByUserId/' + localStorage.getItem('id'), {
                dataType: "json",
                headers: {
                    Authorization: 'bearer ' + localStorage.getItem('token')
                }
            }).then((response) => {
                const data = response.data.data;
                // console.log(response)
                this.sisaCuti = data.remaining_leave + data.cuti_besar;
            }).catch(async (e) => {
                console.log(e)
                const err = e.response.data; const toast = await toastController.create({
                    message: 'Sesi anda telah berakhir',
                    duration: 3000,
                    position: 'top'
                });

                await toast.present();
                if (err.code == 401) {
                    localStorage.clear()
                    this.$router.replace('/login', 'forward');
                }
                // const err = e.response.data;
                // toast.error(err.message);
            });
        },
        getHoliday() {
            if (this.startDate != null && this.endDate != null) {
                const stdt = new Date(this.startDate);
                const start = stdt.getFullYear() + '-' + (stdt.getMonth() + 1) + '-' + stdt.getDate();
                const eddt = new Date(this.endDate);
                const end = eddt.getFullYear() + '-' + (eddt.getMonth() + 1) + '-' + eddt.getDate();
                const post = {
                    start: start,
                    end: end,
                }
                return axios.post(process.env.VUE_APP_API_URL + 'trx/holiday/getByDateRange', post)
                    .then((response) => {
                        const data = response.data.data;
                        data.forEach(item => {
                            this.holidayDate.push(item.holiday_date)
                        });
                        this.totalHoliday = data.length;

                        // function get total cuti
                        const that = this;
                        function getBusinessDatesCount(stdt, eddt) {
                            const th = that;
                            const nationalDay = th.totalHoliday;
                            // console.log(nationalDay);

                            let count = 0;
                            const curDate = new Date(stdt.getTime());
                            while (curDate <= eddt) {
                                const dayOfWeek = curDate.getDay();
                                if (dayOfWeek !== 0 && dayOfWeek !== 6) count++;
                                curDate.setDate(curDate.getDate() + 1);
                            }
                            // alert(count);
                            return count - nationalDay;
                        }

                        const stdt = new Date(this.startDate);
                        const eddt = new Date(this.endDate);
                        const totalDays = getBusinessDatesCount(stdt, eddt);
                        this.absence_duration = totalDays;
                        console.log(this.absence_duration)


                        function getBusinessDays(stdt, eddt) {
                            // const start = new Date(startDate);
                            const end = new Date(eddt);
                            const current = new Date(stdt);
                            const dates = [];

                            while (current <= end) {
                                if (current.getDay() !== 6 && current.getDay() !== 0) {
                                    dates.push(new Date(current).getFullYear() + '-' + (new Date(current).getMonth() + 1) + '-' + new Date(current).getDate());
                                }

                                current.setDate(+current.getDate() + 1);
                            }

                            return dates;
                        }
                        const businessDays = getBusinessDays(stdt, eddt);
                        this.dateLeaves = businessDays;
                        console.log(this.dateLeaves);
                    }).catch((e) => {
                        console.log(e)
                        // const err = e.response.data;
                        // toast.error(err.message);
                    });
            }
        },
        getJenis() {
            return axios.get(process.env.VUE_APP_API_URL + 'mst/masterAbsence/getCuti', { dataType: "json" })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.jenisOptions.push(
                            {
                                id: item.absence_code,
                                text: item.absence_name
                            }
                        )
                    });
                }).catch((e) => {
                    console.log(e)
                    // const err = e.response.data;
                    // toast.error(err.message);
                });
        },
        submit: async function () {
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Mohon tunggu...'
                });
            await loading.present();

            // validasi submit
            if (this.startDate == null) {
                const toast = await toastController.create({
                    message: 'Tanggal mulai tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.endDate == null) {
                const toast = await toastController.create({
                    message: 'Tanggal selesai tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.jenis == null) {
                const toast = await toastController.create({
                    message: 'Jenis cuti tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.keterangan == null) {
                const toast = await toastController.create({
                    message: 'Keterangan tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.sisaCuti < this.absence_duration) {
                if (this.jenis != 'MTA006') {
                    const toast = await toastController.create({
                        message: 'Sisa cuti anda tidak mencukupi',
                        duration: 2000,
                        position: 'top'
                    });
                    loading.dismiss();
                    await toast.present();
                    return false;
                }
            }

            // current date
            const dt = new Date();
            const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
            // date from form input
            const stDate = new Date(this.startDate);
            const startdate = stDate.getFullYear() + "-" + (stDate.getMonth() + 1) + "-" + stDate.getDate();
            const enDate = new Date(this.endDate);
            const enddate = enDate.getFullYear() + "-" + (enDate.getMonth() + 1) + "-" + enDate.getDate();

            const post = {
                absence_code: this.jenis,
                absence_description: this.keterangan,
                absence_date: this.dateLeaves,
                absence_attachment: (this.imgDocs != null ? localStorage.getItem('id') + '_' + this.jenis + '_' + date + '.jpg' : ''),
                absence_latitude: '',
                absence_longitude: '',
                absence_duration: this.absence_duration,
                approval_1: 0,
                approval_2: 0,
                users_id: localStorage.getItem('id'),
                created_by: localStorage.getItem('id'),
            }
            await axios.post(process.env.VUE_APP_API_URL + 'trx/absence/add', post)
                .then(async (res) => {
                    if (res.status) {
                        const toast = await toastController.create({
                            message: 'Anda berhasil mengirimkan surat cuti',
                            duration: 3000,
                            position: 'top'
                        });

                        loading.dismiss();
                        await toast.present();
                        setTimeout(async () => { this.$router.go(-1); }, 1000);
                    }
                }).catch((e) => {
                    loading.dismiss();
                    console.log(e)
                    // const err = e.response.data;
                    // toast.error(err.message);
                });
        },
        back: function () {
            this.$router.back();
        },
    }
}
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
    left: 0px;
    padding: 0 1em;
    z-index: 10;
}

.header h2 {
    /* margin-left: 1em; */
    text-align: center;
    margin-top: -2em;
    text-shadow: 0 0 3px #a3a3a3;
    margin-bottom: 1em;
}

.form h5 {
    text-align: center;
    margin-bottom: 1.2em;
}

ion-datetime {
    /* margin-bottom: 2em; */
    --background: #fff1f2;
    --background-rgb: 255, 241, 242;

    /* border-radius: 16px; */
    box-shadow: rgba(var(--ion-color-rose-rgb), 0.3) 0px 10px 15px -3px;
}

#dashboard-container .btn-camera {
    width: 100px;
    height: 100px;
    margin: 3em auto;
    position: relative;
    text-align: center;
    cursor: pointer;
}

#dashboard-container .btn-camera label {
    position: absolute;
    top: -30px;
    left: -50px;
    right: -50px;
    width: 200px;
    font-size: 14px;
}

.img-document {
    margin-bottom: 3em !important;
    overflow: hidden;
    text-align: center;
}

.img-document img {
    width: 80%;
    border-radius: 1em;
}

.form span {
    color: red;
}
</style>
