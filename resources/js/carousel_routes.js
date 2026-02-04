import { ref, onMounted, onUnmounted } from "vue";

// ... (tus imports y código existentes) ...

const roadImages = ref([
    '../../img/index/carousel/road1.jpg',
    '../../img/index/carousel/road2.jpg',
    '../../img/index/carousel/road3.jpg',
    '../../img/index/carousel/road4.jpg',
    '../../img/index/carousel/road5.jpg'
]);

const currentIndex = ref(0);
let intervalId = null;
let animationTimeoutId = null; // Para el temporizador de animación

const startCarousel = () => {
    // Limpia cualquier intervalo anterior para evitar duplicados
    if (intervalId) {
        clearInterval(intervalId);
    }

    // Función para obtener un tiempo aleatorio entre 1 y 4 segundos
    const getRandomInterval = () => Math.random() * (4000 - 1000) + 1000; // ms

    const nextSlide = () => {
        // Limpia el timeout anterior para la animación
        if (animationTimeoutId) {
            clearTimeout(animationTimeoutId);
        }

        const prevIndex = currentIndex.value;
        currentIndex.value = (currentIndex.value + 1) % roadImages.value.length;

        // Si quieres aplicar clases de animación específicas (animate-in/out)
        // podrías necesitar un sistema más complejo de V-transition o refactorizar el CSS.
        // Por simplicidad, la transición CSS ya maneja el desplazamiento.

        // Reinicia el temporizador con un nuevo intervalo aleatorio
        intervalId = setTimeout(nextSlide, getRandomInterval());
    };

    // Inicia el carrusel con un retardo aleatorio inicial
    intervalId = setTimeout(nextSlide, getRandomInterval());
};

onMounted(() => {
    startCarousel();
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
    if (animationTimeoutId) {
        clearTimeout(animationTimeoutId);
    }
});

// ... (resto de tu script setup) ...