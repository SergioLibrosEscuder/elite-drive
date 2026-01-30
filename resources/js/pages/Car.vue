<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const carId = props.id;

const car = ref(null);


onMounted(async () => {
    const carJSON = await axios.get(`/api/cars/${carId}`);
    car.value = carJSON.data;
    console.log("Coche recibido:", carJSON.data);
});
</script>

<template>
    <div v-if="car" class="container py-4">
        <!-- HERO  -->
        <div class="position-relative mb-5">
            <!-- <img :src="car.images[0]" class="img-fluid rounded w-100" :alt="car.name"
                style="max-height: 60vh; object-fit: cover" /> -->
            <div class="position-absolute bottom-0 start-0 p-4 text-white" style="
                    background: linear-gradient(
                        to top,
                        rgba(0, 0, 0, 0.7),
                        transparent
                    );
                    width: 100%;
                ">
                <h1 class="display-4 fw-bold">{{ car.brand }} {{ car.model }}</h1>
                <p class="fs-4">€{{ car.hourly_price }} day</p>
            </div>
        </div>

        <!-- CALL TO ACTION -->
        <div class="text-center mb-5">
            <button class="btn btn-dark btn-lg px-5 py-3">Book Now</button>
        </div>

        <!-- GALLERY -->
        <!-- <section class="mb-5">
            <h2 class="mb-3">Gallery</h2>
            <div class="row g-3">
                <div v-for="(img, i) in car.images" :key="i" class="col-12 col-md-4">
                    <img :src="img" class="img-fluid rounded shadow-sm" />
                </div>
            </div>
        </section> -->

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

        <!-- DESCRIPTION -->
        <section class="mb-5">
            <h2 class="mb-3">Description</h2>
            <p class="fs-5">{{ car.description }}</p>
        </section>
    </div>

    <!-- LOADING STATE -->
    <div v-else class="container text-center py-5">
        <div class="spinner-border text-dark" role="status"></div>
        <p class="mt-3 fs-5">Loading vehicle…</p>
    </div>
</template>
