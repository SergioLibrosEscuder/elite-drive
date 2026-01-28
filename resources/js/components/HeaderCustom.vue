<template>
    <!-- HEADER / NAVBAR ============================================================================ -->

    <header>
        <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">ELITE DRIVE</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="cars.html">Cars</a></li>
                        <li class="nav-item"><a class="nav-link" href="gallery.html">Gallery</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                        <li class="nav-item ms-lg-3">
                            <button class="btn bg-primary-cta btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-person-circle me-2"></i> Login
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>
