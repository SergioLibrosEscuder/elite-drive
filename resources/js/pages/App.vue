<script setup>
import FooterCustom from '../components/FooterCustom.vue';
import HeaderCustom from '../components/HeaderCustom.vue';
import CartModal from '../components/modals/CartModal.vue';

import LoginModal from '../components/modals/LoginModal.vue';
import RegisterModal from '../components/modals/RegisterModal.vue';
import RecoverPasswordModal from '../components/modals/RecoverPasswordModal.vue';

import ToastContainer from '../components/toasts/ToastContainer.vue';

</script>

<template>
    <HeaderCustom />
    <!-- Where the view os loaded -->
    <router-view />
    <FooterCustom />

    <!-- Bootstrap elements used -->
    <LoginModal />
    <RegisterModal />
    <RecoverPasswordModal />
    <CartModal />
    <ToastContainer />
</template>