<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="bg-container">
                <!-- <img src="@/assets/img/bg_menu/ijin.jpg" class="image-selfie" alt="selfie image" /> -->
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">
                <div class="header">
                    <h2>Form Sakit</h2>
                </div>

                <div v-if="imgDocs != null" class="img-sdr">
                    <img :src="imgDocs" alt="Foto Selfie" />
                </div>

                <div class="btn-selfie" @click.prevent="takePic">
                    <label>Foto Surat Keterangan</label>
                    <img src="@/assets/img/cam.png" alt="Camera" />
                </div>
                <div class="locate-container">
                    <label for="">Lokasi saat ini</label>
                    <div v-if="locate == null" class="locate-content tx-center">
                        Pastikan GPS Anda aktif
                    </div>
                    <div v-else class="locate-content">
                        {{ locate }}
                    </div>
                </div>

                <div class="form">
                    <label><span>*</span>Dari Tanggal</label>
                    <IonInput type="date" v-model="startDate" class="date" full readonly />
                </div>

                <div class="form">
                    <label><span>*</span>Sampai Tanggal</label>
                    <IonInput type="date" v-model="endDate" class="date"
                        @change="dateStrict(); getLeaveDate(); getHoliday();" full />
                </div>

                <div class="form">
                    <label><span>*</span>Keterangan</label>
                    <IonTextarea v-model="keterangan" full />
                    <span>*Foto surat dokter pada saat anda di klinik atau rumah sakit</span>
                </div>

                <div class="tx-center">
                    <IonButton v-if="imgDocs != null" type="button" @click="submit">Kirimkan</IonButton>
                </div>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
// import systems
import { IonContent, IonPage, toastController, loadingController, IonIcon } from '@ionic/vue';
import { Geolocation } from '@capacitor/geolocation';
import { Camera, CameraResultType } from '@capacitor/camera';
import axios from 'axios';
import { arrowBackCircle } from 'ionicons/icons';


// import components
import IonTextarea from "@/components/form/FormTextarea.vue";
import IonInput from "@/components/form/FormInput.vue";
// import IonSelect from "@/components/form/FormSelect.vue";
import IonButton from "@/components/form/FormButton.vue";

