<template>
    <div class="pageHeaderBg">
        <div class="pageHeader">{{ title }}</div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#b148d2" fill-opacity="1" d="M0,160L80,181.3C160,203,320,245,480,245.3C640,245,800,203,960,197.3C1120,192,1280,224,1360,240L1440,256L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"></path></svg>
    <IonIcon v-if="back" @click="fnBack" :icon="icons.arrowBack" size="large" />
</template>

<script>
import { IonIcon } from '@ionic/vue';
import * as icons from 'ionicons/icons';

export default {
    props: {
        title: {
            type: String,
            default: ''
        },
        back: {
            type: Boolean,
            default: false
        }
    },
    components: { IonIcon },
    setup(){
        return { icons };
    },
    data(){
        return {}
    },
    methods: {
        fnBack: function() {
            this.$router.back();
        }
    }
}
</script>

<style scoped>
    .pageHeader {
        margin: 0 0.5em 1em 0.5em;
        font-size: 2.4em;
        padding: 1em 0;
        color: var(--ion-color-step-950);
        /* background-color: bisque; */
        text-transform: uppercase;
        text-align: center;
        /* border-radius: 0 0 0.5em 0.5em; */
        /* box-shadow: 0 6px 6px grey; */
        font-weight: bold;
        font-family: 'Handlee';
    }
    .pageHeaderBg {
        background-color: var(--color-light-purple);
        padding-bottom: 0.5em;
    }
    ion-icon {
        color: var(--ion-color-step-950);
        position: absolute;
        top: 0.5em;
        left: 0.5em;
        cursor: pointer;
    }
</style>