<!-- Sergio Libros - Programming ================================================================ -->
<!-- Guillermo Soto - Style ===================================================================== -->

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cartStore';
import axios from 'axios';
import { useToast } from '../composables/useToast';

// Toast object
const toast = useToast();

// Pinia store
const cartStore = useCartStore();

// Router to redirect
const router = useRouter();
const isProcessing = ref(false);

// Avoid accessing checkout if cart is empty
if (cartStore.count === 0) {
    router.push('/cars');
}

// Date formatting
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

// Payment handling
const handlePayment = async () => {
    isProcessing.value = true;
    // Errors array
    const errors = [];

    // Make a reservation for every item in cart
    for (const item of cartStore.cartItems) {
        try {
            await axios.post('/reservations', {
                vehicle_id: item.vehicle.id,
                start_date: item.start,
                end_date: item.end,
                amount: item.price
            });
        } catch (error) {
            console.error(`Error booking ${item.vehicle.brand}:`, error);
            // If error, push it into array
            errors.push(item.vehicle.model);
        }
    }

    isProcessing.value = false;

    if (errors.length > 0) {
        toast.error(`Could not book: ${errors.join(', ')}. Please try again.`, "Checkout Error");
    } else {
        cartStore.clearCart();
        toast.success('Reservation confirmed successfully! Thank you.', "Checkout");
        // Redirect to reservation list profile section
        router.push({ path: '/profile', hash: '#reservation-list' });
    }
};
</script>

<style scoped>
@import "../../css/admin_style.css";
@import "../../css/checkout_style.css";
</style>

<template>
    <div class="container mt-5">

        <h4 class="dashboard-title mb-4 p-3 shadow-sm">Checkout</h4>

        <div class="row g-5">

            <!-- PRODUCT ITEM ==================================================== -->

            <div class="col-lg-8" v-if="cartStore.count > 0">
                <div class="card panel-content shadow-sm mb-3" v-for="item in cartStore.cartItems" :key="item.id">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img :src="`/images/cars/thumbnails/${item.vehicle.id}-thm.webp`" class="img-fluid h-100"
                                style="object-fit: cover; min-height: 150px;" alt="Car"
                                @error="$event.target.src = '/images/placeholder.webp'">
                        </div>

                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title fw-bold mb-0 color-secondary">{{ item.vehicle.brand }} {{
                                        item.vehicle.model
                                        }}</h5>
                                    <span class="fs-5 fw-bold text-white">{{
                                        Number(item.price).toLocaleString('es-ES') }}€</span>
                                </div>

                                <div class="inner-panel-primary p-3 small mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Pick-up:</span>
                                        <span class="fw-bold text-white">{{ formatDate(item.start) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Drop-off:</span>
                                        <span class="fw-bold text-white">{{ formatDate(item.end) }}</span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button @click="cartStore.removeFromCart(item.id)"
                                        class="btn btn-sm btn-outline-danger" :disabled="isProcessing">
                                        <i class="bi bi-trash me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8" v-else>
                <div class="card panel-content shadow-sm mb-3">
                    <div class="row g-0">
                        <div class="col p-5 text-center">
                            THERE ARE NO VEHICLES IN CART
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUMARY ========================================================== -->

            <div class="col-lg-4">
                <div class="dashboard-title sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Summary</h4>

                        <div class="d-flex justify-content-between mb-2 small" v-for="item in cartStore.cartItems"
                            :key="item.id">
                            <span class="pe-2" style="max-width: 200px;">{{ item.vehicle.brand }} {{
                                item.vehicle.model }}</span>
                            <span class="">{{ Number(item.price).toLocaleString('es-ES') }}€</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold">Total:</span>
                            <span class="fs-3 fw-bold text-white">{{ Number(cartStore.total).toLocaleString('es-ES')
                            }}€</span>
                        </div>

                        <button v-if="cartStore.count > 0" @click="handlePayment" class="btn bg-primary-cta w-100 py-2"
                            :disabled="isProcessing">
                            <span v-if="isProcessing" class="me-2"></span>
                            {{ isProcessing ? 'Processing Payment...' : 'Confirm & Pay' }}
                        </button>

                        <button v-else disabled="true" class="btn bg-dark-subtle w-100 py-2">
                            <span>Cart is Empty</span>
                        </button>

                        <div class=" text-center mt-3">
                            <small class="text-white d-block"><i class="bi bi-lock-fill"></i> Secure SSL
                                Payment</small>
                            <small class="text-white" style="font-size: 0.75rem;">By clicking, you agree to our
                                Terms.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM IMAGE ==================================================== -->

        <section class="container-fluid px-0 mt-4 mb-5">
            <div class="row g-0">
                <div class="col-12 text-center">
                    <img :src="'/images/checkout/landscape.jpg'" alt="Banner ad" class="img-bottom-banner shadow-sm">
                </div>
            </div>
        </section>
    </div>
</template>