<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="topbar" class="tx-left">
                <ion-icon id="popover-button" aria-hidden="true" :icon="personCircle" />
                <ion-popover trigger="popover-button" :dismiss-on-select="true">
                    <ion-content>
                        <ion-list lines="none">
                            <ion-item :button="true" :detail="false">Profile</ion-item>
                            <ion-item :button="true" :detail="false" @click.prevent="logout">Logout</ion-item>
                        </ion-list>
                    </ion-content>
                </ion-popover>
            </div>
            <div id="logo-container">
                <ion-slides pager="true" :options="slideOpts">
                    <ion-slide v-for="(val, i) in carousel" :key="i">
                        <img class="img-carousel" :src="require('@/assets/img/carousel/' + val + '.jpg')" />
                    </ion-slide>
                </ion-slides>
            </div>

            <div id="dashboard-container">
                <div class="row">
                    <div id="scroll-container" v-if="attendee != null">
                        <div id="scroll-text">Anda sudah absen {{ this.attendee }} Pukul {{ this.attendee_time }}</div>
                    </div>
                    <div v-for="arr in  menu " :key="arr" class="row-menu">
                        <div class="menu" @click="routeTo($event, arr.url)"
                            :style="{ 'background-color': arr.color, 'pointer-events': arr.isDisabled }">
                            <div class="menu-content">
                                <ion-icon aria-hidden="true" :icon="arr.icon" style="font-size: 50px;"></ion-icon><br>
                                {{ arr.title }}
                            </div>
                        </div>

                        <!-- <div class="menu-full" @click.prevent="routeTo($event, arr.url)">
                            <img :src="require('@/assets/img/menu/' + arr.bg + '.jpg')" />
                            <span v-html="arr.title" style="color: black;"></span>
                            <ion-icon aria-hidden="true" :icon="arr.icon"
                                style="font-size: 50px; float: right; margin: 20px 30px;"></ion-icon>
                        </div> -->
                    </div>
                </div>
            </div>
            <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
                <ion-refresher-content></ion-refresher-content>
            </ion-refresher>
        </ion-content>
    </ion-page>
</template>

<script>
import { IonContent, IonPage, IonSlides, IonSlide, toastController, useBackButton, useIonRouter, IonPopover, IonList, IonItem, IonRefresher, IonRefresherContent } from '@ionic/vue';
import { personCircle } from 'ionicons/icons';
import { App } from '@capacitor/app';
import { mail, arrowDownCircle, arrowUpCircle, document } from 'ionicons/icons';
import axios from 'axios';

export default {
    props: [],
    components: { IonContent, IonPage, IonSlides, IonSlide, IonPopover, IonList, IonItem, IonRefresher, IonRefresherContent },
    data() {
        return {
            attendee: null,
            attendee_time: null,
            menu: [],
            carousel: [
                '01',
                '02',
                '03',
                '04',
                // '05',
                '06',
                '07',
                '08',
                '09'
            ],
            tmp_menu: [
                {
                    id: 1,
                    title: 'Absen Masuk',
                    // bg: 'bg_1',
                    color: '#537188',
                    icon: arrowDownCircle,
                    url: '/absensi'
                },
                {
                    id: 2,
                    title: 'Absen Keluar',
                    // bg: 'bg_2',
                    color: '#5C8984',
                    icon: arrowUpCircle,
                    url: '/absensi/out'
                },
                {
                    id: 3,
                    title: 'Perizinan',
                    // bg: 'bg_3',
                    color: '#545B77',
                    icon: mail,
                    url: '/absensi/perizinan',
                    isDisabled: 'none' // none is for disabling div
                },
                {
                    id: 4,
                    title: 'Rekap',
                    // bg: 'bg_5',
                    color: '#374259',
                    icon: document,
                    url: '/absensi/rekap',
                    isDisabled: 'none' // none is for disabling div
                }
            ]
        };
    },
    setup() {
        const handleRefresh = (CustomEvent) => {
            location.reload();
            setTimeout(() => {
                event.target.complete();
            }, 1000);
        };

        const slideOpts = {
            initialSlide: 0,
            speed: 4000,
            autoplay: true,
        };
        const ionRouter = useIonRouter();
        useBackButton(-1, () => {
            if (!ionRouter.canGoBack()) {
                App.exitApp();
            }
        });

        return { slideOpts, handleRefresh, personCircle, arrowDownCircle, arrowUpCircle, mail, document };
    },
    async mounted() {
        // chunk data menu
        // const chunkSize = 2;
        // for (let i = 0; i < this.tmp_menu.length; i += chunkSize) {
        //     const chunk = this.tmp_menu.slice(i, i + chunkSize);
        //     // do whatever
        //     this.menu.push(chunk);
        // }
        this.menu = this.tmp_menu;
        this.getData();
    },
    methods: {
        routeTo: async function (e, url) {
            this.$router.push(url);
        },
        imgGenerate: function (index, src) {
            return "<img class=\"img-carousel\" src=\"" + src + "\" alt=\"Image Carousel " + (index + 1) + '" />';
        },
        logout: async function () {
            this.$router.replace('/login', 'forward');
            localStorage.clear();
            const toast = await toastController.create({
                message: 'Berhasil logout',
                duration: 3000,
                position: 'bottom'
            });
            await toast.present();
        },
        // pengecekan user untuk disable absen in atau out
        getData() {
            axios.get(process.env.VUE_APP_API_URL + 'trx/attendee/getByUsersId/' + localStorage.getItem('id'))
                .then(async (r) => {
                    const data = r.data.data;
                    if (r.status) {
                        if (data.attendee_time != null && data.attendee_type != null) {
                            this.attendee = (data.attendee_type == 'IN' ? 'Masuk' : (data.attendee_type == 'OUT' ? 'Pulang' : null));
                            this.attendee_time = data.attendee_time
                        }
                    }
                    console.log(this.attendee)
                }).catch(async (e) => {
                    console.log(e)
                    const err = e.response.data;
                    const toast = await toastController.create({
                        message: err.message,
                        duration: 3000,
                        position: 'bottom'
                    });
                    await toast.present();
                });
        }
    }
}
</script>

