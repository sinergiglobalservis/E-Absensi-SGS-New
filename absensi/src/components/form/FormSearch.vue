<template>
    <div :class="'search-container '+(full?'full ':'')+(error?'error ':'')">
        <input type="text" :placeholder="placeholder" :value="modelValue" :maxlength="max" @input="emiter" />
        <IonIcon :icon="searchOutline" size="large" />
    </div>
</template>

<script>
import { IonIcon } from '@ionic/vue';
import { searchOutline } from 'ionicons/icons';

export default {
    props: {
        modelValue: {
            default: ''
        },
        placeholder: {
            default: ''
        },
        max: {
            type: Number,
            default: 1024
        },
        full: {
            type: Boolean,
            default: false
        }
    },
    components: { IonIcon },
    setup(){
        return { searchOutline };
    },
    data(){
        return {
            last: '',
            error: false
        };
    },
    methods: {
        emiter: function(e){
            this.$emit('update:modelValue', e.target.value);
        }
    }
}    
</script>

<style scoped>
.search-container {
    border-radius: 5em;
    background: var(--ion-color-step-900);
    border: none;
    outline: none;
    display: flex;
}

input {
    padding: 0.4em 1em;
    font-size: 1em;
    /* border: 1px solid #bcb4b1; */
    height: 3em;
    outline: none;
    background: none;
    border-inline: none;
    border: none;
    flex: auto;

}
.search-container.full {
    width: 100%;
}
.search-container.error {
    background-color: #fe5643;
}

.search-container ion-icon {
    padding: 0.2em;
    /* border: 1px solid; */
    border-radius: 3em;
    background: var(--ion-color-tertiary);
    color: var(--ion-color-step-950);
}

@media (prefers-color-scheme: dark) {
    input {
        color: var(--ion-color-step-50);
    }
}
</style>