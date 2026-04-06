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
    <div class="trennlinie"></div>
    <div class="galleryContainer">
        <div class="gallery">
            <p v-if="loading">Lade Galerie…</p>
            <p v-else-if="error">{{ error }}</p>

            <div v-else class="slider">
                <button class="nav left" @click="prev">‹</button>

                <img
                    v-if="galleryURLs.length"
                    :src="'storage/'+galleryURLs[currentIndex]"
                    class="slideImage"
                    loading="lazy"
                />

                <button class="nav right" @click="next">›</button>
            </div>
        </div>
    </div>


    <div class="trennlinie"></div>
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
    height: 70vh;
    background:
        linear-gradient(145deg, rgba(55, 18, 18, 0.88), rgba(20, 20, 20, 0.9));
    backdrop-filter: blur(4px);
    border-radius: 10px;
    padding: 0.45rem; /* dünnerer Rahmen */
    border: 2px solid rgba(120, 30, 30, 0.95);
    box-shadow:
        0 0 0 1px rgba(170, 70, 70, 0.35),
        0 12px 35px rgba(0, 0, 0, 0.65),
        inset 0 1px 0 rgba(255, 180, 180, 0.08),
        inset 0 -1px 0 rgba(0, 0, 0, 0.45);

    display: flex;
    justify-content: center;
    align-items: center;
}

/* Slider */
.slider {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    border-radius: 8px;
}

/* Bild bleibt IMMER gleich groß */
.slideImage {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 6px;
    transition: opacity 0.5s ease;
}

/* Navigation */
.nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(180deg, rgba(95, 20, 20, 0.9), rgba(45, 10, 10, 0.92));
    color: #f3e7e7;
    border: 1px solid rgba(160, 70, 70, 0.9);
    font-size: 2.2rem;
    padding: 0.45rem 1rem;
    cursor: pointer;
    border-radius: 6px;
    transition: 0.2s ease;
    backdrop-filter: blur(3px);
    box-shadow:
        0 4px 14px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 180, 180, 0.15);
    z-index: 2;
}

.nav:hover {
    background: linear-gradient(180deg, rgba(140, 30, 30, 0.96), rgba(75, 15, 15, 0.96));
    transform: translateY(-50%) scale(1.04);
}

.nav.left {
    left: 18px;
}

.nav.right {
    right: 18px;
}

/* Tablet */
@media (max-width: 900px) {
    .gallery {
        height: 55vh;
        padding: 0.35rem;
    }

    .nav {
        font-size: 2rem;
        padding: 0.35rem 0.8rem;
    }

    .nav.left {
        left: 10px;
    }

    .nav.right {
        right: 10px;
    }
}

/* Mobile */
@media (max-width: 600px) {
    .gallery {
        height: 45vh;
        padding: 0.3rem;
        border-radius: 8px;
    }

    .slider {
        border-radius: 6px;
    }

    .slideImage {
        border-radius: 4px;
    }

    .nav {
        font-size: 1.7rem;
        padding: 0.25rem 0.65rem;
    }

    /* weiter außen */
    .nav.left {
        left: 4px;
    }

    .nav.right {
        right: 4px;
    }
}
</style>
