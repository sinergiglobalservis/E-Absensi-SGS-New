<template>
    <VueDatePicker year-picker v-model="modelValue" @input="$emit('update:modelValue', $event.target.value)"></VueDatePicker>
</template>

<script>
export default{
    props: {},
    methods:{}
}
</script>

<style scoped>
</style>