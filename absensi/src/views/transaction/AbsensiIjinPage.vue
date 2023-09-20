<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="bg-container">
                <!-- <img src="@/assets/img/bg_menu/ijin.jpg" class="izin" alt="izin page" /> -->
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">

                <div class="header">
                    <h2>Form Izin</h2>
                </div>

                <div v-if="imgDocs != null" class="img-document">
                    <img :src="imgDocs" alt="Foto Dokumen" />
                </div>

                <div class="btn-camera" @click.prevent="takePic">
                    <label>Ambil gambar</label>
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
                    <label><span>*</span>Tanggal</label>
                    <IonInput type="date" v-model="absence_date" class="date" @change="dateStrict()" full />
                </div>
                <div class="form">
                    <label><span>*</span>Dari Jam</label>
                    <IonInput type="time" v-model="timeStart" class="time" full />
                </div>
                <div class="form">
                    <label><span>*</span>Sampai Dengan</label>
                    <IonInput type="time" v-model="timeEnd" class="time" full />
                </div>

                <div class="form">
                    <label><span>*</span>Jenis Ijin</label>
                    <IonSelect v-model="jenis" :options="jenisOptions" full />
                </div>

                <div class="form">
                    <label><span>*</span>Kategori</label>
                    <IonSelect v-model="kategori" :options="kategoriOptions" />
                </div>

                <div class="form">
                    <label><span>*</span>Keterangan</label>
                    <IonTextarea v-model="keterangan" full />
                </div>

                <div class="tx-center">
                    <IonButton v-if="jenis != null && imgDocs != null && kategori != null && keterangan != null"
                        type="button" @click="submit">Kirimkan</IonButton>
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
import IonSelect from "@/components/form/FormSelect.vue";
import IonButton from "@/components/form/FormButton.vue";

export default {
    props: [],
    components: { IonContent, IonPage, IonButton, IonTextarea, IonSelect, IonInput, IonIcon },
    data() {
        return {
            jenisOptions: [],
            absence_date: null,
            timeStart: null,
            timeEnd: null,
            jenis: null,
            kategori: null,
            keterangan: null,
            imgDocs: null,
            locate: null,
            coordinates: null,
            kategoriOptions: [
                { id: 'Keperluan Pribadi', text: "Keperluan Pribadi" },
                { id: 'Keperluan Kantor', text: "Keperluan Kantor" },
            ],
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

        return { takePicture, printCurrentPosition, arrowBackCircle };
    },
    async mounted() {
        this.getIzin();

        const loading = await loadingController
            .create({
                message: 'Mencari lokasi...',
                spinner: 'circles',
            });
        loading.present();

        // gps get coordinates
        this.coordinates = await this.printCurrentPosition();

        let ltlg = null,
            i = 0;
        const intv = setInterval(async () => {

            this.coordinates = await this.printCurrentPosition();
            console.log(this.coordinates.coords.accuracy)

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
                                loading.dismiss();
                                return r.data[0].display_name;
                            } else {
                                const toast = await toastController.create({
                                    message: 'Gagal mengambil informasi lokasi',
                                    duration: 3000,
                                    position: 'top'
                                });
                                await toast.present();
                                loading.dismiss();
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
                loading.dismiss();

                // setTimeout(this.$router.go(-1),5000);
                // setTimeout(this.$router.push('/dashboard'), 3000);
            }
        }, 3000);
    },
    methods: {
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

            if (this.absence_date < minDate) {
                const toast = await toastController.create({
                    message: 'Tanggal tidak boleh kurang dari hari ini',
                    duration: 3000,
                    position: 'top'
                });

                await toast.present();
                this.absence_date = null;
            }
        },
        takePic: async function () {
            const pict = await this.takePicture();
            this.imgDocs = pict.webPath;

            const jenis_izin = this.jenis;

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
                        // const formData = new FormData();

                        const splitting = base64data.split(";");
                        const post = {
                            imgDocs: splitting[1].substr(7),
                            name: localStorage.getItem('id') + '_' + jenis_izin + '_' + date + '.jpg'
                        }
                        // console.log(post);

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
        getIzin() {
            return axios.get(process.env.VUE_APP_API_URL + 'mst/masterAbsence/getIzin', { dataType: "json" })
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    data.forEach(item => {
                        this.jenisOptions.push({ id: item.absence_code, text: item.absence_name })
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

            if (this.imgDocs == null) {
                loading.dismiss()
                const toast = await toastController.create({
                    message: 'Dokumen tidak boleh kosong',
                    duration: 3000,
                    position: 'top'
                });
                await toast.present();
                return false;
            } else if (this.absence_date == null) {
                const toast = await toastController.create({
                    message: 'Tanggal tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.timeStart == null) {
                const toast = await toastController.create({
                    message: 'Jam mulai tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.timeEnd == null) {
                const toast = await toastController.create({
                    message: 'Jam selesai tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.jenis == null) {
                const toast = await toastController.create({
                    message: 'Jenis izin tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.kategori == null) {
                const toast = await toastController.create({
                    message: 'Kategori izin tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            } else if (this.keterangan == null) {
                const toast = await toastController.create({
                    message: 'Keterangan izin tidak boleh kosong',
                    duration: 2000,
                    position: 'top'
                });
                loading.dismiss();
                await toast.present();
                return false;
            }

            const dt = new Date();
            const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

            const post = {
                absence_code: this.jenis,
                absence_description: this.keterangan,
                absence_date: this.absence_date,
                absence_duration: 1,
                absence_attachment: localStorage.getItem('id') + '_' + this.jenis + '_' + date + '.jpg',
                absence_latitude: this.coordinates.coords.latitude,
                absence_longitude: this.coordinates.coords.longitude,
                approval_1: 0,
                approval_2: 0,
                users_id: localStorage.getItem('id'),
                created_by: localStorage.getItem('id'),
            }
            await axios.post(process.env.VUE_APP_API_URL + 'trx/absence/add', post)
                .then(async (res) => {
                    if (res.status) {
                        const toast = await toastController.create({
                            message: 'Anda berhasil mengirimkan surat ijin',
                            duration: 3000,
                            position: 'top'
                        });

                        loading.dismiss()
                        await toast.present();
                        setTimeout(async () => { this.$router.replace('/dashboard', 'forward'); }, 1000);
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

#dashboard-container .btn-camera {
    width: 100px;
    height: 100px;
    margin: 50px auto 0;
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

.header h2 {
    /* margin-left: 1em; */
    text-align: center;
    margin-top: -2em;
    text-shadow: 0 0 3px #a3a3a3;
    margin-bottom: 1em;
}

.form span {
    color: red;
}
</style>
