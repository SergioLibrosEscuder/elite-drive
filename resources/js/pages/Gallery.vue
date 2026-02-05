<script setup>
import { onMounted } from 'vue';
import { useToast } from '../composables/useToast';

const toast = useToast();

/* =========================================
   1. DATOS Y ESTADO
   ========================================= */
const carData = [
    { id: 1, type: "coche", model: "Mercedes-AMG GT", price: "185.000 €", image: "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=1000&auto=format&fit=crop", desc: "Un pura sangre alemán...", specs: [{ label: "0-100 km/h", value: "3.2 s" }, { label: "Potencia", value: "585 CV" }, { label: "Motor", value: "V8 Biturbo" }, { label: "Velocidad Máx", value: "318 km/h" }] },
    { id: 2, type: "coche", model: "Porsche 911 Turbo S", price: "240.000 €", image: "https://images.unsplash.com/photo-1503376763036-066120622c74?q=80&w=1000&auto=format&fit=crop", desc: "El icono atemporal...", specs: [{ label: "0-100 km/h", value: "2.7 s" }, { label: "Potencia", value: "650 CV" }, { label: "Tracción", value: "AWD" }, { label: "Velocidad Máx", value: "330 km/h" }] },
    { id: 3, type: "coche", model: "Chevrolet Corvette C8", price: "110.000 €", image: "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=1000&auto=format&fit=crop", desc: "La redefinición del deportivo...", specs: [{ label: "0-100 km/h", value: "2.9 s" }, { label: "Potencia", value: "495 CV" }, { label: "Motor", value: "V8 LT2" }, { label: "Velocidad Máx", value: "312 km/h" }] },
    { id: 4, type: "moto", model: "Ducati Panigale V4", price: "32.000 €", image: "https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?q=80&w=1000&auto=format&fit=crop", desc: "Esencia de competición...", specs: [{ label: "Cilindrada", value: "1.103 cc" }, { label: "Potencia", value: "214 CV" }, { label: "Peso", value: "175 kg" }, { label: "Electrónica", value: "Evo 2" }] },
    { id: 5, type: "moto", model: "BMW R 1250 GS", price: "21.500 €", image: "https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?q=80&w=1000&auto=format&fit=crop", desc: "La reina de las aventuras...", specs: [{ label: "Motor", value: "Bóxer" }, { label: "Potencia", value: "136 CV" }, { label: "Par Motor", value: "143 Nm" }, { label: "Transmisión", value: "Cardán" }] }
];

let carModal;
let currentVehicle = null; // Guardamos el vehículo seleccionado para el PDF

/* =========================================
   2. LÓGICA DE DESCARGA PDF
   ========================================= */
const descargarPDF = () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const accentColor = [255, 77, 0]; // Naranja Motorsport

    // 1. DETERMINAR QUÉ ESTAMOS DESCARGANDO
    // Miramos si el modal de calendario está abierto
    const calendarModalEl = document.getElementById('calendarModal');
    const isCalendarVisible = calendarModalEl && calendarModalEl.classList.contains('show');

    if (isCalendarVisible) {
        // --- LÓGICA PARA CALENDARIO ---
        doc.setFontSize(22);
        doc.setTextColor(accentColor[0], accentColor[1], accentColor[2]);
        doc.text("Elite Drive | Calendario 2024", 105, 20, { align: "center" });

        // Extraer datos de la tabla de HTML directamente
        doc.autoTable({
            html: '#calendarModal table', // Selecciona la tabla dentro del modal
            startY: 35,
            theme: 'grid',
            headStyles: { fillColor: accentColor },
            styles: { fontSize: 10 }
        });

        doc.save("Calendario_EliteDrive_2024.pdf");

    } else if (currentVehicle) {
        // --- LÓGICA PARA COCHE (Tu código anterior) ---
        doc.setFontSize(22);
        doc.setTextColor(accentColor[0], accentColor[1], accentColor[2]);
        doc.text("Elite Drive | Motorsport", 105, 20, { align: "center" });

        doc.setFontSize(18);
        doc.setTextColor(0, 0, 0);
        doc.text(currentVehicle.model, 20, 40);

        doc.setFontSize(11);
        doc.setTextColor(100, 100, 100);
        const splitDesc = doc.splitTextToSize(currentVehicle.desc, 170);
        doc.text(splitDesc, 20, 55);

        const tableRows = currentVehicle.specs.map(s => [s.label, s.value]);
        doc.autoTable({
            startY: 80,
            head: [['Especificación', 'Valor']],
            body: tableRows,
            theme: 'grid',
            headStyles: { fillColor: accentColor }
        });

        doc.save(`Ficha_${currentVehicle.model.replace(/\s+/g, '_')}.pdf`);
    }
};

/* =========================================
   3. LÓGICA VERTICAL SYNC
   ========================================= */
