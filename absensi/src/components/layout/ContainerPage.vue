<template>
    <div class="container-page">
        <slot />
    </div>
</template>

<style scoped>
    .container-page {
        background-color: var(--ion-color-step-950);
        border-radius: 0.8em;
        margin: 0 1em;
        padding: 1em;
    }
    .container-page * {
        color: var(--ion-color-step-100);
    }
</style>