<!-- Guillermo Soto -->

<template>
    <!-- HEADER / NAVBAR ============================================================================ -->

    <header>
        <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <!-- RouterLink to Home page in logotype-->
                <RouterLink class="navbar-brand fw-bold" to="/">ELITE DRIVE</RouterLink>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <!-- RouterLink to Home page -->
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/">Home</RouterLink>
                        </li>
                        <!-- RouterLink to Vehicle Fleet page -->
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/cars">Cars</RouterLink>
                        </li>
                        <!-- RouterLink to Gallery page -->
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/gallery">Gallery</RouterLink>
                        </li>
                        <!-- RouterLink to Contact page -->
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/contact">Contact</RouterLink>
                        </li>
                        <!-- Conditional links based on user role -->
                        <li v-if="isAdmin" class="nav-item">
                            <RouterLink class="nav-link" to="/admin">Admin</RouterLink>
                        </li>
                        <li v-if="isCustomer" class="nav-item">
                            <RouterLink class="nav-link" to="/profile">My Profile</RouterLink>
                        </li>
                        <!-- Cart icon only for customers -->
                        <li class="nav-item ms-2 me-2" v-if="isCustomer">
                            <a class="nav-link position-relative cursor-pointer" data-bs-toggle="modal"
                                data-bs-target="#cartModal" id="cart-trigger" style="cursor: pointer;">
                                <i class="bi bi-cart3 fs-5"></i>
                                <!-- Bubble to show cart item count -->
                                <span v-if="cartStore.count > 0"
                                    class="position-absolute bottom-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.65rem;">
                                    {{ cartStore.count }}
                                </span>
                            </a>
                        </li>
                        <!-- Conditional buttons based on user role -->
                        <li class="nav-item ms-lg-3">
                            <button v-if="!isAuthenticated" class="btn bg-primary-cta btn-sm" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="bi bi-person-circle me-2"></i> Login
                            </button>

                            <button v-else @click="handleLogout" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cartStore';
import { useAuth } from '../composables/useAuth';
import { onMounted, ref } from 'vue';

// Create objects used later
const router = useRouter();
const { isAuthenticated, isAdmin, isCustomer, logout, fetchUser } = useAuth();

onMounted(async () => {
    // Get current user
    fetchUser();
})

// Use pinia store for the cart
const cartStore = useCartStore();

// Logout function
const handleLogout = async () => {
    // Await logout method from useAuth composable
    await logout();

    // Redirect to Home page
    router.push('/');
};
</script>
