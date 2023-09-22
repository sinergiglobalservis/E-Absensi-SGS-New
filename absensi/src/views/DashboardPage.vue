<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div class="container">
        <div class="header">
          <img src="../assets/img/sgs_logo.png" alt="sgs_logo" class="sgs_logo">
        </div>
        <ion-card>
          <ion-card-header>
            <ion-card-title>Absensi Online</ion-card-title>
            <ion-card-subtitle>
              <div class="jumbotron">
                <ion-icon :icon="calendar"></ion-icon>
                <span>{{ today }}</span>
              </div>
            </ion-card-subtitle>
          </ion-card-header>

          <ion-card-content v-if="attendee_date != null">Absen {{ attendee }} Pukul {{ (attendee_time_out == null ?
            attendee_time_in : attendee_time_out)
          }} <ion-icon :icon="checkmarkCircleOutline"></ion-icon></ion-card-content>
          <ion-card-content v-if="absence_date != null">
            Anda sedang {{ absence_name }}
            <ion-icon
              :icon="(approval_1 == 1 ? checkmarkCircleOutline : (approval_1 == 2 ? closeCircleOutline : timeOutline))"></ion-icon>
          </ion-card-content>
          <ion-card-content v-if="attendee == null && absence_date == null">Anda belum Absen</ion-card-content>
        </ion-card>
      </div>
      <div class="main-menu">
        <ion-grid>
          <ion-row>
            <ion-col class="row-menu">
              <div class="icon-menu ion-activatable ripple-parent circle"
                :style="{ 'pointer-events': (attendee_time_out != null ? '' : (attendee_time_in == null ? '' : 'none')), 'background-color': (attendee_time_out != null ? '' : (attendee_time_in == null ? '' : '#9ca79b')) }">
                <ion-ripple-effect></ion-ripple-effect>
                <ion-icon aria-hidden="true" :icon="logIn" @click="() => router.push('/absensi')" />
              </div>
              <br>
              <label>Masuk</label>
            </ion-col>
            <ion-col class="row-menu">
              <div class="icon-menu ion-activatable ripple-parent circle"
                :style="{ 'pointer-events': (attendee_time_out != null ? 'none' : (attendee_time_in != null ? '' : 'none')), 'background-color': (attendee_time_out != null || attendee_time_out != null ? '#9ca79b' : (attendee_time_in != null ? '' : '#9ca79b')) }">
                <ion-ripple-effect></ion-ripple-effect>
                <ion-icon aria-hidden="true" :icon="logOut" @click="() => router.push('/absensi/out')" />
              </div>
              <br>
              <label>Pulang</label>
            </ion-col>
            <ion-col class="row-menu">
              <div class="icon-menu ion-activatable ripple-parent circle">
                <ion-ripple-effect></ion-ripple-effect>
                <ion-icon aria-hidden="true" :icon="mail" @click="() => router.push('/absensi/perizinan')" />
              </div>
              <br>
              <label>Perizinan</label>
            </ion-col>
            <ion-col class="row-menu">
              <div class="icon-menu ion-activatable ripple-parent circle">
                <ion-ripple-effect></ion-ripple-effect>
                <ion-icon aria-hidden="true" :icon="document" @click="() => router.push('/absensi/rekap')" />
              </div>
              <br>
              <label>Rekap</label>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>

      <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>
    </ion-content>
  </ion-page>
</template>

<script>
import {
  IonPage,
  IonContent,
  IonCol,
  IonGrid,
  IonRow,
  IonIcon,
  IonRippleEffect,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardSubtitle,
  IonCardTitle,
  useIonRouter,
  useBackButton,
  toastController,
  IonRefresher,
  IonRefresherContent
} from '@ionic/vue';

import { mail, logIn, logOut, mailOpen, location, document, personCircle, calendar, checkmarkCircleOutline, timeOutline, closeCircleOutline } from 'ionicons/icons';
import { useRouter } from 'vue-router';
import { Geolocation } from '@capacitor/geolocation';
import { App } from '@capacitor/app';
// import { Device } from '@capacitor/device';
import axios from 'axios';

