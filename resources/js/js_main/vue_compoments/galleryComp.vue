<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue"

const galleryURLs = ref([])
const loading = ref(true)
const error = ref(null)
const events = ref([])
const currentIndex = ref(0)
let interval = null
const props = defineProps({
    getUrl: { type: String, default: '/api/events' }
})
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
async function loadEvents() {
    loading.value = true
    error.value = false

    try {
        const res = await fetch(props.getUrl, {
            headers: { 'Accept': 'application/json' }
        })

        if (!res.ok) {
            error.value = true
            return
        }

        events.value = await res.json()
    } catch (e) {
        error.value = true
    } finally {
        loading.value = false
    }
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

    await loadEvents()
})
function formatDate(value) {
    if (!value) return ''
    return new Date(value).toLocaleString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
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

    <div class="defaultContainer">
        <p>
            Unser Spiel ist Low-Fantasy, rau und immersiv –
            ganz nach dem Prinzip: „Du kannst, was du darstellen kannst.“
        </p>

        <p>Lust auf uns? Melde dich einfach. Wir sind vertreten auf:</p>

        <div class="sozial__media">
            <a class="sozial__link instagram"
               href="https://www.instagram.com/talagrader.sturmpioniere/"
               target="_blank" rel="noopener noreferrer" aria-label="Eckland-LARP auf Instagram">
                <span class="sozial__icon"></span>
                <span>Instagram</span>
            </a>

            <a class="sozial__link facebook"
               href="https://www.facebook.com/KaiserreichEcklandTalagradsSturmpioniere/"
               target="_blank" rel="noopener noreferrer" aria-label="Eckland-LARP auf Facebook">
                <span class="sozial__icon"></span>
                <span>Facebook</span>
            </a>

            <a class="sozial__link discord"
               href="https://discord.gg/"
               target="_blank" rel="noopener noreferrer" aria-label="Eckland-LARP auf Discord">
                <span class="sozial__icon"></span>
                <span>Discord</span>
            </a>
        </div>

        <p>Oder einfach auf dieser Website unter <a class="kontakt" href="/ueber-uns/kontakt">Kontakt</a>.</p>
    </div>
    <div class="trennlinie"></div>



    <div class="defaultContainer">
        <p>
            Es gibt viele gute Events. Hier werden die kommenden Events aufgelistet.
        </p>

        <p>
            Lust, in eine andere Welt einzutauchen? Melde dich einfach!
            In unserer LARP-Gruppe gibt es Spieler mit über 15 Jahren Erfahrung,
            die sich immer über neues, frisches Blut freuen.
        </p>

        <p>
            Bei ernsthaftem Interesse können wir Leihkleidung zum Ausprobieren stellen.
        </p>
    </div>

    <section class="eventList">
        <p v-if="loading" class="eventList__info">Lade Neuigkeiten...</p>
        <p v-else-if="error" class="eventList__info">Neuigkeiten konnten nicht geladen werden.</p>
        <p v-else-if="!events.length" class="eventList__info">Aktuell keine Termine.</p>

        <article v-for="event in events" :key="event.id" class="eventCard">
            <h3>{{ event.title }}</h3>

            <p class="eventCard__date">
                {{ formatDate(event.starts_at) }}
                <span v-if="event.ends_at"> – {{ formatDate(event.ends_at) }}</span>
            </p>

            <p v-if="event.description" class="eventCard__text">{{ event.description }}</p>
        </article>
    </section>

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


.eventList {
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    padding: 0 15px;

    display: flex;
    flex-direction: column;
    gap: 14px;
    color: #fff;
}

.eventList__info {
    text-align: center;
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
}

.eventCard {
    padding: 14px 18px;
    border-radius: 12px;
    border-left: 4px solid #dc2929;

    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);

    h3 {
        margin: 0 0 4px;
        font-size: 1.15rem;
    }

    &__date {
        margin: 0 0 10px;
        font-size: 0.85rem;
        color: #dc9a9a;
    }

    &__text {
        margin: 0;
        line-height: 1.5;
        white-space: pre-line;   // Zeilenumbrüche aus der Beschreibung bleiben erhalten
    }
}

.sozial__media {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 12px 0;
}

.sozial__link {
    display: flex;
    align-items: center;
    gap: 8px;

    padding: 8px 14px;
    border-radius: 10px;

    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);

    color: #fff;
    text-decoration: none;
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
    transition: all 0.2s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }

    &:focus-visible {
        outline: 2px solid #dc2929;
        outline-offset: 2px;
    }
}

// Icon als Maske: übernimmt die Textfarbe (currentColor) und lässt sich per CSS einfärben
.sozial__icon {
    width: 20px;
    height: 20px;
    background-color: currentColor;

    -webkit-mask: var(--icon) center / contain no-repeat;
    mask: var(--icon) center / contain no-repeat;
    transition: color 0.2s ease;
}

.instagram { .sozial__icon { --icon: url('/img/icons/instagram.svg'); } &:hover { color: #e4405f; } }
.facebook  { .sozial__icon { --icon: url('/img/icons/facebook.svg');  } &:hover { color: #4c8df6; } }
.discord   { .sozial__icon { --icon: url('/img/icons/discord.svg');   } &:hover { color: #7983f5; } }
.kontakt{
    color: white;

}

</style>
