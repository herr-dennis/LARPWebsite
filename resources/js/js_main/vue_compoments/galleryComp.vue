<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue"

const galleryURLs = ref([])
const loading = ref(true)
const error = ref(null)

const currentIndex = ref(0)
let interval = null

const next = () => {
    if (galleryURLs.value.length === 0) return
    currentIndex.value =
        (currentIndex.value + 1) % galleryURLs.value.length
}

const prev = () => {
    if (galleryURLs.value.length === 0) return
    currentIndex.value =
        (currentIndex.value - 1 + galleryURLs.value.length) %
        galleryURLs.value.length
}

const startAutoSlide = () => {
    interval = setInterval(() => {
        next()
    }, 4000) // alle 4 Sekunden
}

const stopAutoSlide = () => {
    if (interval) clearInterval(interval)
}

onMounted(async () => {
    try {
        const res = await fetch("/api/pictures")
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        galleryURLs.value = await res.json()
        startAutoSlide()
    } catch (e) {
        error.value = "Galerie konnte nicht geladen werden."
    } finally {
        loading.value = false
    }
})

onBeforeUnmount(() => {
    stopAutoSlide()
})
</script>

<template>
    <div class="galleryContainer">
        <div class="gallery">
            <p v-if="loading">Lade Galerie…</p>
            <p v-else-if="error">{{ error }}</p>

            <div v-else class="slider">
                <button class="nav left" @click="prev">‹</button>

                <img
                    v-if="galleryURLs.length"
                    :src="galleryURLs[currentIndex]"
                    class="slideImage"
                />

                <button class="nav right" @click="next">›</button>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

.galleryContainer {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 3rem 2rem;
}

/* Haupt-Galerie-Box */
.gallery {
    width: 100%;
    max-width: 1400px;
    height: 70vh; // 🔥 70% der Bildschirmhöhe
    background: rgba(20, 20, 20, 0.75);
    backdrop-filter: blur(6px);
    border-radius: 16px;
    padding: 1rem;

    display: flex;
    justify-content: center;
    align-items: center;

    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
}

/* Slider */
.slider {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Bild bleibt IMMER gleich groß */
.slideImage {
    width: 100%;
    height: 100%;
    object-fit: cover;   // 🔥 Bild füllt Container
    border-radius: 12px;
    transition: opacity 0.5s ease;
}

/* Navigation */
.nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    font-size: 2.5rem;
    padding: 0.6rem 1.2rem;
    cursor: pointer;
    border-radius: 10px;
    transition: 0.2s ease;
    backdrop-filter: blur(3px);
}

.nav:hover {
    background: rgba(163, 32, 32, 0.9);
}

.nav.left {
    left: 20px;
}

.nav.right {
    right: 20px;
}

/* Mobile Anpassung */
@media (max-width: 900px) {
    .gallery {
        height: 55vh;
    }
}

@media (max-width: 600px) {
    .gallery {
        height: 45vh;
    }
}

</style>
