<template>
    <textarea @change="$emit('update:modelValue', $event.target.value)" :rows="rows" >{{ modelValue }}</textarea>
</template>

<script>
export default {
    props: {
        rows: {
            type: Number,
            default: 3
        }
    }
}
</script>

<style scoped>
    textarea {
        padding: 0.4em 1em;
        font-size: 1em;
        border: 1px solid #bcb4b1;
        /* border: 1px solid var(--vt-color-step-900); */
        border-radius: 1em;
        outline: none;
        background: white;
        font-size: 14px;
        color: var(--vt-color-step-300);
        vertical-align: middle;
        line-height: 2em;
        min-width: 240px;
    }
</style>