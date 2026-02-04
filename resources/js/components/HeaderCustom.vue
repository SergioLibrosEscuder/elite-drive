<template>
    <!-- HEADER / NAVBAR ============================================================================ -->

    <header>
        <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <RouterLink class="navbar-brand fw-bold" to="/">ELITE DRIVE</RouterLink>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/">Home</RouterLink>
                        </li>
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/cars">Cars</RouterLink>
                        </li>
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/gallery">Gallery</RouterLink>
                        </li>
                        <li class="nav-item">
                            <RouterLink class="nav-link" to="/contact">Contact</RouterLink>
                        </li>
                        <!-- Conditional links based on user role -->
                        <li v-if="userRole === 'admin'" class="nav-item">
                            <RouterLink class="nav-link" to="/admin">Admin</RouterLink>
                        </li>
                        <li v-if="userRole === 'customer'" class="nav-item">
                            <RouterLink class="nav-link" to="/profile">My Profile</RouterLink>
                        </li>
                        <li class="nav-item ms-2 me-2" v-if="userRole === 'customer'">
                            <a class="nav-link position-relative cursor-pointer" data-bs-toggle="modal"
                                data-bs-target="#cartModal" style="cursor: pointer;">
                                <i class="bi bi-cart3 fs-5"></i>
                                <span v-if="cartStore.count > 0"
                                    class="position-absolute bottom-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.65rem;">
                                    {{ cartStore.count }}
                                </span>
                            </a>
                        </li>
                        <!-- Conditional buttons based on user role -->
                        <li class="nav-item ms-lg-3">
                            <button v-if="!userRole" class="btn bg-primary-cta btn-sm" data-bs-toggle="modal"
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
import { onMounted, ref } from 'vue';

const router = useRouter();
const userRole = ref(null);

onMounted(async () => {
    const res = await axios.get("/user/me");
    userRole.value = res.data.role;
})

const cartStore = useCartStore();

// Logout function
const handleLogout = () => {
    // 1. Clear browser storage
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_name');

    // 2. Update userRole ref
    userRole.value = null;

    // 3. Redirect to Home
    router.push('/');
};
</script>
