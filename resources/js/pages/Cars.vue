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
    status: "",
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
const statuses = computed(() => getAll("status"));

const filteredCars = computed(() => {
    return cars.value.filter((c) => {
        if (filters.value.query && !(c.brand + " " + c.model).toLowerCase().includes(filters.value.query.toLowerCase())) return false;
        if (filters.value.brand && c.brand !== filters.value.brand) return false;
        if (filters.value.fuel && c.fuel_type !== filters.value.fuel) return false;
        if (filters.value.doors && Number(c.doors) !== Number(filters.value.doors)) return false;
        if (filters.value.status && c.status !== filters.value.status) return false;
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
        status: "",
        minYear: null,
        maxYear: null,
    };
};

function getThumbnail(car) {
    return `/images/cars/thumbnails/${car.id}-thm.webp`;
};
</script>

<style scoped>
  @import "../../css/admin_style.css";
  @import "../../css/cars_style.css";
</style>


<template>
    <div class="container py-5">
        <div class="dashboard-title mb-4 p-3 shadow-sm d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Vehicle Fleet</h4>
            <button class="btn bg-primary-cta d-md-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#filtersCollapse">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
        </div>

        <div class="row">
            <!-- SIDEBAR FILTERS -->
            <aside class="col-12 col-md-3 mb-4">
                <div class="collapse d-md-block" id="filtersCollapse">
                    <div class="panel-header p-3 border-bottom-0">
                        <h5 class="panel-title">
                            <i class="bi bi-sliders me-2"></i> Filters
                        </h5>
                    </div>
                    <div class="panel-content p-3">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-search me-2"></i> Search
                            </label>
                            <input v-model="filters.query" type="search" class="form-control"
                                placeholder="Brand or model" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-car-front me-2"></i> Brand
                            </label>
                            <select v-model="filters.brand" class="form-select">
                                <option value="">Any</option>
                                <option v-for="b in brands" :key="b" :value="b">{{ b }}</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">
                                     <i class="bi bi-cash me-2"></i> Min price</label>
                                <input v-model.number="filters.minPrice" type="number" class="form-control" min="0"
                                    placeholder="€" />
                            </div>
                            <div class="col-6">
                                <label class="form-label">
                                    <i class="bi bi-cash me-2"></i> Max price</label>
                                <input v-model.number="filters.maxPrice" type="number" class="form-control" min="0"
                                    placeholder="€" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-door-closed me-2"></i> Doors</label>
                            <select v-model="filters.doors" class="form-select">
                                <option value="">Any</option>
                                <option v-for="d in doors" :key="d" :value="d">{{ d }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-fuel-pump me-2"></i> Fuel</label>
                            <select v-model="filters.fuel" class="form-select">
                                <option value="">Any</option>
                                <option v-for="f in fuels" :key="f" :value="f">{{ f }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-info-circle me-2"></i> Status</label>
                            <select v-model="filters.status" class="form-select">
                                <option value="">Any</option>
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-2"></i> Min year</label>
                                <input v-model.number="filters.minYear" type="number" class="form-control"
                                    placeholder="YYYY" />
                            </div>
                            <div class="col-6">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-2"></i> Max year</label>
                                <input v-model.number="filters.maxYear" type="number" class="form-control"
                                    placeholder="YYYY" />
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn bg-primary-cta" @click="resetFilters">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Reset
                            </button>
                            <button class="btn bg-primary-cta d-md-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
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
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">{{ car.brand }} {{ car.model }}</h6>
                                <p class="mb-3 fw-bold">
                                    €{{ car.hourly_price }}
                                    <span class="price-label">/ hour</span>
                                </p>
                                <div class="mt-auto d-grid">
                                    <RouterLink :to="`/cars/${car.id}`" class="btn bg-primary-cta btn-sm">
                                        <i class="bi bi-info-circle me-2"></i> Details
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

    &:hover {
        transform: translateY(-6px);
    }
}
</style>