<!-- Sergio Libros ============================================================================= -->

<script setup>
import { useRouter } from 'vue-router';
import { useCartStore } from '../../stores/cartStore';

// Use router to manage redirections
const router = useRouter();
// Use pinia store to pass data between views
const cartStore = useCartStore();

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

// Redirect to checkout page function
const goToCheckout = () => {
    // Get cart modal HTML
    const modalEl = document.getElementById('cartModal');
    // Get cart modal Bootstrap
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    // Hide cart modal
    if (modalInstance) modalInstance.hide();
    // Redirect to checkout view
    router.push('/checkout');
};
</script>

<template>

    <!-- CART MODAL ================================================================================ -->

    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 p-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cart-check me-2"></i>Your Reservations
                    </h5>
                    <button type="button" class="btn-close me-1" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Message if cart is empty -->
                    <div v-if="cartStore.count === 0" class="text-center py-5">
                        <i class="bi bi-cart-x text-white" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-white">Your cart is empty.</p>
                    </div>
                    <!-- Show items in cart -->
                    <ul v-else class="list-group list-group-flush">
                        <li v-for="item in cartStore.cartItems" :key="item.id" class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-bold">{{ item.vehicle.brand }} {{ item.vehicle.model }}</h6>
                                    <small class="text-white d-block" style="font-size: 0.8rem;">
                                        {{ formatDate(item.start) }} <i class="bi bi-arrow-right-short"></i> {{
                                            formatDate(item.end) }}
                                    </small>
                                </div>
                                <div class="text-end ms-2">
                                    <div class="fw-bold fs-6 text-white">{{ Number(item.price).toLocaleString('es-ES')
                                    }}€</div>
                                    <!-- Remove from cart button -->
                                    <button @click="cartStore.removeFromCart(item.id)"
                                        class="btn btn-link text-danger p-0 mt-1" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="modal-footer d-flex mt-4 pt-3 justify-content-between border-0">
                    <!-- Total cart amount -->
                    <div>
                        <p class="mb-0"><span class="color-secondary">Total: </span>
                            <span class="fs-5 fw-bold">{{
                                Number(cartStore.total.toFixed(2)).toLocaleString('es-ES') }}€</span>
                        </p>
                    </div>
                    <div>
                        <!-- Close modal button -->
                        <button type="button" class="btn bg-primary-cta me-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Close
                        </button>
                        <!-- Go to checkout button only if there are items in cart -->
                        <button v-if="cartStore.count > 0" @click="goToCheckout" class="btn bg-primary-cta">
                            <i class="bi bi-arrow-right-circle"></i> Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>