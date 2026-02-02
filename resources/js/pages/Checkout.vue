<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cartStore';
import axios from 'axios';

const cartStore = useCartStore();
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
    const errors = [];

    // Realizar reservas para cada ítem en el carrito
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
            errors.push(item.vehicle.model);
        }
    }

    isProcessing.value = false;

    if (errors.length > 0) {
        alert(`Could not book: ${errors.join(', ')}. Please try again.`);
    } else {
        cartStore.clearCart();
        alert('Reservation confirmed successfully! Thank you.');
        router.push('/profile');
    }
};
</script>

<template>
    <div class="container py-5">
        <h2 class="fw-bold mb-4">Checkout</h2>

        <div v-if="cartStore.count > 0" class="row g-5">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-3" v-for="item in cartStore.cartItems" :key="item.id">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img :src="`/images/cars/thumbnails/${item.vehicle.id}-thm.webp`"
                                class="img-fluid rounded-start h-100" style="object-fit: cover; min-height: 150px;"
                                alt="Car" @error="$event.target.src = '/images/placeholder.webp'">
                        </div>

                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title fw-bold mb-0">{{ item.vehicle.brand }} {{ item.vehicle.model
                                        }}</h5>
                                    <span class="fs-5 fw-bold text-primary">{{
                                        Number(item.price).toLocaleString('es-ES') }}€</span>
                                </div>

                                <div class="bg-light p-3 rounded border small mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Pick-up:</span>
                                        <span class="fw-bold">{{ formatDate(item.start) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Drop-off:</span>
                                        <span class="fw-bold">{{ formatDate(item.end) }}</span>
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

            <div class="col-lg-4">
                <div class="card shadow border-0 bg-light sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Summary</h4>

                        <div class="d-flex justify-content-between mb-2 small" v-for="item in cartStore.cartItems"
                            :key="item.id">
                            <span class="text-truncate pe-2" style="max-width: 200px;">{{ item.vehicle.brand }} {{
                                item.vehicle.model }}</span>
                            <span>{{ Number(item.price).toLocaleString('es-ES') }}€</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-3 fw-bold text-success">{{ Number(cartStore.total).toLocaleString('es-ES')
                                }}€</span>
                        </div>

                        <button @click="handlePayment" class="btn btn-dark w-100 py-3 fw-bold shadow-sm"
                            :disabled="isProcessing">
                            <span v-if="isProcessing" class="spinner-border spinner-border-sm me-2"></span>
                            {{ isProcessing ? 'Processing Payment...' : 'Confirm & Pay' }}
                        </button>

                        <div class="text-center mt-3">
                            <small class="text-muted d-block"><i class="bi bi-lock-fill"></i> Secure SSL Payment</small>
                            <small class="text-muted" style="font-size: 0.75rem;">By clicking, you agree to our
                                Terms.</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>