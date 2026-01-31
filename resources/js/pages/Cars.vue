<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const cars = ref([]);
const loading = ref(true);

const filters = ref({
    query: "",
    brand: "",
    minPrice: null,
    maxPrice: null,
    doors: "",
    fuel: "",
    minYear: null,
    maxYear: null,
});

onMounted(async () => {
    try {
        const res = await axios.get("/api/cars");
        cars.value = res.data;
    } catch (error) {
        console.error("Failed to fetch cars:", error);
    } finally {
        loading.value = false;
    }
});

function getAll(spec) {
    return Array.from(new Set(cars.value.map((c) => c[spec]).filter(Boolean))).sort();
};

function addToCart(car) {
    alert(`Added ${car.brand} ${car.model} to cart`);
}

const brands = computed(() => getAll("brand"));
const doors = computed(() => getAll("doors"));
const fuels = computed(() => getAll("fuel_type"));

const filteredCars = computed(() => {
    return cars.value.filter((c) => {
        if (filters.value.query && !(c.brand + " " + c.model).toLowerCase().includes(filters.value.query.toLowerCase())) return false;
        if (filters.value.brand && c.brand !== filters.value.brand) return false;
        if (filters.value.fuel && c.fuel_type !== filters.value.fuel) return false;
        if (filters.value.doors && Number(c.doors) !== Number(filters.value.doors)) return false;
        if (filters.value.minPrice && Number(c.hourly_price) < Number(filters.value.minPrice)) return false;
        if (filters.value.maxPrice && Number(c.hourly_price) > Number(filters.value.maxPrice)) return false;
        if (filters.value.minYear && Number(c.manufacturing_year) < Number(filters.value.minYear)) return false;
        if (filters.value.maxYear && Number(c.manufacturing_year) > Number(filters.value.maxYear)) return false;
        return true;
    });
});

const resetFilters = () => {
    filters.value = {
        query: "",
        brand: "",
        minPrice: null,
        maxPrice: null,
        doors: "",
        fuel: "",
        minYear: null,
        maxYear: null,
    };
};

function getThumbnail(car) {
    return `/images/cars/thumbnails/${car.id}-thm.webp`;
};
</script>

<template>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Vehicle Fleet</h1>
            <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#filtersCollapse">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
        </div>

        <div class="row">
            <!-- SIDEBAR FILTERS -->
            <aside class="col-12 col-md-3 mb-4">
                <div class="collapse d-md-block" id="filtersCollapse">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>

                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <input v-model="filters.query" type="search" class="form-control"
                                    placeholder="Brand or model" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <select v-model="filters.brand" class="form-select">
                                    <option value="">Any</option>
                                    <option v-for="b in brands" :key="b" :value="b">{{ b }}</option>
                                </select>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Min price</label>
                                    <input v-model.number="filters.minPrice" type="number" class="form-control" min="0"
                                        placeholder="€" />
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Max price</label>
                                    <input v-model.number="filters.maxPrice" type="number" class="form-control" min="0"
                                        placeholder="€" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Doors</label>
                                <select v-model="filters.doors" class="form-select">
                                    <option value="">Any</option>
                                    <option v-for="d in doors" :key="d" :value="d">{{ d }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fuel</label>
                                <select v-model="filters.fuel" class="form-select">
                                    <option value="">Any</option>
                                    <option v-for="f in fuels" :key="f" :value="f">{{ f }}</option>
                                </select>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Min year</label>
                                    <input v-model.number="filters.minYear" type="number" class="form-control"
                                        placeholder="YYYY" />
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Max year</label>
                                    <input v-model.number="filters.maxYear" type="number" class="form-control"
                                        placeholder="YYYY" />
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-secondary" @click="resetFilters">Reset</button>
                                <button class="btn btn-sm btn-outline-secondary d-md-none" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#filtersCollapse">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- VEHICLE GRID -->
            <main class="col-12 col-md-9">
                <div v-if="loading" class="container text-center py-5">
                    <div class="spinner-border text-dark" role="status"></div>
                    <p class="mt-3 fs-5">Loading vehicles...</p>
                </div>

                <div v-else class="row g-3">
                    <div v-for="car in filteredCars" :key="car.id" class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card vehicle-card h-100 shadow-sm">
                            <div class="position-relative">
                                <img :src="getThumbnail(car)" class="card-img-top" :alt="`${car.brand} ${car.model}`"
                                    style="height:160px; object-fit:cover;" />
                                <button @click.stop="addToCart(car)" class="btn btn-primary add-overlay"
                                    title="Add to cart">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">{{ car.brand }} {{ car.model }}</h6>
                                <p class="mb-3 fw-bold">€{{ car.hourly_price }} <small class="text-muted">/ hour</small>
                                </p>
                                <div class="mt-auto d-grid">
                                    <RouterLink :to="`/cars/${car.id}`" class="btn btn-outline-dark btn-sm">Details
                                    </RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!filteredCars.length" class="col-12">
                        <p class="text-center mb-0">No vehicles found matching the filters.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.vehicle-card {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    overflow: hidden;
}

.vehicle-card:hover {
    transform: translateY(-6px);

    & .add-overlay {
        opacity: 1;
        transform: translateY(0);
    }
}

.add-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transform: translateY(-6px);
    transition: opacity 0.15s ease, transform 0.15s ease;
}

@media (max-width: 576px) {
    .add-overlay {
        opacity: 1;
        transform: none;
    }
}
</style>