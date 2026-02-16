<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import emailjs from '@emailjs/browser'

// --- CONFIGURACIÓN ---
const serviceID = 'service_4o31555'   //
const templateID = 'template_3q3yqht'  //
const publicKey = 'qTiJLWQWodscWpuF-'  //

// --- UI / estado ---
const isSubmitting = ref(false)
const isChatOpen = ref(false)
const toggleWhatsApp = () => { isChatOpen.value = !isChatOpen.value }
const isOpen = ref(false)
let intervalId = null

// --- formulario ---
const form = reactive({
  nombre: '',
  email: '',
  telefono: '',
  interes: 'info',
  marca_tasacion: '',
  modelo_tasacion: '',
  ano_tasacion: '',
  asunto: '',
  mensaje: ''
})

const errors = reactive({
  nombre: false,
  email: false
})



// --- NUEVAS FUNCIONES DE VALIDACIÓN EN TIEMPO REAL ---

// Solo permite letras y espacios (incluye tildes y ñ)
const filterTextOnly = (event) => {
    const value = event.target.value;
    // La expresión regular reemplaza todo lo que NO sea letras o espacios
    form.nombre = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
}

// Solo permite números
const filterNumberOnly = (field, event) => {
    const value = event.target.value;
    // Reemplaza todo lo que NO sea un dígito (0-9)
    form[field] = value.replace(/\D/g, '');
}



// --- negocio horario ---
const updateBusinessStatus = () => {
  const espanaTime = new Date().toLocaleString("en-US", { timeZone: "Europe/Madrid" })
  const now = new Date(espanaTime)
  const day = now.getDay()
  const hour = now.getHours()
  const minutes = now.getMinutes()
  const currentTime = hour + (minutes / 60)

  let status = false
  if (day >= 1 && day <= 5) { // Lunes a Viernes: 9 a 20
    if (currentTime >= 9 && currentTime < 20) status = true
  } 
  else if (day === 6) { // Sábado: 10 a 14
    if (currentTime >= 10 && currentTime < 14) status = true
  }
  isOpen.value = status
}

