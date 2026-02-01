<script setup>
import { useRouter } from 'vue-router';
import { useCartStore } from '../../stores/cartStore';

const router = useRouter();
const cartStore = useCartStore();

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

const goToCheckout = () => {
    const modalEl = document.getElementById('cartModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    router.push('/checkout');
};
</script>

<template>
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="bi bi-cart-check me-2"></i>Your Reservations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div v-if="cartStore.count === 0" class="text-center py-5">
                        <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted">Your cart is empty.</p>
                        <button class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal">Browse Cars</button>
                    </div>

                    <ul v-else class="list-group list-group-flush">
                        <li v-for="item in cartStore.cartItems" :key="item.cartId" class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-bold">{{ item.vehicle.brand }} {{ item.vehicle.model }}</h6>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">
                                        {{ formatDate(item.start) }} <i class="bi bi-arrow-right-short"></i> {{
                                            formatDate(item.end) }}
                                    </small>
                                    <small class="text-primary fw-bold">{{ item.hours }}h x {{ item.vehicle.hourly_price
                                        }}€</small>
                                </div>

                                <div class="text-end ms-2">
                                    <div class="fw-bold fs-6">{{ item.price }}€</div>
                                    <button @click="cartStore.removeFromCart(item.cartId)"
                                        class="btn btn-link text-danger p-0 mt-1" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="modal-footer d-flex justify-content-between bg-light">
                    <div>
                        <small class="text-muted">Total:</small>
                        <span class="fs-5 fw-bold ms-2 text-dark">{{ cartStore.total.toFixed(2) }}€</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary me-2"
                            data-bs-dismiss="modal">Close</button>
                        <button v-if="cartStore.count > 0" @click="goToCheckout" class="btn btn-sm btn-primary">
                            Checkout <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>