<template>
    <select @change="$emit('update:modelValue', $event.target.value)">
        <option value="">Pilih</option>
        <option v-for="(v, k) in option" :key="k" :value="v.value" :selected="v.value == curr">{{ v.text }}</option>
    </select>
</template>

<script>
export default {
    props: {
        option: {
            default() {
                return [];
            }
        },
        modelValue: {
            default: ''
        }
    },
    data() {
        return {
            curr: ''
        };
    },
    mounted() {
        // console.log(this.option,this.modelValue);
        this.curr = this.modelValue;
    },
    watch: {
        // modelValue: function (n, o) {
        //     this.curr = n;
        //     // console.log(n, o)
        // }
    }

}
</script>

<style scoped>
select {
    padding: 0.4em 1em;
    font-size: 1em;
    border: 1px solid #bcb4b1;
    /* border: 1px solid var(--vt-color-step-900); */
    border-radius: 1em;
    outline: none;
    height: 3em;
    background: white;
    font-size: 14px;
    color: var(--vt-color-step-300);
    vertical-align: middle;
    line-height: 2em;
    width: 100%;
    /* min-width: 240px; */
}
</style>