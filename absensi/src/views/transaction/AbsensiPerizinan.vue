<template>
    <ion-page>
        <ion-content :fullscreen="true">
            <div id="bg-container">
                <div class="tx-left" style="position: absolute;">
                    <ion-icon aria-hidden="true" :icon="arrowBackCircle" @click="back" />
                </div>
                <img src="@/assets/img/bg_menu/ijin.jpg" class="image-selfie" alt="selfie image" />
            </div>

            <div id="dashboard-container">
                <div class="row">
                    <div v-for="arr in menu" :key="arr" class="row-menu">
                        <div class="menu" @click="routeTo($event, arr.url)">
                            <div class="menu-content">
                                <ion-icon aria-hidden="true" :icon="arr.icon" style="font-size: 60px;" />
                                <br>
                                <span v-html="arr.title"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </ion-content>
    </ion-page>
</template>

<script>
// import systems
import { IonContent, IonPage, IonIcon } from '@ionic/vue';
import { mail, arrowBackCircle, arrowUndo, mailOpen, listOutline } from 'ionicons/icons';

export default {
    props: [],
    components: { IonContent, IonPage, IonIcon },
    data() {
        return {
            menu: [
                {
                    id: 1,
                    title: 'Cuti',
                    icon: arrowUndo,
                    url: '/absensi/cuti'
                },
                {
                    id: 2,
                    title: 'Sakit',
                    icon: mail,
                    url: '/absensi/sakit'
                },
                {
                    id: 3,
                    title: 'Izin',
                    icon: mailOpen,
                    url: '/absensi/ijin'
                },
                {
                    id: 4,
                    title: 'List Pengajuan',
                    icon: listOutline,
                    url: '/absensi/pengajuan'
                },
            ]
        };
    },
    setup() {
        return { mail, mailOpen, arrowBackCircle, arrowUndo, listOutline }
    },
    async mounted() {
        // this.getData();
    },
    methods: {
        routeTo: async function (e, url) {
            this.$router.push(url);
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

#dashboard-container {
    background: #E3F4F4;
    height: calc(100% + 2em);
    margin-top: 100px;
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
    border-radius: .5em;
    width: 12em;
    height: 100px;
    float: left;
    background: #9BABB8;
    box-shadow: 0px 3px 10px #303030;
    /* background: rgb(71, 189, 128); */
    /* background: linear-gradient(147deg, rgba(112,71,189,1) 50%, rgba(183,146,255,1) 100%); */
    position: relative;
    cursor: pointer;
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

#bg-container ion-icon {
    margin: 10px 10px;
    font-size: 3em;
    color: grey;
    transition: .2s ease-in;
}

#bg-container ion-icon:hover {
    color: black;
}

#dashboard-container ion-icon {
    color: white;
}

#dashboard-container span {
    color: white;
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
</style>
