<!-- Guillermo Soto ============================================================================= -->

<script setup>
    import { ref, onMounted, onUnmounted } from "vue";

    const props = defineProps({
        images: {
            type: Array,
            required: true,
            default: () => []
        }
    });

    // Random image start
    const currentIndex = ref(Math.floor(Math.random() * props.images.length));
    let timeoutId = null;

    const getRandomInterval = () => Math.random() * (8000 - 4000) + 4000;

    const playCarousel = () => {
        // Clear interval
        if (timeoutId) clearTimeout(timeoutId);
        // Next slide
        currentIndex.value = (currentIndex.value + 1) % props.images.length;
        // Next Carousel display
        timeoutId = setTimeout(playCarousel, getRandomInterval());
    };

    onMounted(() => {
        // Start carousel whith random delay
        timeoutId = setTimeout(playCarousel, Math.random() * 3000);
        console.log("Cargado")
    });

    onUnmounted(() => clearTimeout(timeoutId));
</script>

<style scoped>
    @import "../../css/index_style.css";
</style>

<template>
    <div class="road-carousel-container shadow-lg">
        <div class="road-carousel-inner">
            <img v-for="(image, index) in images" 
                :key="index" 
                :src="image" 
                :class="{ 
                    'active-road-image': currentIndex === index, 
                    'prepare-above': (currentIndex + 1) % images.length === index,
                    'road-image': true 
                }">
        </div>
    </div>
</template>