<template>
    <textarea :class="(full ? 'full' : '')" :placeholder="placeholder" @input="emiter" v-text="modelValue"></textarea>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: String,
            default: ""
        },
        placeholder: {
            type: String,
            default: ""
        },
        full: {
            type: Boolean,
            default: false
        }
    },
    mounted: function () {
        // console.log(this.modelValue)
    },
    methods: {
        emiter: function (e) {
            const val = e.target.value
            this.$emit('update:modelValue', val)
        }
    }
}
</script>

<style scoped>
textarea {
    border-radius: 1em;
    background: white;
    color: var(--ion-color-step-300);
    border: 1px solid var(--ion-color-step-900);
    padding: 1em;
    font-size: 14px;
    min-height: 6em;
}

.full {
    width: 100%;
}
</style>