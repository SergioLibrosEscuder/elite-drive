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

<template>
    <div v-if="car" class="container py-5">
        <!-- HERO  -->
        <div class="position-relative my-5">
            <img :src="`/images/cars/covers/${carId}-cvr.webp`" class="img-fluid rounded w-100"
                :alt="`${car.brand} ${car.model}`" style="max-height: 60vh; object-fit: cover" />
            <div class="position-absolute bottom-0 start-0 p-4 text-white" style="
                    background: linear-gradient(
                        to top,
                        rgba(0, 0, 0, 0.7),
                        transparent
                    );
                    width: 100%;
                ">
                <h1 class="display-4 fw-bold">{{ car.brand }} {{ car.model }}</h1>
                <p class="fs-4">€{{ car.hourly_price }} per hour</p>
            </div>
        </div>

        <!-- CALL TO ACTION -->
        <div class="text-center mb-5">
            <button class="btn btn-dark btn-lg px-5 py-3" data-bs-toggle="offcanvas"
                data-bs-target="#reservationOffcanvas">Book Now</button>
        </div>

        <!-- DESCRIPTION -->
        <section class="mb-5">
            <h2 class="mb-3">Description</h2>
            <p class="fs-5">{{ car.description }}</p>
        </section>

        <!-- SPECIFICATIONS -->
        <section class="mb-5">
            <h2 class="mb-3">Specifications</h2>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Manufacturing Year:</strong> {{ car.manufacturing_year }}
                </li>
                <li class="list-group-item">
                    <strong>Traction:</strong> {{ car.traction }}
                </li>
                <li class="list-group-item">
                    <strong>Transmission:</strong> {{ car.transmission }}
                </li>
                <li class="list-group-item">
                    <strong>Engine:</strong> {{ car.engine }}
                </li>
                <li class="list-group-item">
                    <strong>Engine Capacity:</strong> {{ car.engine_capacity }}
                </li>
                <li class="list-group-item">
                    <strong>Doors:</strong> {{ car.doors }}
                </li>
                <li class="list-group-item">
                    <strong>Fuel Type:</strong> {{ car.fuel_type }}
                </li>
                <li class="list-group-item">
                    <strong>Color:</strong> {{ car.color }}
                </li>
                <li class="list-group-item">
                    <strong>Status:</strong> {{ car.status }}
                </li>
            </ul>
        </section>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="reservationOffcanvas" aria-labelledby="offcanvasLabel">

            <div class="offcanvas-header bg-dark text-white">
                <h5 class="offcanvas-title" id="offcanvasLabel">Configure Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column">

                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <img :src="`/images/cars/thumbnails/${carId}-thm.webp`" class="rounded"
                        style="width: 80px; height: 50px; object-fit: cover;">
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold">{{ car.brand }} {{ car.model }}</h6>
                        <small class="text-muted">{{ car.hourly_price }}€ / hour</small>
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

                <div v-if="estimatedPrice > 0" class="alert alert-success border-0 text-center mb-auto">
                    <small class="d-block text-uppercase mb-1">Total Estimated</small>
                    <span class="display-6 fw-bold">{{ estimatedPrice.toLocaleString('es-ES') }}€</span>
                </div>
                <div v-else class="alert alert-light border text-center mb-auto text-muted">
                    <small>Select dates to see price</small>
                </div>

                <div class="mt-4">
                    <button @click="handleAddToCart" class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow"
                        :disabled="!startDate || !endDate || estimatedPrice <= 0">
                        Add to Cart
                    </button>
                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 0.75rem;">
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