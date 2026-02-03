<script setup>
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useCartStore } from "../stores/cartStore";
import axios from "axios";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const carId = props.id;
const car = ref(null);

const router = useRouter();
const cartStore = useCartStore();

const startDate = ref("");
const endDate = ref("");

const userRole = ref(localStorage.getItem("user_role"));

const estimatedPrice = computed(() => {
    if (startDate.value && endDate.value && car.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const diffTime = Math.abs(end - start);
        const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));

        if (diffHours <= 0) return 0;

        return diffHours * car.value.hourly_price;
    }
    return 0;
});

const minDate = computed(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
});

const handleAddToCart = () => {
    if (!userRole.value) {
        alert("You must be logged in as a customer to add items to the cart.");
        return;
    }

    if (!startDate.value || !endDate.value) {
        alert("Please select both start and end dates.");
        return;
    }

    if (new Date(endDate.value) <= new Date(startDate.value)) {
        alert("End date must be after start date.");
        return;
    }

    cartStore.addToCart(car.value, startDate.value, endDate.value);

    const offcanvas = document.getElementById("reservationOffcanvas");
    const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvas);

    if (offcanvasInstance) {
        offcanvasInstance.hide();
    }
};

onMounted(async () => {
    try {
        const carJSON = await axios.get(`/api/cars/${carId}`);
        car.value = carJSON.data;
    } catch (error) {
        console.error("Error fetching car details:", error);
    }
});

</script>

<style scoped>
  @import "../../css/admin_style.css";
  @import "../../css/cars_style.css";
</style>

<template>
    <div v-if="car" class="container py-5">
        <!-- HERO  -->
        <div class="hero-image-container img position-relative my-5">
            <img :src="`/images/cars/covers/${carId}-cvr.webp`" class="img-fluid w-100"
                :alt="`${car.brand} ${car.model}`" style="max-height: 60vh; object-fit: cover" />
            <div class="position-absolute bottom-0 start-0 p-4 text-white" style="
                    background: linear-gradient(
                        to top,
                        rgba(0, 0, 0, 0.7),
                        transparent
                    );
                    width: 100%;
                ">
                <h2 class="fw-bold">{{ car.brand }} {{ car.model }}</h2>
                <p class="fs-4"> {{ car.hourly_price }}€ per hour</p>
            </div>
        </div>

        <!-- CALL TO ACTION -->
        <div class="text-center mb-5">
            <button class="btn bg-primary-cta btn-lg px-5 py-3" data-bs-toggle="offcanvas"
                data-bs-target="#reservationOffcanvas">
                <i class="bi bi-cart-check me-2"></i> Book Now
            </button>
        </div>

        <!-- DESCRIPTION -->
        <section class="dashboard-title mb-4 p-3 shadow-sm">
            <h4 class="mb-3">Description</h4>
            <hr class="my-2">
            <p class="fs-5 mb-4 p-3 text-white">{{ car.description }}</p>
        </section>

        <!-- SPECIFICATIONS -->
        <section class="dashboard-title mb-4 p-3 shadow-sm">
            <h4 class="mb-3">Specifications</h4>
            <ul class="list-group mb-4 p-3">
                <li class="list-group-item">
                    <strong><i class="bi bi-calendar me-2"></i> Manufacturing Year: </strong>
                    <span class="text-white"> {{ car.manufacturing_year }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-gear me-2"></i> Traction: </strong>
                    <span class="text-white"> {{ car.traction }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-gear me-2"></i> Transmission: </strong>
                    <span class="text-white"> {{ car.transmission }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-gear me-2"></i> Engine: </strong>
                    <span class="text-white"> {{ car.engine }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-gear me-2"></i> Engine Capacity: </strong>
                    <span class="text-white"> {{ car.engine_capacity }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-door-closed me-2"></i> Doors: </strong>
                    <span class="text-white"> {{ car.doors }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-fuel-pump me-2"></i> Fuel Type: </strong>
                    <span class="text-white"> {{ car.fuel_type }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-palette me-2"></i> Color: </strong>
                    <span class="text-white"> {{ car.color }}</span>
                    <hr class="my-2">
                </li>
                <li class="list-group-item">
                    <strong><i class="bi bi-info-circle me-2"></i> Status: </strong>
                    <span class="text-white"> {{ car.status }}</span>
                </li>
            </ul>
        </section>

        <!-- RESERVATION OFFCANVAS -->
        <div class="offcanvas offcanvas-end custom-panel color-secondary" tabindex="-1" id="reservationOffcanvas" aria-labelledby="offcanvasLabel">

            <div class="offcanvas-header panel-header border-0">
                <h5 class="panel-title">Configure Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column">

                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <img :src="`/images/cars/thumbnails/${carId}-thm.webp`" class="rounded"
                        style="width: 80px; height: 50px; object-fit: cover;">
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold">{{ car.brand }} {{ car.model }}</h6>
                        <small class="text-white">{{ car.hourly_price }}€ / hour</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pick-up Date</label>
                    <input type="datetime-local" class="form-control" v-model="startDate" :min="minDate">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Drop-off Date</label>
                    <input type="datetime-local" class="form-control" v-model="endDate" :min="startDate || minDate">
                </div>

                <div v-if="estimatedPrice > 0" class="inner-panel-primary text-center p-3">
                    <small class="d-block text-uppercase mb-1">Total Estimated</small>
                    <span class="display-6 text-white fw-bold">{{ estimatedPrice.toLocaleString('es-ES') }}€</span>
                </div>
                <div v-else class="alert inner-panel border-0 text-center mb-auto text-white">
                    <small>Select dates to see price</small>
                </div>

                <div class="mt-4">
                    <button @click="handleAddToCart" class="btn bg-primary-cta w-100 fs-5"
                        :disabled="!startDate || !endDate || estimatedPrice <= 0">
                        Add to Cart
                    </button>
                    <div class="text-center mt-3">
                        <small class="text-white">
                            You won't be charged yet.
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- LOADING STATE -->
    <div v-else class="container text-center py-5">
        <div class="spinner-border text-dark" role="status"></div>
        <p class="mt-3 fs-5">Loading vehicle…</p>
    </div>
</template>