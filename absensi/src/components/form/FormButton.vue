<template>
    <button :class="'rounded-button'+(outline?'-outline':'')+(full?' full':'')+(color?' '+color:'')+(small?' small':'')" :type="type"><slot /></button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: "button"
        },
        outline: {
            type: Boolean,
            default: false
        },
        full: {
            type: Boolean,
            default: false
        },
        color: {
            type: String,
            default: ''
        },
        small: {
            type: Boolean,
            default: false
        }
    },
}
</script>

<style scoped>
button {
    padding: 0 1em;
    font-weight: 500;
    font-size: 1em;
    border-radius: 5em;
    height: 3em;
    min-width: 8em;
    outline: none;
}
.rounded-button {
    border: none;
    background: rgb(111,71,189);
    background: linear-gradient(147deg, rgba(111,71,189,1) 50%, rgba(154,107,246,1) 100%); 
    color: #FFFFFF;
}
.rounded-button.green {
    background: #00A300;
}
.rounded-button.info {
    background: #7C73D9;
}
.rounded-button.danger {
    background: #fa5659;
}


.rounded-button-outline {
    background: none;
    border: 1px solid #00458E;
    color: #00458E;
}
.rounded-button-outline.green {
    border: 1px solid #00A300;
    color: #00A300;
}
.rounded-button-outline.info {
    border: 1px solid #7C73D9;
    color: #7C73D9;
}
.rounded-button-outline.danger {
    border: 1px solid #fa5659;
    color: #fa5659;
}
.full {
    width: 100%;
}
.small {
    /* padding: 0 0.5em !important;
    height: 2.2em !important; */
    min-width: 5em !important;
    font-size: 0.7em;
}
</style>