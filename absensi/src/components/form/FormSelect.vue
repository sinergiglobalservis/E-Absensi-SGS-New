<template>
    <ion-select :class="(full ? 'full' : '')" :value="vModel" @ionDismiss="upModel" interface="action-sheet"
        placeholder="Pilihan" cancel-text="Kembali">
        <ion-select-option v-for="v in options" :key="v.id" :value="v.id">{{ v.text }}</ion-select-option>
    </ion-select>
</template>

<script>
/* eslint-disable */
import { IonSelect, IonSelectOption } from '@ionic/vue';
import { defineComponent } from 'vue'

export default {
    components: {
        IonSelect, IonSelectOption
    },
    props: {
        options: {
            type: Array,
            default() { return []; },
            required: true
        },
        modelValue: {
            type: String,
            default: null
        },
        full: {
            type: Boolean,
            default: false
        },
    },
    setup: function () {
        defineComponent({ IonSelect, IonSelectOption });
    },
    data() {
        return {
            vModel: null
        }
    },
    mounted: function () {
        this.vModel = this.modelValue
    },
    watch: {
        modelValue: function (n, o) {
            this.vModel = n
        }
    },
    methods: {
        upModel: function (e) {
            const val = e.target.value
            this.$emit('update:modelValue', val)
            this.$emit('change')
        }
    }
}
</script>

<style scoped>
.full {
    width: 100%;
}

ion-select {
    border: 1px solid var(--ion-color-step-900);
    border-radius: 5em;
    outline: none;
    height: 3em;
    background: white;
    font-size: 14px;
}

/* ion-select-option {
    background: rgb(97, 40, 40);
    color: var(--ion-color-step-100) !important;
} */
</style>