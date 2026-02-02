<script setup>
import FooterCustom from '../components/FooterCustom.vue';
import HeaderCustom from '../components/HeaderCustom.vue';
import CartModal from '../components/modals/CartModal.vue';

import LoginModal from '../components/modals/LoginModal.vue';
import RegisterModal from '../components/modals/RegisterModal.vue';

</script>

<template>
    <HeaderCustom />
    <router-view />
    <FooterCustom />

    <LoginModal />
    <RegisterModal />
    <CartModal />
</template>