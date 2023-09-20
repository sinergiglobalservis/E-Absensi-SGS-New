<template>
    <Pages title="Beranda">
        Halaman utama setelah login
    </Pages>
</template>

<script>
import Pages from '@/components/template/Pages.vue';

export default {
    components: { Pages },
    props: {
        params: {
            default: null
        }
    },
    mounted() {
    },
    data() {
        return {
            title: 'Dashboard'
        };
    }
}
</script>

<style scoped></style>