export default {
    props: [],
    components: { IonContent, IonPage, IonButton, IonTextarea, IonInput, IonIcon },
    data() {
        return {
            options: [],
            startDate: new Date().toISOString().slice(0, 10),
            endDate: null,
            keterangan: null,
            imgDocs: null,
            locate: null,
            coordinates: null,
            totalHoliday: null,
            absence_duration: null,
            dateLeaves: [],
        };
    },
    setup() {
        // gps setup
        const printCurrentPosition = async () => {
            const coordinates = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 1000 });
            return coordinates;
        };


        // camera setup
        (async () => {
            Camera.checkPermissions().then(async (r) => {
                // const toast = await toastController.create({
                //     message: r.camera,
                //     duration: 3000,
                //     position: 'top'
                // });
                // await toast.present();
                if (r.camera == 'denied') {
                    Camera.requestPermissions();
                }
            }).catch(async (e) => {
                const toast = await toastController.create({
                    message: e,
                    duration: 3000,
                    position: 'top'
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

        return { takePicture, printCurrentPosition, arrowBackCircle };
    },
    async mounted() {
        this.getHoliday();
        this.getData();

        // gps get coordinates
        this.coordinates = await this.printCurrentPosition();

        let ltlg = null,
            i = 0;
        const intv = setInterval(async () => {

            this.coordinates = await this.printCurrentPosition();
            console.log(this.coordinates.coords)

            if (this.coordinates != null) {
                ltlg = this.coordinates.coords.latitude + "," + this.coordinates.coords.longitude;

                if (this.coordinates.coords.accuracy <= 100) {
                    // get data from open maps
                    (async () => {
                        const uri = 'https://nominatim.openstreetmap.org/search?q=' + ltlg + '&format=json&polygon_geojson=1&addressdetails=1';

                        this.locate = await axios.get(uri).then(async (r) => {
                            if (r.status == 200) {
                                // const toast = await toastController.create({
                                //     message: 'Berhasil mengambil informasi lokasi',
                                //     duration: 3000,
                                //     position: 'top'
                                // });
                                // await toast.present();

                                return r.data[0].display_name;
                            } else {
                                const toast = await toastController.create({
                                    message: 'Gagal mengambil informasi lokasi',
                                    duration: 3000,
                                    position: 'top'
                                });
                                await toast.present();
                                return;
                            }
                        });
                    })()

                    clearInterval(intv);
                }
            }
            i++;

            if (i > 19) {
                clearInterval(intv);

                const toast = await toastController.create({
                    message: 'Gagal mengambil informasi lokasi',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();

                // setTimeout(this.$router.go(-1),5000);
                setTimeout(this.$router.push('/dashboard'), 3000);
            }
        }, 3000);
    },
    methods: {
        // get tanggal pengecualian hari libur
        async getLeaveDate() {
            const getBusinessDays = (startDate, endDate) => {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const current = new Date(startDate);
                const dates = [];

                while (current <= end) {
                    if (current.getDay() !== 6 && current.getDay() !== 0) {
                        dates.push(new Date(current).getFullYear() + '-' + (new Date(current).getMonth() + 1) + '-' + new Date(current).getDate());
                    }

                    current.setDate(+current.getDate() + 1);
                }

                return dates;
            }
            const stdt = new Date(this.startDate);
            const eddt = new Date(this.endDate);

            const businessDays = getBusinessDays(stdt, eddt);
            this.dateLeaves = businessDays;
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
        takePic: async function () {
            const pict = await this.takePicture();
            this.imgDocs = pict.webPath;


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
                    await loading.present();
                    const reader = new FileReader();
                    reader.readAsDataURL(response.data);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        // const formData = new FormData();

                        const splitting = base64data.split(";");
                        const post = {
                            imgDocs: splitting[1].substr(7),
                            name: localStorage.getItem('id') + '_' + 'SAKIT' + '_' + date + '.jpg'
                        }
                        loading.dismiss();

                        axios.post(process.env.VUE_APP_API_URL + 'trx/absence/upload', post)
                            .then((res) => {
                                loading.dismiss();
                                console.log(res)
                            })
                            .catch((err) => {
                                console.log(err);
                            });
                    };
                });
        },
        getData() {
            return axios.get(process.env.VUE_APP_API_URL + 'mst/masterAbsence/show', { dataType: "json" })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.options.push({ id: item.absence_code, text: item.absence_name })
                    });
                }).catch((e) => {
                    console.log(e)
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
                        const countTotalHoliday = this.totalHoliday;
                        function getBusinessDatesCount(stdt, eddt) {
                            const th = countTotalHoliday;
                            const nationalDay = th;
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
        submit: async function () {
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Mohon tunggu...'
                });
            await loading.present();

            if (this.imgDocs == null) {
                loading.dismiss()
                const toast = await toastController.create({
                    message: 'Foto tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return false;
            }
            if (this.endDate == null) {
                loading.dismiss()
                const toast = await toastController.create({
                    message: 'Foto tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return false;
            }
            if (this.keterangan == null) {
                loading.dismiss()
                const toast = await toastController.create({
                    message: 'Foto tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return false;
            }

            const dt = new Date();
            const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

            const stDate = new Date(this.startDate);
            const startdate = stDate.getFullYear() + "-" + (stDate.getMonth() + 1) + "-" + stDate.getDate();
            const enDate = new Date(this.endDate);
            const enddate = enDate.getFullYear() + "-" + (enDate.getMonth() + 1) + "-" + enDate.getDate();

            const post = {
                absence_code: 'MTA001',
                absence_description: this.keterangan,
                absence_date: this.dateLeaves,
                absence_duration: this.absence_duration,
                absence_attachment: localStorage.getItem('id') + '_' + 'SAKIT' + '_' + date + '.jpg',
                absence_latitude: this.coordinates.coords.latitude,
                absence_longitude: this.coordinates.coords.longitude,
                approval_1: 1,
                approval_2: 1,
                users_id: localStorage.getItem('id'),
                created_by: localStorage.getItem('id'),
            }
            console.log(post);

            await axios.post(process.env.VUE_APP_API_URL + 'trx/absence/add', post)
                .then(async (res) => {
                    if (res.status) {
                        const toast = await toastController.create({
                            message: 'Berhasil',
                            duration: 3000,
                            position: 'top'
                        });

                        loading.dismiss()
                        await toast.present();
                        setTimeout(async () => { this.$router.go(-1); }, 1000);
                    }
                }).catch((e) => {
                    loading.dismiss()
                    console.log(e)
                    // const err = e.response.data;
                    // toast.error(err.message);
                });
        },
        back: function () {
            this.$router.back();
        }
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

.img-sdr {
    margin-bottom: 3em !important;
    overflow: hidden;
    text-align: center;
}

.img-sdr img {
    width: 80%;
    border-radius: 1em;
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

#dashboard-container .locate-container {
    padding-top: 3em;
    position: relative;
}

#dashboard-container .locate-container>label {
    font-size: 16px;
    position: absolute;
    top: 0;
    left: 0;
    border: 1px solid #cecece;
    border-radius: 1em;
    padding: 0.2em 0.5em;
}

#dashboard-container .locate-container .locate-content {
    margin-top: -1em;
    border: 1px solid #cecece;
    padding: 0.5em;
    border-radius: 0.5em;
}

#dashboard-container .btn-selfie {
    width: 100px;
    height: 100px;
    margin: 50px auto 0;
    position: relative;
    text-align: center;
    cursor: pointer;
}

#dashboard-container .btn-selfie label {
    position: absolute;
    top: -30px;
    left: -50px;
    right: -50px;
    width: 200px;
    font-size: 14px;
}

.header h2 {
    /* margin-left: 1em; */
    text-align: center;
    margin-top: -2em;
    text-shadow: 0 0 3px #a3a3a3;
    margin-bottom: 2em;
}

.form span {
    font-size: 11px;
    color: red;
}
</style>