onMounted(() => {
  emailjs.init(publicKey)
  updateBusinessStatus()
  intervalId = setInterval(updateBusinessStatus, 60000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

// --- FUNCION DE ENVIO ---
const handleSubmit = async () => {
  // 1. Validación
  errors.nombre = false
  errors.email = false
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  
  if (!form.nombre.trim()) errors.nombre = true
  if (!emailRegex.test(form.email)) errors.email = true
  
  if (errors.nombre || errors.email) {
    alert("Por favor, rellena los campos obligatorios correctamente.")
    return
  }

  isSubmitting.value = true

  // 2. Mapeo de parámetros corregido para tu plantilla
  const templateParams = {
    // Datos personales
    from_name: form.nombre,    // {{from_name}}
    reply_to: form.email,      // {{reply_to}}
    telefono: form.telefono || 'No proporcionado', // {{telefono}}
    interes: form.interes,     // {{interes}}
    fecha_envio: new Date().toLocaleString('es-ES'), // {{fecha_envio}}
    asunto: form.asunto,       // {{asunto}}
    message: form.mensaje,     // {{message}} -> IMPORTANTE: Tu plantilla usa "message", no "mensaje"

    // --- DATOS DEL VEHÍCULO CORREGIDOS ---
    // Enviamos los nombres de variables EXACTOS que tienes en el template HTML
    marca_tasacion: form.interes === 'venta' ? form.marca_tasacion : 'N/A',
    modelo_tasacion: form.interes === 'venta' ? form.modelo_tasacion : 'N/A',
    ano_tasacion: form.interes === 'venta' ? form.ano_tasacion : 'N/A',

    // Variables de apoyo para configuración de EmailJS
    nombre: form.nombre,
    email: form.email
  }

  try {
    // 3. Envío
    const result = await emailjs.send(serviceID, templateID, templateParams, publicKey)
    
    console.log('EmailJS success:', result)
    alert(`¡Gracias ${form.nombre}! Tu mensaje ha sido enviado correctamente.`)

    // Limpiar formulario
    Object.assign(form, {
      nombre: '', email: '', telefono: '', interes: 'info',
      marca_tasacion: '', modelo_tasacion: '', ano_tasacion: '',
      asunto: '', mensaje: ''
    })

  } catch (err) {
    console.error('ERROR AL ENVIAR (EmailJS):', err)
    alert('Hubo un error al enviar el mensaje. Revisa tu conexión.')
  } finally {
    isSubmitting.value = false
  }
}
</script>


<template>

 <section class="contact-hero d-flex align-items-center justify-content-center">
        <div class="overlay"></div>
        <div class="container text-center position-relative">
            <h1 class="display-3 fw-bold text-white text-uppercase">Contact Us</h1>
        </div>
    </section>

    <section class="brands-section py-5 border-bottom border-secondary">
        <div class="container text-center">
            <p class="brand-title text-muted text-uppercase fw-bold ls-2 mb-5">We work with the best brands</p>
            
            <div class="row justify-content-center align-items-start g-4">
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">BMW</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/bmwlogo.png" alt="BMW" class="hover-logo">
                    </div>
                </div>
                
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">AUDI</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/audilogo.png" alt="Audi" class="hover-logo">
                    </div>
                </div>
                
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">MERCEDES</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/mercedeslogo.png" alt="Mercedes" class="hover-logo">
                    </div>
                </div>
                
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">PORSCHE</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/porchelogo.png" alt="Porsche" class="hover-logo">
                    </div>
                </div>
                
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">DUCATI</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/ducatilogo.png" alt="Ducati" class="hover-logo">
                    </div>
                </div>
                
                <div class="col-6 col-md-2 brand-column">
                    <h4 class="brand-item fw-bold m-0">FERRARI</h4>
                    <div class="logo-wrapper">
                        <img src="../../img/contact/ferrarilogo.png" alt="Ferrari" class="hover-logo">
                    </div>
                </div>
            </div>
        </div>
    </section>
<section class="container py-5 my-4">
    <div class="row g-5">
        <div class="col-lg-6">
            <h2 class="mb-4 section-title">Send Us a <span class="text-highlight">Message</span></h2>
            
            <form @submit.prevent="handleSubmit">
                <div class="row g-3">
                    <div class="col-md-6">
    <label for="nombre" class="form-label">Full name *</label>
    <input type="text" 
           :value="form.nombre" 
           @input="filterTextOnly" 
           class="form-control"
           :class="{ 'is-invalid': errors.nombre }" 
           placeholder="Your name" 
           required>
</div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" v-model="form.email" class="form-control" :class="{'is-invalid': errors.email}" placeholder="you@email.com" required>
                    </div>
                   <div class="col-md-6">
    <label for="telefono" class="form-label">Phone</label>
    <input type="tel" 
           :value="form.telefono" 
           @input="filterNumberOnly('telefono', $event)"
           class="form-control"
           placeholder="+34 600 000 000"
           maxlength="15"> 
</div>
                    <div class="col-md-6">
                        <label for="interes" class="form-label">Interested in</label>
                        <select v-model="form.interes" class="form-select">
                            <option value="info">General information</option>
                            <option value="compra">Buying a vehicle</option>
                            <option value="venta">Selling a vehicle (Valuation)</option>
                        </select>
                    </div>

                    <div v-if="form.interes === 'venta'" class="col-12">
                        <div class="p-3 rounded border border-warning-subtle bg-dark-subtle mt-2">
                            <h6 class="text-highlight mb-3"><i class="bi bi-car-front-fill me-2"></i>Details for Valuation</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" v-model="form.marca_tasacion" class="form-control" placeholder="Make (e.g., BMW)">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" v-model="form.modelo_tasacion" class="form-control" placeholder="Model (e.g., M4)">
                                </div>
                              <div class="col-md-4">
    <input type="text" 
           :value="form.ano_tasacion" 
           @input="filterNumberOnly('ano_tasacion', $event)"
           class="form-control"
           placeholder="Year"
           maxlength="4">
</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="asunto" class="form-label">Subject *</label>
                        <input type="text" v-model="form.asunto" class="form-control" placeholder="How can we help you?" required>
                    </div>
                    <div class="col-12">
                        <label for="mensaje" class="form-label">Message *</label>
                        <textarea v-model="form.mensaje" class="form-control" rows="4" placeholder="Tell us more about what you're looking for..." required></textarea>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" :disabled="isSubmitting" class="btn btn-custom w-100 py-2">
                            <i v-if="!isSubmitting" class="bi bi-send-fill me-2"></i>
                            <span v-else class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ isSubmitting ? 'Sending...' : 'Send Message' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            <h2 class="mb-4 section-title">Find Us</h2>
            <div class="map-container mb-4 rounded overflow-hidden shadow-sm">
                <img src="../../img/contact/concensio.jpg" alt="Our Dealership" class="img-fluid w-100 h-100 object-fit-cover">
            </div>
            <div class="showroom-box p-4 rounded text-white">
                <h5 class="text-highlight mb-3">Visit our showroom</h5>
                <p class="small mb-3">We invite you to visit our facilities where you can see our selection of premium vehicles up close and receive personalized advice.</p>
                <p class="small mb-0 fst-italic text-white-50">No appointment necessary! However, if you prefer, you can reserve a specific time for exclusive attention.</p>
            </div>
        </div>
    </div>
</section>

    <section class="map-overlay-section position-relative">
        <div class="map-background">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12317.58784344074!2d-0.376288!3d39.469907!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd604f4cf8051387%3A0x3531c352820a484c!2sValencia!5e0!3m2!1ses!2ses!4v1690000000000!5m2!1ses!2ses" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <div class="container cards-container">
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="contact-card shadow-lg">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h3 class="card-title fw-bold mb-3">EMAIL:</h3>
                        <p class="card-text">
                            <a href="mailto:info@automotoelite.com" class="text-decoration-none text-muted">info@automotoelite.com</a>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="contact-card shadow-lg">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h3 class="card-title fw-bold mb-3">MOBILE:</h3>
                        <p class="card-text text-muted">+34 600 123 456</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="contact-card shadow-lg">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3 class="card-title fw-bold mb-3">LOCATION:</h3>
                        <p class="card-text text-muted">
                            Av. Principal 123, Industrial Park<br>
                            46000 Valencia, Spain
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="py-5 bg-darker">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <h2 class="section-title mb-4">Opening <span class="text-highlight">Hours</span></h2>
                
                <div class="mb-4">
                    <div v-if="isOpen" class="status-badge" style="color: #25d366; font-weight: bold;">
                        <i class="bi bi-circle-fill me-2 small dot-pulse"></i>ABIERTO AHORA
                    </div>
                    <div v-else class="status-badge" style="color: #ff4b2b; font-weight: bold;">
                        <i class="bi bi-circle-fill me-2 small"></i>CERRADO ACTUALMENTE
                    </div>
                </div>

                <p class="text-muted">Visit us at our facilities for a personalized experience.</p>
                <div class="hours-list">
                    <div class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>Monday - Friday</span>
                        <span class="text-white">09:00 - 20:00</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>Saturdays</span>
                        <span class="text-white">10:00 - 14:00</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>Sundays & Holidays</span>
                        <span class="text-highlight fw-bold">By Appointment Only</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-1">
                <div class="vip-card p-4 rounded text-center border-orange">
                    <i class="bi bi-star-fill text-highlight mb-3 fs-1"></i>
                    <h4 class="text-white">Looking for a VIP experience?</h4>
                    <p class="text-muted small">We offer personalized advice outside regular business hours for exclusive clients. Contact us via WhatsApp to schedule a private appointment.</p>
                    <a href="https://wa.me/34654916176?text=Hola,%20me%20gustaría%20solicitar%20una%20cita%20VIP" target="_blank" class="btn btn-outline-light btn-sm mt-2">Request VIP Appointment</a>
                </div>
            </div>
        </div>
    </div>
</section>

    <section class="py-5 bg-darker">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title text-white">Talk to our <span class="text-highlight">Experts</span></h2>
                <p class="text-muted">Direct and personalized advice for each of your needs.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-4 col-lg-3">
                    <div class="team-card text-center p-4 rounded shadow-lg">
                        <div class="member-img-wrapper mb-3 mx-auto">
                            <img src="../../img/contact/javier.png" alt="Javier López" class="img-fluid">
                        </div>
                        <h5 class="fw-bold text-white mb-1">Javier López</h5>
                        <p class="text-highlight small text-uppercase fw-bold mb-3">Sales Advisor</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-team-social"><i class="bi bi-linkedin"></i></a>
                            <a href="mailto:javier@premiumcars.com" class="btn btn-team-social"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="team-card text-center p-4 rounded shadow-lg border-orange-thin">
                        <div class="member-img-wrapper mb-3 mx-auto border-orange">
                            <img src="../../img/contact/elena.png" alt="Elena García" class="img-fluid">
                        </div>
                        <h5 class="fw-bold text-white mb-1">Elena García</h5>
                        <p class="text-highlight small text-uppercase fw-bold mb-3">Valuation & Purchases</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-team-social"><i class="bi bi-linkedin"></i></a>
                            <a href="mailto:elena@premiumcars.com" class="btn btn-team-social"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="team-card text-center p-4 rounded shadow-lg">
                        <div class="member-img-wrapper mb-3 mx-auto">
                            <img src="../../img/contact/marcos.png" alt="Marcos Sanz" class="img-fluid">
                        </div>
                        <h5 class="fw-bold text-white mb-1">Marcos Sanz</h5>
                        <p class="text-highlight small text-uppercase fw-bold mb-3">VIP Assistance</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-team-social"><i class="bi bi-linkedin"></i></a>
                            <a href="mailto:marcos@premiumcars.com" class="btn btn-team-social"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Frequently <span class="text-highlight">Asked Questions</span></h2>
                <p class="text-muted">Find answers to the most common questions about our services</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion custom-accordion" id="accordionFAQ">
                        
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    What types of vehicles do you offer?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    We offer an exclusive selection of sports and high-end vehicles, including brands such as BMW, Audi, Porsche and Ducati.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Do you offer financing?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    Yes, we work with leading financial institutions to offer tailored plans.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    Can I do a test drive?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    Absolutely. You can schedule a test drive through our contact form.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                    Do you accept vehicles as part payment?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    Yes, we perform valuations of your current vehicle to deduct from the final price.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                    What warranties do the vehicles include?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse show" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    All our vehicles include a premium warranty of 12 to 24 months depending on model and year.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix">
                                    Do you ship to other cities?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted">
                                    We provide insured shipping across the peninsula and islands. Ask us about rates.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


<div class="whatsapp-widget">
    <div v-if="isChatOpen" class="wa-chat-box">
        <div class="wa-chat-header">
            <i class="bi bi-whatsapp"></i>
            <div class="header-text">
                <h5>Have any questions?</h5>
                <p>Click our customer support below to chat on WhatsApp</p>
            </div>
        </div>
        <div class="wa-chat-body">
            <span class="reply-time">The team usually replies within 24 hours</span>
            <a href="https://wa.me/34654916176" target="_blank" class="wa-contact-card">
                <div class="wa-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Support">
                    <span class="wa-online-dot" :style="{ backgroundColor: isOpen ? '#25d366' : '#ff4b2b' }"></span>
                </div>
                <div class="wa-contact-info">
                    <span class="name">Elite Drive Support</span>
                    <span class="status">Ask me anything</span>
                </div>
                <i class="bi bi-whatsapp ms-auto text-success fs-4"></i>
            </a>
        </div>
    </div>

    <button @click="toggleWhatsApp" class="wa-main-button">
        <i v-if="!isChatOpen" class="bi bi-whatsapp fs-4"></i>
        <i v-else class="bi bi-x-lg fs-4"></i>
        <span>Shall we talk?</span>
    </button>
</div>

</template>



<style scoped>
/* 'scoped' hace que los estilos solo afecten a este componente */
@import "../../css/contacto.css";
</style>