function initVerticalSync() {
    const menuItems = document.querySelectorAll('.menu-item');
    const dynamicBg = document.getElementById('dynamic-bg');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-desc');

    let currentIndex = 0;
    let autoInterval;

    const update = (index) => {
        const activeItem = menuItems[index];
        if (!activeItem || !dynamicBg) return;

        menuItems.forEach(item => item.classList.remove('active'));
        activeItem.classList.add('active');

        const container = activeItem.parentElement;
        const itemOffsetTop = activeItem.offsetTop;
        const containerHalfHeight = container.offsetHeight / 2;
        const itemHalfHeight = activeItem.offsetHeight / 2;

        container.scrollTo({
            top: itemOffsetTop - containerHalfHeight + itemHalfHeight,
            behavior: 'smooth'
        });

        dynamicBg.style.opacity = '0';
        if (previewTitle) previewTitle.style.opacity = '0';
        if (previewDesc) previewDesc.style.opacity = '0';

        setTimeout(() => {
            const bg = activeItem.getAttribute('data-bg');
            dynamicBg.style.backgroundImage = `url('${bg}')`;
            if (previewTitle) previewTitle.innerText = activeItem.getAttribute('data-title');
            if (previewDesc) previewDesc.innerText = activeItem.getAttribute('data-desc');
            dynamicBg.style.opacity = '0.6';
            if (previewTitle) previewTitle.style.opacity = '1';
            if (previewDesc) previewDesc.style.opacity = '1';
        }, 400);
    };

    const startAutoPlay = () => {
        autoInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % menuItems.length;
            update(currentIndex);
        }, 5000);
    };

    menuItems.forEach((item, i) => {
        item.addEventListener('mouseenter', () => {
            clearInterval(autoInterval);
            currentIndex = i;
            update(i);
        });
        item.addEventListener('mouseleave', startAutoPlay);
    });

    update(0);
    startAutoPlay();
}

/* =========================================
   4. RENDERIZADO Y MODAL
   ========================================= */
function renderVehicles(data) {
    const gridContainer = document.getElementById('inventory-grid');
    if (!gridContainer) return;

    gridContainer.innerHTML = '';
    data.forEach(item => {
        const cardCol = document.createElement('div');
        cardCol.className = 'col-md-6 col-lg-4 mb-4';
        cardCol.innerHTML = `
            <div class="card car-card h-100" onclick="window.abrirModal(${item.id})">
                <div class="card-img-wrapper">
                    <img src="${item.image}" class="card-img-top" alt="${item.model}">
                    <div class="card-overlay"><span class="view-btn">Ver Especificaciones</span></div>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-white">${item.model}</h5>
                    <p class="card-price">${item.price}</p>
                </div>
            </div>`;
        gridContainer.appendChild(cardCol);
    });
}

window.abrirModal = (id) => {
    const v = carData.find(v => v.id === id);
    if (!v || !carModal) return;

    currentVehicle = v; // IMPORTANTE: Guardamos el vehículo actual para el PDF

    document.getElementById('modalTitle').innerText = v.model;
    document.getElementById('modalImage').src = v.image;
    document.getElementById('modalDesc').innerText = v.desc;

    const catBadge = document.getElementById('modalCategory');
    if (catBadge) catBadge.innerText = v.type === 'coche' ? 'Coche' : 'Moto';

    const specsContainer = document.getElementById('modalSpecs');
    specsContainer.innerHTML = v.specs.map(spec => `
        <div class="col-6 col-md-3">
            <div class="spec-item text-center rounded bg-dark p-2 mb-2">
                <div class="spec-label text-muted small">${spec.label}</div>
                <div class="spec-value text-white fw-bold">${spec.value}</div>
            </div>
        </div>
    `).join('');

    carModal.show();
};

/* =========================================
   5. CICLO DE VIDA ONMOUNTED
   ========================================= */
onMounted(() => {
    // 1. Inicializar Modal de Vehículos (Coches/Motos)
    const modalElement = document.getElementById('carModal');
    if (modalElement) {
        carModal = new bootstrap.Modal(modalElement);
    }

    // 2. Inicializar el Slider Principal (Fast & Furious / Ford v Ferrari)
    if (document.getElementById('ms-main-slider-container')) {
        new Swiper('#ms-main-slider-container', {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

    // 3. Inicializar Slider de Videojuegos (Legendary Video Games)
    if (document.querySelector('.game-carousel-container')) {
        new Swiper('.game-carousel-container', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            speed: 800,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
                1400: { slidesPerView: 4 }
            }
        });
    }

    // 4. Render Inicial de Inventario de Vehículos
    renderVehicles(carData);

    // 5. Configurar Buscador de Vehículos
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const text = e.target.value.toLowerCase();
            const filtered = carData.filter(item => item.model.toLowerCase().includes(text));
            renderVehicles(filtered);
        });
    }

    // 6. Listener Global para botones de Descarga PDF
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#btnDescargarPDF');
        if (btn) {
            descargarPDF();
        }
    });

    // 7. Listener para el envío del formulario de reserva
    const formReserva = document.getElementById('formReservaRuta');
    if (formReserva) {
        formReserva.addEventListener('submit', descargarTicketReserva);
    }

    // 8. Inicializar Sistemas Visuales Adicionales
    initVerticalSync();
    if (typeof initSwiperSliders === 'function') initSwiperSliders();
    if (typeof initParallax === 'function') initParallax();

    // 9. Gestión de Videos de Trailers (Cierre Limpio)
    const trailerModal = document.getElementById('trailerModal');
    if (trailerModal) {
        trailerModal.addEventListener('hidden.bs.modal', () => {
            const video = document.getElementById('ms-video-player');
            if (video) {
                video.pause();
                video.src = "";
            }
        });
    }

    // 10. NUEVO: Slider "Nuestros Juegos Favoritos" (mega-swiper)
    // Se han añadido las flechas de navegación específicas
    const megaSwiperGames = document.querySelector('.mega-swiper-slider-wrapper');
    if (megaSwiperGames) {
        new Swiper('.mega-swiper-slider-wrapper', {
            slidesPerView: 'auto',
            spaceBetween: 25,
            loop: true,
            centeredSlides: false,
            speed: 800,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
            },
            // FUNCIÓN DE LAS FLECHAS AÑADIDA AQUÍ:
            navigation: {
                nextEl: '.mega-swiper-next-btn',
                prevEl: '.mega-swiper-prev-btn',
            },
            observer: true,
            observeParents: true,
            breakpoints: {
                768: { spaceBetween: 30 },
                1200: { spaceBetween: 40 }
            }
        });
    }

    console.log("Elite Drive: Sistemas cargados (Incluyendo carrusel de Videojuegos y Favoritos con flechas).");
});
/* =========================================
   LÓGICA DE RUTAS Y RESERVAS
   ========================================= */