<style scoped>
#logo-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 0;
    text-align: right;
    height: calc(200px + 3em);
    z-index: -1;
    /* background: url('@/assets/img/bg_1.jpg') no-repeat fixed top center; */
}

#logo-container img {
    width: 200px;
}

#logo-container .logo_kbp {
    position: absolute;
    right: 1em;
    top: 1em;
    z-index: 100;
}

#logo-container .img-carousel {
    width: 100%;
    height: auto;
}

#dashboard-container {
    background: white;
    height: calc(100% + 2em);
    margin-top: 25%;
    border-radius: 2em 2em 0 0;
    padding: 3em 1em 1em 1em;
    box-shadow: 0 -0.2em 1em grey;
    color: black;
    overflow: auto;

}

#dashboard-container .row {
    padding: 0 -1em;
    /* margin: 0.5em 0; */
    margin: 0 auto;
    position: relative;
    width: min-content;
}

#dashboard-container .row .row-menu {
    width: calc(200px * 2 + 2.5em);
}

#dashboard-container .row .row-menu .menu {
    padding: 0 1em;
    margin: 0.5em 0.5em;
    border-radius: 1em;
    width: 12em;
    height: 100px;
    float: left;
    /* background: #8294C4; */
    /* background: rgb(71, 189, 128); */
    /* background: linear-gradient(147deg, rgba(112,71,189,1) 50%, rgba(183,146,255,1) 100%); */
    position: relative;
    cursor: pointer;
    box-shadow: 0 -0.2em .5em grey;
}

#dashboard-container .row .row-menu .menu .menu-content {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    text-align: center;
    color: white;
}

.menu-full {
    /* border: 1px solid; */
    border-radius: 1em;
    height: 120px;
    padding: 1em;
    margin: 1em 0;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0.2em 1em grey;
}

.menu-full>img {
    z-index: 1;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: inline-block;
}

.menu-full>span {
    z-index: 10;
    position: absolute;
    top: 50%;
    left: 0;
    margin: 0 1em;
    transform: translateY(-50%);
    color: white;
    text-shadow: 0 1px 5px grey;
    font-size: 18px;
}

/* fix width */
@media screen and (max-width: 500px) {
    #dashboard-container .row .row-menu {
        width: calc(160px * 2 + 2.5em) !important;
    }

    #dashboard-container .row .row-menu .menu {
        width: 160px !important;
    }
}

#topbar {
    margin: 4px;
}

#topbar ion-icon {
    background-color: white;
    padding: 2px 2px;
    margin: 2px 2px;
    border-radius: .2em;
    font-size: 40px;
    color: darkgrey;
}

ion-list ion-item {
    font-size: 12px;
}

ion-list ion-item:hover {
    color: grey;
}


/* running text */
#scroll-container {
    /* border: 3px solid black; */
    /* border-radius: 5px; */
    overflow: hidden;
}

#scroll-text {
    margin-bottom: 10px;
    font-weight: 700;
    /* animation properties */
    -moz-transform: translateX(100%);
    -webkit-transform: translateX(100%);
    transform: translateX(100%);

    -moz-animation: my-animation 15s linear infinite;
    -webkit-animation: my-animation 15s linear infinite;
    animation: my-animation 15s linear infinite;
}

/* for Firefox */
@-moz-keyframes my-animation {
    from {
        -moz-transform: translateX(100%);
    }

    to {
        -moz-transform: translateX(-100%);
    }
}

/* for Chrome */
@-webkit-keyframes my-animation {
    from {
        -webkit-transform: translateX(100%);
    }

    to {
        -webkit-transform: translateX(-100%);
    }
}

@keyframes my-animation {
    from {
        -moz-transform: translateX(100%);
        -webkit-transform: translateX(100%);
        transform: translateX(100%);
    }

    to {
        -moz-transform: translateX(-100%);
        -webkit-transform: translateX(-100%);
        transform: translateX(-100%);
    }
}
</style>