export default {
  components: {
    IonPage,
    IonContent,
    IonCol,
    IonGrid,
    IonRow,
    IonIcon,
    IonRippleEffect,
    IonCard,
    IonCardContent,
    IonCardHeader,
    IonCardSubtitle,
    IonCardTitle,
    IonRefresher,
    IonRefresherContent
  },
  setup() {

    // gps setup
    const printCurrentPosition = async () => {
      const coordinates = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 1000 });
      return coordinates;
    };

    // location setup
    (async () => {
      Geolocation.checkPermissions().then(async (r) => {
        // console.log(r)
        // const toast = await toastController.create({
        //   message: r.location,
        //   duration: 3000,
        //   position: 'bottom'
        // });
        // await toast.present();
        if (r.location == 'denied') {
          Geolocation.requestPermissions();
        }
      }).catch(async () => {
        const toast = await toastController.create({
          message: 'Hidupkan Location Perangkat Anda.',
          duration: 3000,
          position: 'bottom'
        });
        await toast.present();
      });
    })();

    const ionRouter = useIonRouter();
    useBackButton(-1, () => {
      if (!ionRouter.canGoBack()) {
        App.exitApp();
      }
    });

    const handleRefresh = () => {
      window.location.reload();
      setTimeout(() => {
        event.target.complete();
      }, 1000);
    };

    const router = useRouter();
    return { handleRefresh, useBackButton, router, printCurrentPosition, logIn, mail, mailOpen, logOut, location, document, personCircle, calendar, checkmarkCircleOutline, closeCircleOutline, timeOutline };
  },
  data() {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const d = new Date()
    const day_name = days[d.getDay()] + ', ' + d.getDate() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    return {
      today: day_name,
      attendee: null,
      attendee_time_in: null,
      attendee_time_out: null,
      attendee_date: null,
      absence_date: null,
      absence_name: null,
      // approval cuti check
      approval_1: null,
    };
  },
  async mounted() {
    this.checkSession();
    this.getData();
    // this.menu = this.tmp_menu;
  },
  methods: {
    async checkSession() {
      // check token akses
      axios.get(process.env.VUE_APP_API_URL + 'mst/user/getByUserId/' + localStorage.getItem('id'),
        {
          dataType: "json",
          headers: {
            Authorization: 'bearer ' + localStorage.getItem('token')
          }
        }).then((response) => {
          console.log(response.data.status)
        }).catch(async (e) => {
          const err = e.response.data;
          const toast = await toastController.create({
            message: 'Sesi anda telah berakhir',
            duration: 3000,
            position: 'top'
          });

          await toast.present();
          if (err.code == 401) {
            localStorage.clear()
            this.$router.replace('/login', 'forward');
          }
        });
    },
    // pengecekan user untuk disable absen in atau out
    getData() {
      axios.get(process.env.VUE_APP_API_URL + 'trx/attendee/getByUsersId/' + localStorage.getItem('id'))
        .then(async (r) => {
          const data = r.data.data[0];
          if (data != null) {
            if (data.attendee_time_in != null || data.attendee_time_out != null) {
              this.attendee = (data.attendee_time_out != null ? 'Pulang' : (data.attendee_time_in != null ? 'Masuk' : null));
              this.attendee_time_in = data.attendee_time_in;
              this.attendee_time_out = data.attendee_time_out;
              this.attendee_date = data.attendee_date;
            } else if (data.absence_code != null) {
              this.absence_date = data.absence_date;
              this.absence_name = data.absence_name;
              this.approval_1 = data.approval_1;
            }
          }
        }).catch(async (e) => {
          const err = e.response.data;
          const toast = await toastController.create({
            message: err.message,
            duration: 3000,
            position: 'top'
          });
          await toast.present();
        });
    }
  },
}
</script>

<style scoped>
.sgs_logo {
  margin-top: -4em;
}

.container {
  position: absolute;
  width: 100%;
  height: 13em;
  background-color: #e7f1ff;
  border-radius: 0 0 1em 1em;
}

.container .header {
  position: absolute;
  left: 0;
  top: 0;
  right: 0;
  margin: 3em auto;
  text-align: center;
}

.container ion-card {
  position: absolute;
  border: solid 1.5px rgb(133, 133, 133);
  box-shadow: 0px 3px 10px #303030;
  width: 90%;
  margin: 11em auto;
  /* height: 7em; */
  border-radius: 1em;
  top: 0;
  left: 0;
  right: 0;
  background: #FFFFFF;
}

.container ion-card-title {
  color: #000000;
  font-size: 1.2em;
}

.container .jumbotron {
  font-size: 1em;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 400px;
}

.jumbotron span {
  font-size: 1.3em;
}

.container .jumbotron ion-icon {
  float: left;
  font-size: 1.5em;
  color: #E97777;
  margin-right: 5px;
}

.container ion-card-content {
  margin-top: -.5em;
  margin-bottom: -.5em;
  float: right;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: 400px;
}

ion-card-content ion-icon {
  font-size: 1.5em;
  margin-bottom: -5px;
  color: #00cd2c;
  /* background-color: #000000; */
}

ion-ripple-effect {
  border-radius: 1em;
}

.main-menu {
  margin: 17em auto;
  width: 100%;
  position: absolute;
}

.main-menu ion-col {
  text-align: center;
}

ion-col .icon-menu {
  box-shadow: 2px 2px 3px #303030;
  margin: 5px 0px;
  padding: 5px 10px;
  background-color: #1ebbd7;
  border-radius: 1em;
  /* height: 5em; */
  border: solid 1px rgb(30, 103, 45);
  /* margin: 1em 1em; */
  text-align: center;
  display: inline-block;
}

ion-col label {
  font-size: 14px;
  color: #000000;
}

.main-menu ion-icon {
  display: inline-block;
  font-size: 40px;
  color: #ffffff;
  margin-top: 10px;
}

.ripple-parent {
  position: relative;
  overflow: hidden;

  border: 1px solid #ddd;
}

.circle {
  border-radius: 50%;
}
</style>