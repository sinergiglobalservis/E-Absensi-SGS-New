<template>
    <div class="container-app">
        <div>Daftar Aplikasi</div>
        <div class="item-app" v-for="i in 15" :key="i">
            Aplikasi {{ i }}
        </div>
    </div>
</template>

<script>
export default {
    
}
</script>

<style scoped>
    .container-app {
        margin: 0 1em;
    }
    .container-app .item-app {
        padding: 1em 1.5em;
        margin: 0.5em 0 0.5em 0.5em;
        color: var(--ion-color-step-100);
        background: var(--ion-color-step-900);
        border: none;
        border-radius: 0.5em;
        width: calc( 50% - 1em ) ;
        float: left;
    }
</style>