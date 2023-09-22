<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">
        <ion-tab-button id="dashboard" tab="dashboard" href="/dashboard">
          <ion-icon aria-hidden="true" :icon="home" />
        </ion-tab-button>

        <!-- <ion-tab-button id="list" tab="list" href="/list" disabled>
          <ion-icon aria-hidden="true" :icon="heart" />
        </ion-tab-button>

        <ion-tab-button id="photo" tab="photo" href="/photo" disabled>
          <ion-icon aria-hidden="true" :icon="notifications" />
        </ion-tab-button> -->

        <ion-tab-button tab="profile" href="/profile">
          <ion-icon aria-hidden="true" :icon="person" />
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script setup lang="ts">
/* eslint-disable */
import { IonTabBar, IonTabButton, IonTabs, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import { person, notifications, home, heart } from 'ionicons/icons';
</script>

<style scoped>
ion-tab-bar {
  --background: #bbe0e0;
}

ion-tab-button {
  /* background-color: #E3F4F4; */
  --color-selected: #114167;
}

ion-icon {
  font-size: 25px;
}
</style>