// 1. Función para saltar del modal de rutas al de formulario
window.abrirFormularioReserva = () => {
    // Cerramos el modal de rutas (usando el ID que me pasaste antes)
    const rutasModalEl = document.getElementById('rutasModal');
    if (rutasModalEl) {
        const modalRutas = bootstrap.Modal.getInstance(rutasModalEl);
        if (modalRutas) modalRutas.hide();
    }

    // Abrimos el modal del formulario (usando el ID de tu último mensaje)
    setTimeout(() => {
        const formModalEl = document.getElementById('reservaFormModal');
        if (formModalEl) {
            const modalForm = new bootstrap.Modal(formModalEl);
            modalForm.show();
        }
    }, 400); // Pequeño delay para que Bootstrap no se bloquee con las sombras
};

// 2. Función para generar el PDF de la reserva
const descargarTicketReserva = (e) => {
    e.preventDefault(); // Evita que la página se recargue

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Capturamos los datos de TU formulario
    const ruta = document.getElementById('rutaSeleccionada').value;
    const nombre = document.getElementById('nombrePiloto').value;
    const email = document.getElementById('emailPiloto').value;

    // --- Diseño del PDF ---
    const accentColor = [255, 77, 0]; // Naranja #ff4d00

    // Cabecera estética
    doc.setFillColor(20, 20, 20);
    doc.rect(0, 0, 210, 40, 'F');
    doc.setTextColor(accentColor[0], accentColor[1], accentColor[2]);
    doc.setFontSize(22);
    doc.text("ELITE DRIVE | MOTORSPORT", 105, 25, { align: "center" });

    // Cuerpo
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(16);
    doc.text("CONFIRMACIÓN DE RESERVA", 20, 60);

    doc.setFontSize(12);
    doc.text(`Piloto: ${nombre}`, 20, 80);
    doc.text(`Ruta: ${ruta}`, 20, 90);
    doc.text(`Email: ${email}`, 20, 100);
    doc.text(`Fecha de solicitud: ${new Date().toLocaleDateString()}`, 20, 110);

    // Tabla o cuadro de aviso
    doc.setDrawColor(accentColor[0], accentColor[1], accentColor[2]);
    doc.rect(15, 130, 180, 30);
    doc.text("Presenta este ticket digital el día de la ruta.", 105, 147, { align: "center" });

    // Descarga
    doc.save(`Ticket_Reserva_${nombre.replace(/\s+/g, '_')}.pdf`);

    // Cerrar el modal al terminar
    const formModalEl = document.getElementById('reservaFormModal');
    const modalForm = bootstrap.Modal.getInstance(formModalEl);
    if (modalForm) modalForm.hide();

    toast.info("¡Reserva confirmada! Tu ticket se ha generado.", "Reservation Confirmed");
};

/* =========================================
   EN EL ONMOUNTED
   ========================================= */




/* =========================================
   LÓGICA DEL SLIDER PRINCIPAL (SWIPER)
   ========================================= */
