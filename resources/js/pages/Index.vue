<!-- Guillermo Soto ============================================================================= -->

<script setup>
</script>

<style scoped>
  @import "../../css/index_style.css";
</style>

<template>

    <!-- CONTENT ==================================================================================== -->

    <!-- HERO ================================================================= -->

    <hero class="hero-section d-flex align-items-start text-center pt-5">
        <div class="container mt-5">
            <h1 class="display-1 fw-bold">Boost your Motion</h1>
            <h2 class="lead mb-4">Discover the best Luxury Car experiences.</h2>
        </div>
    </hero>

    <!-- SERVICES ============================================================= -->
    
    <section class="services-section py-5">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-md-4">
                    <div class="service-image-container h-100">
                        <img src="../../img/index/services.jpg" alt="Luxury Service" class="img-fluid  shadow object-fit-cover h-100">
                    </div>
                </div>
                <div class="col-md-8 d-flex flex-column justify-content-between">
                    
                    <div class="service-box p-4 mb-3 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-person-badge fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Chauffeur Service</h4>
                                <p class="mb-0">Professional drivers at your disposal for a premium, stress-free travel experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 mb-3 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Custom Delivery</h4>
                                <p class="mb-0">We deliver your vehicle to any specific point you choose, from airports to private villas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-map fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Driving Routes</h4>
                                <p class="mb-0">Curated scenic routes designed to let you experience the full power of our luxury fleet.</p>
                            </div>
                        </div>
                    </div>
                    <div class="service-box p-4 border shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-speedometer2 fs-2 me-3 text-orange"></i>
                            <div>
                                <h4 class="fw-bold text-white">Track Days & Coaching</h4>
                                <p class="mb-0">Unleash the full potential of our supercars on professional racing circuits with expert technical support.</p>
                            </div>
                        </div>
                    </div>

                </div>
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
                        <img src="../../img/index/carousel/carousel_1.jpg" class="d-block w-100" alt="Route 1">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador LP750-4 SV</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable rear wing.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="../../img/index/carousel/carousel_2.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador LP700-4 SV</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable rear wing.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="../../img/index/carousel/carousel_3.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Pickup Ram 1500</h5>
                            <p>Robust, bold, and packed with advanced technology and premium comfort.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="../../img/index/carousel/carousel_4.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Chevrolet Corvette C8 Stingray</h5>
                            <p>Member of the high society of performance. The performance you want and the functionality you need.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="../../img/index/carousel/carousel_5.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                            <h5>Lamborghini Aventador SVJ</h5>
                            <p>Its design is aggressive and incorporates racing aerodynamics, including an adjustable rear wing.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#routeCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#routeCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- ROUTES CAROUSEL ====================================================== -->

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">The world is too big to stay in just one place</h2>
            <div id="verticalFleetCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road1.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road2.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road3.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road4.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road5.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road6.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="vertical-card shadow">
                                    <img src="../../img/index/road/road7.jpg" class="img-fluid" alt="road">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#verticalFleetCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#verticalFleetCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>
    
</template>
