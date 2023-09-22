<template>
    <ion-page>
        <ion-content :fullscreen="true">

            <div id="selfie-container">
                <!-- <img src="@/assets/img/bye.png" class="image-selfie" alt="selfie image" /> -->
                <div class="tx-left">
                    <ion-icon :icon="arrowBackCircle" @click="back" />
                </div>
            </div>

            <div id="dashboard-container">
                <div v-if="selfie != null" class="foto-selfie">
                    <img :src="selfie" alt="Foto Selfie" />
                </div>

                <div class="btn-selfie" @click.prevent="takePic">
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

                <div class="tx-center">
                    <IonButton v-if="selfie != null && locate != null" type="button" @click="absen">Pulang</IonButton>
                </div>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
// import systems
import { IonContent, IonPage, toastController, loadingController, IonIcon } from '@ionic/vue';
import { Camera, CameraResultType } from '@capacitor/camera';
import { Geolocation } from '@capacitor/geolocation';
import axios from 'axios';
import { arrowBackCircle } from 'ionicons/icons';


// import components
// import IonInput from "@/components/form/FormInput.vue";
// import IonSelect from "@/components/form/FormSelect.vue";
import IonButton from "@/components/form/FormButton.vue";

const dt = new Date();
const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
const time = dt.getHours().toString() + dt.getMinutes().toString();


export default {
    props: [],
    components: { IonContent, IonPage, IonButton, IonIcon },
    data() {
        return {
            filename: localStorage.getItem('id') + "_OUT_" + date + "_" + time + '.jpg',
            selfie: null,
            locate: null,
            coordinates: null,
            workhour_code: null,
            jadwalOptions: [],
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
        // mounting get jadwal
        // this.getJadwal();

        const loading = await loadingController
            .create({
                spinner: 'circles',
                message: 'Mencari lokasi...'
            });
        loading.present();

        // gps get coordinates
        this.coordinates = await this.printCurrentPosition();

        let ltlg = null,
            i = 0;
        const intv = setInterval(async () => {

            this.coordinates = await this.printCurrentPosition();
            // console.log(this.coordinates.coords.accuracy)

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
                                loading.dismiss
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

                setTimeout(this.$router.go(-1), 3000);
            }
        }, 3000);

    },
    methods: {
        getJadwal() {
            const dt = new Date();
            const now = dt.getFullYear() + '-' + (dt.getMonth() + 1) + '-' + dt.getDate()
            const post = {
                id: localStorage.getItem('id')
            }
            return axios.post(process.env.VUE_APP_API_URL + 'trx/attendee/getScheduleOut', post)
                .then((response) => {
                    // binding data
                    const data = response.data.data;
                    this.workhour_code = data.workhour_code;
                    // console.log(this.workhour_code)
                }).catch(async (e) => {
                    // console.log(e)
                    const err = e.response.data;
                    const toast = await toastController.create({
                        message: err,
                        duration: 3000,
                        position: 'top'
                    });
                    await toast.present();
                });
        },
        routeTo: async function (e, url) {
            this.$router.push(url);
        },
        takePic: async function () {
            const pict = await this.takePicture();
            this.selfie = pict.webPath;

            // create loading overlay
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Please wait...'
                });
            const nameFile = this.filename;

            // convert blob to base64
            axios.get(this.selfie, { responseType: "blob" })
                .then(async function (response) {
                    await loading.present();
                    const reader = new FileReader();
                    reader.readAsDataURL(response.data);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        // const formData = new FormData();

                        const splitting = base64data.split(";");
                        const post = {
                            img: splitting[1].substr(7),
                            name: nameFile
                        }


                        axios.post(process.env.VUE_APP_API_URL + 'trx/attendee/upload', post)
                            .then((res) => {
                                loading.dismiss();
                                // console.log(res)
                            })
                            .catch((err) => {
                                // console.log(err);
                            });
                    };
                });
        },
        absen: async function () {
            const loading = await loadingController
                .create({
                    spinner: 'circles',
                    message: 'Please wait...',
                });
            await loading.present();

            const dt = new Date();
            const date = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
            const time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
            const imgName = this.filename;

            const post = {
                attendee_date: date,
                attendee_time_out: time,
                attendee_latitude_out: this.coordinates.coords.latitude,
                attendee_longitude_out: this.coordinates.coords.longitude,
                images_out: imgName,
                users_id: localStorage.getItem('id'),
                updated_by: localStorage.getItem('id')
            }

            await axios.post(process.env.VUE_APP_API_URL + 'trx/attendee/add', post)
                .then(async (r) => {
                    if (r.status) {
                        const toast = await toastController.create({
                            message: 'Anda berhasil absen pulang',
                            duration: 3000,
                            position: 'top'
                        });

                        loading.dismiss()
                        await toast.present();
                        setTimeout(async () => {
                            window.location.replace('/dashboard');
                        }, 100);
                    }
                }).catch(async (e) => {
                    const err = e.response.data;
                    loading.dismiss()
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
}
</script>

<style scoped>
#selfie-container {
    position: absolute;
    height: calc(240px + 2em);
    top: 0;
    left: 0;
    right: 0;
    overflow: hidden;
    z-index: -1;
    text-align: center;
}


#selfie-container ion-icon {
    margin: 10px 10px;
    font-size: 3em;
    color: grey;
    transition: .2s ease-in;
}

#selfie-container ion-icon:hover {
    color: black;
}

#selfie-container .image-selfie {
    width: 100%;
    height: auto;
}

.foto-selfie {
    margin-bottom: 3em !important;
    overflow: hidden;
    text-align: center;
}

.foto-selfie img {
    width: 80%;
    border-radius: 1em;
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

#dashboard-container .btn-selfie {
    width: 100px;
    height: 100px;
    margin: 0 auto;
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
</style>