function initMainSlider() {
    // Inicializamos Swiper
    const swiper = new Swiper('#ms-main-slider-container', {
        loop: true,
        speed: 1000,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        effect: 'fade', // Efecto suave de desvanecimiento
        fadeEffect: {
            crossFade: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
}

// Función para abrir el trailer (Asegúrate de tener un modal con ID trailerModal)
window.openLocalTrailer = (videoPath) => {
    const modalElement = document.getElementById('trailerModal');
    const videoPlayer = document.getElementById('ms-video-player');

    if (modalElement && videoPlayer) {
        videoPlayer.src = videoPath;
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        videoPlayer.play();
    }
};




/* =========================================
   LÓGICA DEL SLIDER DE VIDEOJUEGOS
   ========================================= */
function initGameSlider() {
    if (document.getElementById('game-swiper')) {
        new Swiper('#game-swiper', {
            slidesPerView: 1,      // Por defecto 1 en móvil
            spaceBetween: 20,      // Espacio entre cartas
            loop: true,            // Infinito
            centeredSlides: true,  // La del centro se ve principal
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            // Responsive: cuántos juegos se ven según el tamaño de pantalla
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
                1400: { slidesPerView: 4 }
            }
        });
    }
}






</script>
<template>

    <section id="ms-home-wrapper" class="p-0 overflow-hidden">
        <div id="ms-main-slider-container" class="swiper m-0 p-0">

            <div class="swiper-wrapper">

                <div class="swiper-slide ms-slide ms-bg-1">
                    <div class="ms-vignette"></div>
                    <div class="container-fluid ms-content-container">
                        <div class="row align-items-center h-100">
                            <div class="col-xl-5 col-lg-8 col-md-10 ps-md-5">
                                <h1 class="ms-big-title">Fast & Furious 9</h1>
                                <div class="ms-meta-bar">
                                    <div class="ms-stars"><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star-half"></i></div>
                                    <span class="ms-rating-text">5.2 (IMDb)</span>
                                    <span class="ms-badge">PG-13</span>
                                    <span class="ms-duration">2h 23min</span>
                                </div>
                                <p class="ms-description">Dom Toretto is living a quiet life, but he knows that danger
                                    always lurks. This time, the threat will force Dom to face the sins of his past.</p>
                                <div class="ms-info-tags">
                                    <div class="ms-tag"><span>Genre:</span> Action, Motor</div>
                                    <div class="ms-tag"><span>Tags:</span> Muscle Cars, Speed</div>
                                </div>
                                <div class="ms-button-group">
                                    <a href="javascript:void(0)" onclick="openLocalTrailer('/video/fastandfourius.mp4')"
                                        class="ms-btn-action ms-btn-orange">
                                        <i class="fa fa-play me-2"></i>Watch Trailer
                                    </a>
                                    <a href="#" class="ms-btn-link">See more details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide ms-slide ms-bg-2">
                    <div class="ms-vignette"></div>
                    <div class="container-fluid ms-content-container">
                        <div class="row align-items-center h-100">
                            <div class="col-xl-5 col-lg-8 col-md-10 ps-md-5">
                                <h1 class="ms-big-title">Ford v Ferrari</h1>
                                <div class="ms-meta-bar">
                                    <div class="ms-stars text-danger"><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star"></i></div>
                                    <span class="ms-rating-text">8.1 (IMDb)</span>
                                    <span class="ms-badge">PG-13</span>
                                    <span class="ms-duration">2h 32min</span>
                                </div>
                                <p class="ms-description">Carroll Shelby and Ken Miles battle corporate interference to
                                    build a revolutionary race car for Ford.</p>
                                <div class="ms-info-tags">
                                    <div class="ms-tag"><span>Genre:</span> Drama, Biographic</div>
                                    <div class="ms-tag"><span>Tags:</span> Classics, Engineering</div>
                                </div>
                                <div class="ms-button-group">
                                    <a href="javascript:void(0)"
                                        onclick="openLocalTrailer('/video/trailerfordvsferrari.mp4')"
                                        class="ms-btn-action ms-btn-orange">
                                        <i class="fa fa-play me-2"></i>Watch Trailer
                                    </a>
                                    <a href="#" class="ms-btn-link">See More Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="slick-prev swiper-button-prev"></div>
            <div class="slick-next swiper-button-next"></div>
        </div>
    </section>

    <div class="modal fade" id="trailerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-black border-0" style="overflow: visible;">
                <button type="button" class="ms-close-trailer-x" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9 bg-black">
                        <video id="ms-video-player" controls playsinline>
                            <source src="" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <section class="brands-section py-5 bg-black overflow-hidden">
        <div class="container text-center mb-5">
            <h3 class="text-uppercase fw-bold text-white">Our <span class="text-accent">Brands</span></h3>
            <div class="divider mx-auto"></div>
        </div>

        <div class="slider">
            <div class="slide-track">
                <div class="slide"><img src="../../img/gallery/porchelogo.png" alt="Porsche"></div>
                <div class="slide"><img src="../../img/gallery/lamborghinilogo.png" alt="Lamborghini"></div>
                <div class="slide"><img src="../../img/gallery/bmwlogo.png" alt="BMW"></div>
                <div class="slide"><img src="../../img/gallery/audilogo.png" alt="Audi"></div>
                <div class="slide"><img src="../../img/gallery/mercedeslogo.png" alt="Mercedes"></div>
                <div class="slide"><img src="../../img/gallery/ferrarilogo.png" alt="Ferrari"></div>
                <div class="slide"><img src="../../img/gallery/Ducatilogo.png" alt="Ducati"></div>
                <div class="slide"><img src="../../img/gallery/porchelogo.png" alt="Porsche"></div>
                <div class="slide"><img src="../../img/gallery/lamborghinilogo.png" alt="Lamborghini"></div>
                <div class="slide"><img src="../../img/gallery/bmwlogo.png" alt="BMW"></div>
                <div class="slide"><img src="../../img/gallery/audilogo.png" alt="Audi"></div>
                <div class="slide"><img src="../../img/gallery/mercedeslogo.png" alt="Mercedes"></div>
                <div class="slide"><img src="../../img/gallery/ferrarilogo.png" alt="Ferrari"></div>
                <div class="slide"><img src="../../img/gallery/Ducatilogo.png" alt="Ducati"></div>
            </div>
        </div>
    </section>

    <section class="vertical-sync-section position-relative">
        <div id="dynamic-bg" class="dynamic-bg-layer"></div>
        <div class="overlay-dark"></div>

        <div class="container py-5 position-relative z-2">
            <h3 class="section-title mb-5 text-white">TRENDING <span class="text-primary-color">STORIES</span></h3>

            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="vertical-menu" id="verticalMenu">

                        <div class="menu-item active"
                            data-bg="https://images.unsplash.com/photo-1542282088-fe8426682b8f?q=80&w=1887&auto=format&fit=crop"
                            data-title="Born to Race"
                            data-desc="An immersive look into the world of GT racing championships.">
                            <div class="d-flex align-items-center">
                                <img src="https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=100&h=140"
                                    alt="Poster" class="poster-thumb">
                                <div class="ms-3 text-white">
                                    <h5>Born to Race</h5>
                                    <span class="badge bg-secondary">Docu-Series</span>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item"
                            data-bg="https://images.unsplash.com/photo-1471479917193-f00955256257?q=80&w=2048&auto=format&fit=crop"
                            data-title="Night Rider"
                            data-desc="When the sun goes down, the illegal street races begin.">
                            <div class="d-flex align-items-center">
                                <img src="https://images.unsplash.com/photo-1471479917193-f00955256257?auto=format&fit=crop&w=100&h=140"
                                    alt="Poster" class="poster-thumb">
                                <div class="ms-3 text-white">
                                    <h5>Night Rider</h5>
                                    <span class="badge bg-secondary">Action</span>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item"
                            data-bg="https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?q=80&w=2070&auto=format&fit=crop"
                            data-title="The Apex" data-desc="A drama about a rookie pilot trying to secure his legacy.">
                            <div class="d-flex align-items-center">
                                <img src="https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=100&h=140"
                                    alt="Poster" class="poster-thumb">
                                <div class="ms-3 text-white">
                                    <h5>The Apex</h5>
                                    <span class="badge bg-secondary">Drama</span>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item"
                            data-bg="https://images.unsplash.com/photo-1583121274602-3e2820c69888?q=80&w=2070&auto=format&fit=crop"
                            data-title="Timeless Icon"
                            data-desc="Experience the heritage of the most successful sports car.">
                            <div class="d-flex align-items-center">
                                <img src="https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=100&h=140"
                                    alt="Poster" class="poster-thumb">
                                <div class="ms-3 text-white">
                                    <h5>Timeless Icon</h5>
                                    <span class="badge bg-secondary">Heritage</span>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item"
                            data-bg="https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=2048&auto=format&fit=crop"
                            data-title="German Power" data-desc="Precision engineering meets raw V8 performance.">
                            <div class="d-flex align-items-center">
                                <img src="https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=100&h=140"
                                    alt="Poster" class="poster-thumb">
                                <div class="ms-3 text-white">
                                    <h5>German Power</h5>
                                    <span class="badge bg-secondary">Performance</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-8 col-md-7 d-flex align-items-end pb-5">
                    <div class="active-info text-end w-100">
                        <h2 id="preview-title" class="display-4 fw-bold text-white">Born to Race</h2>
                        <p id="preview-desc" class="lead text-light">An immersive look into the world of GT racing
                            championships.</p>
                        <button class="btn btn-primary-custom btn-lg mt-3">Watch Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>





    <section class="mega-swiper-games-section">
        <div class="mega-swiper-container">
            <div class="section-title-wrapper">
                <h2 class="mega-swiper-section-title">Nuestros Juegos Favoritos</h2>
                <p class="mega-swiper-section-subtitle">Descubre los títulos más icónicos y aclamados de la historia.
                </p>
            </div>

            <div class="mega-swiper-slider-wrapper swiper-container">
                <div class="swiper-wrapper">

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/dir1xbox.png" alt="Dirt 1" class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">Dirt </h3>
                            <p class="mega-swiper-game-genre">Aventura, Acción</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/ps2granturismo.png" alt="Gran Turismo 4"
                            class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">Gran Turismo </h3>
                            <p class="mega-swiper-game-genre">Acción, Carrera</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/dirt5xbox.jpg" alt="Dirt 5" class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">Dirt </h3>
                            <p class="mega-swiper-game-genre">Acción, Aventura</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/motogp14.png" alt="MotoGP14" class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">MotoGP </h3>
                            <p class="mega-swiper-game-genre">Aventura, Western</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/thecrew2.jpg" alt="The Crew Motorfest"
                            class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">The Crew</h3>
                            <p class="mega-swiper-game-genre">Aventura, Acción</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/motogp08.png" alt="Motogp" class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">MotoGP</h3>
                            <p class="mega-swiper-game-genre">Aventura, Acción</p>
                        </div>
                    </div>

                    <div class="swiper-slide mega-swiper-game-card">
                        <img src="../../img/gallery/motorfest.png" alt="The Crew Motorfest"
                            class="mega-swiper-game-image">
                        <div class="mega-swiper-game-overlay">
                            <h3 class="mega-swiper-game-title">The Crew Motorfest</h3>
                            <p class="mega-swiper-game-genre">Aventura, Acción</p>
                        </div>
                    </div>

                </div>
                <div class="swiper-button-next mega-swiper-next-btn"></div>
                <div class="swiper-button-prev mega-swiper-prev-btn"></div>
            </div>
        </div>
    </section>






    <section class="py-5 section-dark overflow-hidden">
        <div class="container text-center mb-5">
            <h2 class="ms-section-title">Legendary <span class="text-accent">Video Games</span></h2>
            <div class="ms-title-divider mx-auto"></div>
        </div>

        <div class="game-carousel-container swiper" id="game-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide game-card">
                    <div class="card-wrapper">
                        <div class="card-bg" style="background-image: url('/images/cars/gallery/needforspeed.jpg');">
                        </div>
                        <div class="card-title">Need For Speed</div>
                    </div>
                </div>

                <div class="swiper-slide game-card">
                    <div class="card-wrapper">
                        <div class="card-bg" style="background-image: url('/images/cars/gallery/ride5.jpg');"></div>
                        <div class="card-title">Ride 5</div>
                    </div>
                </div>

                <div class="swiper-slide game-card">
                    <div class="card-wrapper">
                        <div class="card-bg" style="background-image: url('/images/cars/gallery/forzahorizon.jpg');">
                        </div>
                        <div class="card-title">Forza Horizon</div>
                    </div>
                </div>

                <div class="swiper-slide game-card">
                    <div class="card-wrapper">
                        <div class="card-bg" style="background-image: url('/images/cars/gallery/granturismo.jpg');">
                        </div>
                        <div class="card-title">Gran Turismo</div>
                    </div>
                </div>

                <div class="swiper-slide game-card">
                    <div class="card-wrapper">
                        <div class="card-bg" style="background-image: url('/images/cars/gallery/f12024.jpg');"></div>
                        <div class="card-title">F1 2024</div>
                    </div>
                </div>

            </div>
            <div class="swiper-button-next" style="color: var(--accent-color)"></div>
            <div class="swiper-button-prev" style="color: var(--accent-color)"></div>
        </div>
    </section>





    <section class="parallax-trigger section-cars">
        <div class="parallax-content">
            <div class="container text-center">
                <h2 class="display-4 fw-bold text-uppercase">Engineering <span class="text-accent">Without Limits</span>
                </h2>
                <p class="lead">Where performance meets automotive art.</p>
            </div>
        </div>
    </section>

    <section class="motorsport-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 content-col">
                    <h4 class="text-accent text-uppercase ls-2">Competition</h4>
                    <h2 class="display-5 fw-bold text-white mb-4">Racing <br>Spirit</h2>
                    <p class="text-gray">
                        Step into the world of high-level competition. We organize exclusive circuit events, track days
                        and driving experiences.
                    </p>
                    <ul class="list-unstyled text-white mt-4 custom-list">
                        <li><span class="bullet"></span> Monthly Track Days</li>
                        <li><span class="bullet"></span> Regional GT Championship</li>
                        <li><span class="bullet"></span> Driver Academy</li>
                    </ul>
                    <a href="#" class="btn btn-outline-custom mt-3" data-bs-toggle="modal"
                        data-bs-target="#calendarModal">
                        View Calendar
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="motorsport-img-container">
                        <img src="https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=1000&auto=format&fit=crop"
                            alt="Racing" class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>




    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-uppercase fw-bold" id="calendarModalLabel">
                        Calendario <span class="text-accent">Temporada 2024</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead class="text-accent">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Evento</th>
                                    <th>Circuito</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>15 Mar</td>
                                    <td class="fw-bold">Track Day Opening</td>
                                    <td>Montmeló, ESP</td>
                                    <td><span class="badge bg-success">Abierto</span></td>
                                </tr>
                                <tr>
                                    <td>02 Abr</td>
                                    <td class="fw-bold">GT Regional - Round 1</td>
                                    <td>Jarama, ESP</td>
                                    <td><span class="badge bg-warning text-dark">Últimas plazas</span></td>
                                </tr>
                                <tr>
                                    <td>20 May</td>
                                    <td class="fw-bold">Academia de Pilotos</td>
                                    <td>Ascari, ESP</td>
                                    <td><span class="badge bg-success">Abierto</span></td>
                                </tr>
                                <tr>
                                    <td>12 Jun</td>
                                    <td class="fw-bold">Summer Night Race</td>
                                    <td>Cheste, ESP</td>
                                    <td><span class="badge bg-secondary">Próximamente</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" id="btnDescargarPDF" class="btn btn-custom">Descargar PDF</button>
                </div>
            </div>
        </div>
    </div>












    <section class="parallax-trigger section-motos">
        <div class="parallax-content">
            <div class="container text-center">
                <h2 class="display-4 fw-bold text-uppercase">Freedom on <span class="text-accent">Two Wheels</span></h2>
                <p class="lead">Feel the road, master the curve, live the adrenaline.</p>
            </div>
        </div>
    </section>





    <section class="motorsport-section py-5 rutas-moto-section">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="motorsport-img-container">
                        <img src="../../img/gallery/rutamoto.jpeg" alt="Motorcycle road route"
                            class="img-fluid rounded shadow-lg">
                    </div>
                </div>

                <div class="col-lg-6 content-col">
                    <h4 class="text-accent text-uppercase ls-2">Routes</h4>
                    <h2 class="display-5 fw-bold text-white mb-4">Biker <br>Spirit</h2>

                    <p class="text-gray">
                        If you love motorcycles, discover carefully selected routes to enjoy the road: panoramic
                        landscapes, perfect curves and weekend getaways designed for the biker community.
                    </p>

                    <ul class="list-unstyled text-white mt-4 custom-list">
                        <li><span class="bullet"></span> One-day routes: curves and viewpoints</li>
                        <li><span class="bullet"></span> Weekend getaways</li>
                        <li><span class="bullet"></span> Guided group routes</li>
                    </ul>

                    <a href="#" class="btn btn-outline-custom mt-3" data-bs-toggle="modal" data-bs-target="#rutasModal">
                        View Routes</a>
                </div>
            </div>
        </div>
    </section>




    <div class="modal fade" id="rutasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-uppercase fw-bold">
                        Recommended <span class="text-accent">Routes</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="card bg-black border-secondary h-100">
                                <img src="../../img/gallery/rutadiablo.jpg" class="card-img-top" alt="Mountain Route">
                                <div class="card-body">
                                    <h5 class="card-title text-accent fw-bold">Devil’s Pass</h5>
                                    <p class="small text-gray">
                                        High-mountain route with tight curves and views of the Pyrenees.
                                    </p>
                                    <div class="d-flex justify-content-between border-top border-secondary pt-2 mt-2">
                                        <span class="small">
                                            <i class="fa fa-tachometer-alt text-accent"></i> 150 km
                                        </span>
                                        <span class="small">
                                            <i class="fa fa-clock text-accent"></i> 3h 20m
                                        </span>
                                    </div>
                                    <span class="badge bg-danger mt-3 w-100">
                                        Difficulty: High
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-black border-secondary h-100">
                                <img src="../../img/gallery/rutacostadelsol.jpg" class="card-img-top"
                                    alt="Coastal Route">
                                <div class="card-body">
                                    <h5 class="card-title text-accent fw-bold">Costa del Sol</h5>
                                    <p class="small text-gray">
                                        Coastal route ideal for enjoying the sunset and sea breeze.
                                    </p>
                                    <div class="d-flex justify-content-between border-top border-secondary pt-2 mt-2">
                                        <span class="small">
                                            <i class="fa fa-tachometer-alt text-accent"></i> 85 km
                                        </span>
                                        <span class="small">
                                            <i class="fa fa-clock text-accent"></i> 1h 45m
                                        </span>
                                    </div>
                                    <span class="badge bg-success mt-3 w-100">
                                        Difficulty: Easy
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-black border-secondary h-100">
                                <img src="../../img/gallery/rutadelsilencio.jpg" class="card-img-top"
                                    alt="Valley Route">
                                <div class="card-body">
                                    <h5 class="card-title text-accent fw-bold">Valley of Silence</h5>
                                    <p class="small text-gray">
                                        Mixed route through pine forests and perfectly smooth secondary roads.
                                    </p>
                                    <div class="d-flex justify-content-between border-top border-secondary pt-2 mt-2">
                                        <span class="small">
                                            <i class="fa fa-tachometer-alt text-accent"></i> 210 km
                                        </span>
                                        <span class="small">
                                            <i class="fa fa-clock text-accent"></i> 4h 10m
                                        </span>
                                    </div>
                                    <span class="badge bg-warning text-dark mt-3 w-100">
                                        Difficulty: Medium
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-custom" onclick="window.abrirFormularioReserva()">
                        Book Guided Route
                    </button>
                </div>
            </div>
        </div>
    </div>








    <div class="modal fade" id="reservaFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-accent" style="border: 1px solid var(--accent-color);">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-uppercase fw-bold">Solicitar <span class="text-accent">Reserva</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formReservaRuta">
                        <div class="mb-3">
                            <label class="form-label small text-muted">SELECCIONA TU RUTA</label>
                            <select id="rutaSeleccionada" class="form-select bg-black text-white border-secondary"
                                required>
                                <option value="" disabled selected>Escoge una ruta...</option>
                                <option value="Devil’s Pass (Paso del Diablo)">Devil’s Pass - Alta Montaña</option>
                                <option value="Costa del Sol">Costa del Sol - Marítima</option>
                                <option value="Valley of Silence (Valle del Silencio)">Valley of Silence - Bosque
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">TU NOMBRE</label>
                            <input type="text" id="nombrePiloto"
                                class="form-control bg-black text-white border-secondary" required
                                placeholder="Ej: Marc Márquez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">CORREO ELECTRÓNICO</label>
                            <input type="email" id="emailPiloto"
                                class="form-control bg-black text-white border-secondary" required
                                placeholder="tu@email.com">
                        </div>
                        <button type="submit" class="btn btn-custom w-100 mt-3">Confirmar y Descargar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>









    <section class="parallax-trigger section-join-community">
        <div class="parallax-overlay"></div>
        <div class="parallax-content">
            <div class="container text-center">
                <h2 class="display-4 fw-bold text-uppercase">Join Our <span class="text-accent">Community</span></h2>
                <p class="lead mb-4">Be part of an exclusive club where passion has no limits.</p>
                <a href="../../js/pages/Contact.vue" class="btn btn-custom px-5">Register Now</a>
            </div>
        </div>
    </section>

    <section class="motorsport-section py-5">
    </section>





    <section class="motorsport-section py-5">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6 content-col mb-4 mb-lg-0">
                    <h4 class="text-accent text-uppercase ls-2">Community</h4>
                    <h2 class="display-5 fw-bold text-white mb-4">
                        Live the Route,<br>Don’t Just Ride It
                    </h2>

                    <p class="text-gray">
                        Riding a motorcycle means sharing experiences. We organize rides, meetups and events for those
                        who feel the road as part of their identity.
                    </p>

                    <ul class="list-unstyled text-white mt-4 custom-list">
                        <li><span class="bullet"></span> Monthly group rides</li>
                        <li><span class="bullet"></span> Exclusive client events</li>
                        <li><span class="bullet"></span> Active biker community</li>
                    </ul>



                </div>

                <div class="col-lg-6">
                    <div class="motorsport-img-container">
                        <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1000&auto=format&fit=crop"
                            alt="Group of bikers riding" class="img-fluid rounded shadow-lg">
                    </div>
                </div>

            </div>
        </div>
    </section>





    <section class="parallax-trigger section-news-banner">
        <div class="parallax-content">
            <div class="container text-center">
                <h2 class="display-4 fw-bold text-uppercase">
                    Motorsport <span class="text-accent">News</span>
                </h2>
                <p class="lead">
                    News, events and updates from the world of motorsports
                </p>
            </div>
        </div>
    </section>

    <section class="py-5 section-dark">
        <div class="container">

            <div class="text-center mb-5">
                <h3 class="text-uppercase fw-bold text-white">
                    Latest <span class="text-accent">News</span>
                </h3>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">

                <!-- News 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="news-card h-100">
                        <img src="../../img/gallery/motoElectrica.jpeg" class="img-fluid" alt="Motorcycle news">
                        <div class="news-content">
                            <span class="news-date">December 25, 2025</span>
                            <h5 class="fw-bold text-white">
                                Electric motorcycles close 2025 with an increase in registrations: 11,050 units in total
                            </h5>
                            <p class="text-gray">
                                Discover one of the most spectacular routes to enjoy riding on two wheels this year.
                            </p>
                            <a href="https://www.hibridosyelectricos.com/motos/moto-electrica-cierra-2025-con-incremento-en-matriculaciones-mayor-motos-combustion_84497_102.html"
                                class="news-link">
                                Read more →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- News 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="news-card h-100">
                        <img src="../../img/gallery/Lexux.jpg" class="img-fluid" alt="Car news">
                        <div class="news-content">
                            <span class="news-date">January 2026</span>
                            <h5 class="fw-bold text-white">
                                Presentation of the new Lexus sports model
                            </h5>
                            <p class="text-gray">
                                Radical design and racing technology arrive at the dealership.
                            </p>
                            <a href="https://prensa.lexusauto.es/lexus-presenta-el-estreno-mundial-del-deportivo-lexus-lfa-concept-bev/"
                                class="news-link">
                                Read more →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- News 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="news-card h-100">
                        <img src="../../img/gallery/toyota.jpg" class="img-fluid" alt="Car news">
                        <div class="news-content">
                            <span class="news-date">October 2025</span>
                            <h5 class="fw-bold text-white">
                                Toyota achieved record sales in 2025 and remains the global automotive leader for the
                                sixth year
                            </h5>
                            <p class="text-gray">
                                The Japanese group, which also includes Daihatsu Motor and Hino Motors, sold 4.6% more
                                vehicles, reaching a total of 11,322,575 units.
                            </p>
                            <a href="https://cincodias.elpais.com/companias/2026-01-29/toyota-logro-ventas-record-en-2025-y-se-mantiene-como-lider-global-del-motor-por-sexto-ano.html"
                                class="news-link">
                                Read more →
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <div class="modal fade" id="carModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark-custom text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-uppercase fw-bold" id="modalTitle">Vehicle Name</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="modal-img-container">
                        <img id="modalImage" src="" alt="Vehicle details" class="w-100">
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-accent m-0">Specifications</h4>
                            <span class="badge" id="modalCategory"
                                style="background-color: var(--accent-color); color: black;">
                                Category
                            </span>
                        </div>
                        <div class="row g-3" id="modalSpecs"></div>
                        <p class="mt-4 text-gray" id="modalDesc">Description...</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-custom w-100">Request Test Drive</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="trailerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-black border-0" style="overflow: visible;">
                <div class="modal-header border-0 p-0">
                    <button type="button" id="btn-close-trailer" class="ms-close-x" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="trailerVideo" src="" title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>


<style scoped>
/* 'scoped' hace que los estilos solo afecten a este componente */
@import "../../css/galeria.css";
</style>
