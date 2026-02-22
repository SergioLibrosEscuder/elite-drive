<!-- Guillermo Soto ============================================================================= -->

<script setup>
import RoadCarousel from "../components/RoadCarousel.vue";

// Images used in road carousel
const roadImages1 = [
    '/images/index/road/road1_1.jpg', '/images/index/road/road1_2.jpg',
    '/images/index/road/road1_3.jpg', '/images/index/road/road1_4.jpg'
];
const roadImages2 = [
    '/images/index/road/road2_1.jpg', '/images/index/road/road2_2.jpg',
    '/images/index/road/road2_3.jpg', '/images/index/road/road2_4.jpg'
];
const roadImages3 = [
    '/images/index/road/road3_1.jpg', '/images/index/road/road3_2.jpg',
    '/images/index/road/road3_3.jpg', '/images/index/road/road3_4.jpg'
];
const roadImages4 = [
    '/images/index/road/road4_1.jpg', '/images/index/road/road4_2.jpg',
    '/images/index/road/road4_3.jpg', '/images/index/road/road4_4.jpg'
];
</script>

<style scoped>
@import "../../css/index_style.css";
@import "../../css/admin_style.css";
</style>

<template>

    <!-- CONTENT ==================================================================================== -->

    <!-- HERO ================================================================= -->

    <section class="hero-video position-relative overflow-hidden">
        <div class="video-background">
            <iframe 
                src="https://www.youtube.com/embed/5WQTtDKM0lE?autoplay=1&mute=1&loop=1&playlist=5WQTtDKM0lE&controls=0&showinfo=0&rel=0&start=15" 
                frameborder="0" 
                allow="autoplay; encrypted-media" 
                allowfullscreen>
            </iframe>
        </div>

        <div class="container position-relative z-index-1 text-center pt-5 mt-5">
            <h1 class="display-1 fw-bold">Boost your Motion</h1>
            <h2 class="lead mb-4 d-none d-md-block">Discover the best Luxury Car experiences</h2>
        </div>
    </section>

    <!-- 3D MODEL ============================================================= -->

    <section class="d-flex align-items-end text-center pt-5">
        <div class="container">
            <div class="sketchfab-embed-wrapper custom-panel shadow-sm p-0 overflow-hidden">
                <iframe 
                    class="w-100 v-3d-model"
                    title="McLaren F1" 
                    frameborder="0" 
                    allowfullscreen 
                    mozallowfullscreen="true" 
                    webkitallowfullscreen="true" 
                    allow="autoplay; fullscreen; xr-spatial-tracking" 
                    src="https://sketchfab.com/models/d96ebc208df54311964cad24f83e1656/embed"> 
                </iframe>
            </div>
        </div>
    </section>

    <!-- DESCRIPTION ========================================================== -->

    <section class="py-5">
        <div class="container">
            <div class="custom-panel mb-4 p-4 shadow-sm">
                <h2 class="text-center fw-bold mt-2 mb-3">Do you dream of getting behind the wheel of a luxury car?</h2>
                <hr class="my-2">
                <p class="fs-5 p-3 text-center text-white">
                    Can you imagine driving around your city in a luxury car like a Lamborghini or a Ferrari?
                    <br><br>
                    At Elite Drive, we have the best options to make your dream a reality.
                    Enjoy driving the best luxury cars on the market with Elite Drive.
                    If you're looking to rent a high-end car, don't hesitate to contact us for a free consultation.
                    <br><br>
                    We offer the most exclusive brands.
                </p>
            </div>
        </div>
    </section>

    <!-- CAROUSEL ============================================================= -->

    <section class="py-5">
        <div class="container">

            <h2 class="text-center fw-bold mb-5">The most exclusive vehicles at your complete disposal</h2>

            <div id="routeCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner shadow">
                    <div class="carousel-item active">
                        <img :src="'/images/index/carousel/carousel_1.jpg'" class="d-block w-100" alt="Route 1">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador LP750-4 SV</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable
                                rear wing.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img :src="'/images/index/carousel/carousel_2.jpg'" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador LP700-4 SV</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable
                                rear wing.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img :src="'/images/index/carousel/carousel_3.jpg'" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Pickup Ram 1500</h5>
                            <p>Robust, bold, and packed with advanced technology and premium comfort.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img :src="'/images/index/carousel/carousel_4.jpg'" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Chevrolet Corvette C8 Stingray</h5>
                            <p>Member of the high society of performance. The performance you want and the functionality
                                you need.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img :src="'/images/index/carousel/carousel_5.jpg'" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador SVJ</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable
                                rear wing.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#routeCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#routeCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS ========================================================= -->

    <section class="py-5">
        <div class="container">

            <h2 class="text-center fw-bold mb-5">How it Works</h2>

            <div class="mb-5 custom-panel">
                <img :src="'/images/index/how_it_works.jpg'" alt="Orange car"
                    class="img-fluid shadow-lg w-100 object-fit-cover" style="max-height: 300px;">
            </div>

            <div class="row g-4 justify-content-center align-items-stretch">
                
                <div class="col-md-4">
                    <div class="custom-panel p-4 shadow-sm text-center h-100 d-flex flex-column">
                        <i class="bi bi-car-front fs-1 mb-3"></i>
                        <h3 class="fw-bold text-white mb-3">
                            <span class="color-secondary">First</span>
                            choose your dream car
                        </h3>
                        <p class="mb-0">
                            Browse our exclusive fleet of luxury vehicles. From powerful sports cars to elegant sedans,
                            find the perfect match for your experience.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="custom-panel p-4 shadow-sm text-center h-100 d-flex flex-column">
                        <i class="bi bi-calendar-check fs-1 mb-3 text-orange"></i>
                        <h3 class="fw-bold text-white mb-3">
                            <span class="color-secondary">Second</span>
                            select dates & location
                        </h3>
                        <p class="mb-0">
                            Pick your desired pick-up and drop-off dates and locations. We offer flexible options
                            to fit your schedule seamlessly.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="custom-panel p-4 shadow-sm text-center h-100 d-flex flex-column">
                        <i class="bi bi-credit-card fs-1 mb-3 text-orange"></i>
                        <h3 class="fw-bold text-white mb-3">
                            <span class="color-secondary">Finally</span>
                            complete your secure payment
                        </h3>
                        <p class="mb-0">
                            Finalize your booking with our easy and secure payment process.
                            Get ready to drive your luxury vehicle!
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SERVICES ============================================================= -->

    <section class="py-5">
        <div class="container">

            <h2 class="text-center fw-bold mb-5">Additional services</h2>

            <div class="row g-4 align-items-stretch">
                <div class="col-md-4">
                    <div class="service-image-container h-100">
                        <img :src="'/images/index/services.jpg'" alt="Luxury Service"
                            class="img-fluid  shadow object-fit-cover h-100">
                    </div>
                </div>

                <div class="col-md-8 d-flex flex-column justify-content-between">
                    <div class="service-box p-4 mb-3 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-person-badge fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Chauffeur Service</h4>
                                <p class="mb-0">Professional drivers at your disposal for a premium, stress-free travel
                                    experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 mb-3 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Custom Delivery</h4>
                                <p class="mb-0">We deliver your vehicle to any specific point you choose, from airports
                                    to private villas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 mb-3 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-map fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Driving Routes</h4>
                                <p class="mb-0">Curated scenic routes designed to let you experience the full power of
                                    our luxury fleet.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-speedometer2 fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Track Days & Coaching</h4>
                                <p class="mb-0">Unleash the full potential of our supercars on professional racing
                                    circuits with expert technical support.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ROUTES CAROUSEL ====================================================== -->

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">The world is too big to stay in the same place</h2>

            <div class="d-flex justify-content-center gap-5 my-5 flex-wrap">
                <RoadCarousel :images="roadImages1" />
                <RoadCarousel :images="roadImages2" class="d-none d-md-block" />
                <RoadCarousel :images="roadImages3" class="d-none d-lg-block" />
                <RoadCarousel :images="roadImages4" class="d-none d-xl-block" />
            </div>
        </div>
    </section>

</template>
