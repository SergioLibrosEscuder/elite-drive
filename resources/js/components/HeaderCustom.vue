<template>
    <!-- HEADER / NAVBAR ============================================================================ -->

    <header>
        <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <RouterLink class="navbar-brand fw-bold" to="/">ELITE DRIVE</RouterLink>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item"><RouterLink class="nav-link" to="/">Home</RouterLink></li>
                        <li class="nav-item"><RouterLink class="nav-link" to="/cars">Cars</RouterLink></li>
                        <li class="nav-item"><RouterLink class="nav-link" to="/gallery">Gallery</RouterLink></li>
                        <li class="nav-item"><RouterLink class="nav-link" to="/contact">Contact</RouterLink></li>
                        
                        <li class="nav-item"><RouterLink class="nav-link" to="/admin">Admin</RouterLink></li>
                        <li class="nav-item"><RouterLink class="nav-link" to="/profile">Profile</RouterLink></li>
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
