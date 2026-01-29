<script setup>
</script>

<template>

    <h1>ELITE DRIVE</h1>

    <!-- CAROUSEL ============================= -->

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Featured Routes</h2>
            <div id="routeCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-4 shadow">
                    <div class="carousel-item active">
                        <img src="./img/index/carousel/carousel_1.jpg" class="d-block w-100" alt="Route 1">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                            <h5>El Chorro, Málaga</h5>
                            <p>Premium sport climbing experience.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./img/index/carousel/carousel_2.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                            <h5>Siurana, Tarragona</h5>
                            <p>World-class limestone limestone walls.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./img/index/carousel/carousel_3.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                            <h5>Mallos de Riglos, Huesca</h5>
                            <p>Stunning vertical conglomerate towers for adrenaline seekers.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="./img/index/carousel/carousel_4.jpg" class="d-block w-100" alt="Route 2">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                            <h5>Rodellar, Huesca</h5>
                            <p>Spectacular overhanging limestone arches and technical caves.</p>
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
    
</template>
