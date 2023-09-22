<template>
    <input :class="(full ? 'full ' : '') + (error ? 'error ' : '')" :type="type" :placeholder="placeholder" :value="modelValue"
        :maxlength="max" @input="emiter" />
</template>

<script>
export default {
    props: {
        modelValue: {
            default: ''
        },
        placeholder: {
            default: ''
        },
        type: {
            default: 'text'
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
    data() {
        return {
            last: '',
            error: false
        };
    },
    methods: {
        emiter: function (e) {
            if (this.type == 'number') {
                const reg = /^\d|\.+$/;
                if (e.target.value == "") {
                    e.target.value = 0;
                    this.$emit('update:modelValue', e.target.value);
                } else {
                    if (reg.test(e.target.value)) {
                        this.$emit('update:modelValue', e.target.value);
                        this.last = e.target.value
                    } else {
                        e.target.value = this.last;
                        this.$emit('update:modelValue', this.last);
                    }
                }
            } else if (this.type == 'email') {
                const reg = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+\.+[a-zA-Z0-9-]+[?:.a-zA-Z0-9-]*$/;

                this.$emit('update:modelValue', e.target.value);
                this.error = e.target.value != '' ? !reg.test(e.target.value) : false;
            } else if (this.type == 'password') {
                const reg = /^[a-zA-Z0-9.@!#$%&!?^_-]*$/;

                this.$emit('update:modelValue', e.target.value);
                this.error = !reg.test(e.target.value);
            } else {
                this.$emit('update:modelValue', e.target.value);
            }
        }
    }
}    
</script>

<style scoped>
input {
    padding: 0.4em 1em;
    font-size: 1em;
    /* border: 1px solid #bcb4b1; */
    border: 1px solid var(--ion-color-step-900);
    border-radius: 5em;
    outline: none;
    height: 3em;
    background: white;
    font-size: 14px;
    color: var(--ion-color-step-300);
    line-height: 2em;
}

input::file-selector-button {
    background: white;
    color: var(--ion-color-step-300);
    border: none;
    border-right: 1px solid var(--ion-color-step-900);
    height: 100%;
}

input.full {
    width: 100%;
}

input.error {
    background-color: #fe5643;
}</style>