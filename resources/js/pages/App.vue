<script setup>
import FooterCustom from '../components/FooterCustom.vue';
import HeaderCustom from '../components/HeaderCustom.vue';

import LoginModal from '../components/modals/LoginModal.vue';
import RegisterModal from '../components/modals/RegisterModal.vue';

</script>

<template>
    <HeaderCustom />
    <router-view />
    <FooterCustom />

    <LoginModal />
    <RegisterModal />
</template>