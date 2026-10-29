<script setup>
import {computed, onMounted, ref} from "vue";
import {useAuthStore} from "../stores/authStore.js";

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const authStore = useAuthStore();
const admin = computed(() => authStore.adminState)
const isLoggedIn = computed(() => authStore.isLoggedIn)

const videos = ref([])

const title_form = ref("");
const description_form = ref("");
const url_form = ref("");

const saving = ref(false)
const errors = ref({})
const showForm = ref(false);

function  toggleInsertWindow(){

    if(showForm.value){
        showForm.value = false;
    }else {
        showForm.value = true;
    }

}
async function deleteVideo(id) {
    if (!confirm("Video wirklich löschen?")) return

    try {
        const response = await fetch(`/api/videos/${id}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            }
        })

        if (!response.ok) {
            alert("Fehler API Call")
            return
        }

        videos.value = await response.json()   // Server liefert die frische Liste
    } catch (e) {
        alert("Server nicht erreichbar")
    }
}
async function insertVideo() {
    if (saving.value) return
    saving.value = true
    errors.value = {}

    try {
        const response = await fetch("/api/videos", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({
                title: title_form.value,
                description: description_form.value || null,
                url: url_form.value,
            })
        })

        if (response.status === 422) {
            const data = await response.json()
            errors.value = data.errors ?? {}
            return
        }

        if (!response.ok) {
            alert("Fehler API Call")
            return
        }

        videos.value = await response.json()   // Server liefert die frische Liste

        title_form.value = ""
        description_form.value = ""
        url_form.value = ""
        showForm.value = false
    } catch (e) {
        alert("Server nicht erreichbar")
    } finally {
        saving.value = false
    }
}
async function getVideos() {
    try {
        const response = await fetch("/api/videos", {
            headers: { "Accept": "application/json" }
        })

        if (!response.ok) {
            alert("Fehler API Call")
            return
        }

        videos.value = await response.json()
    } catch (e) {
        alert("Server nicht erreichbar")
    }
}

function toEmbedUrl(url) {
    try {
        const u = new URL(url)
        let id = null

        if (u.hostname === 'youtu.be') {
            id = u.pathname.slice(1)
        } else if (u.hostname.endsWith('youtube.com')) {
            if (u.pathname === '/watch') id = u.searchParams.get('v')
            else if (u.pathname.startsWith('/embed/')) id = u.pathname.split('/')[2]
            else if (u.pathname.startsWith('/shorts/')) id = u.pathname.split('/')[2]
        }

        return id ? `https://www.youtube-nocookie.com/embed/${id}` : null
    } catch {
        return null
    }
}

// nur Videos, deren URL sich einbetten lässt
const embedVideos = computed(() =>
    videos.value
        .map(v => ({ ...v, embed: toEmbedUrl(v.url) }))
        .filter(v => v.embed)
)

onMounted(()=>{
    getVideos();
})

</script>

<template>
    <button   type="button" class="FormDefaultContainer__Button"   @click.stop="toggleInsertWindow" >
        Videos einfügen</button>
    <div v-if="isLoggedIn && admin" v-show="showForm" class="FormDefaultContainer">
        <p>Videos einfügen</p>

        <form class="FormDefaultContainer__Form" @submit.prevent="insertVideo">
            <label>Titel des Videos</label>
            <input v-model="title_form" type="text" required>
            <span v-if="errors.title">{{ errors.title[0] }}</span>

            <label>Beschreibung (Optional)</label>
            <input v-model="description_form" type="text">

            <label>URL des Videos</label>
            <input v-model="url_form" type="url" required>
            <span v-if="errors.url">{{ errors.url[0] }}</span>

            <button type="submit" :disabled="saving">Speichern</button>
            <button type="button" class="FormDefaultContainer__Button" @click.stop="toggleInsertWindow">
                Schließen
            </button>
        </form>
    </div>


    <div class="videosContainer">
        <p v-if="!embedVideos.length" class="videosContainer__info">Noch keine Videos vorhanden.</p>

        <article v-for="video in embedVideos" :key="video.id" class="videoCard">
            <div class="videoCard__head">
                <h3>{{ video.title }}</h3>

                <button
                    v-if="isLoggedIn && admin"
                    type="button"
                    class="videoCard__delete"
                    aria-label="Video löschen"
                    @click="deleteVideo(video.id)"
                >
                    Löschen
                </button>
            </div>
            <h3>{{ video.title }}</h3>

            <div class="videoCard__frame">
                <iframe
                    :src="video.embed"
                    :title="video.title"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                ></iframe>
            </div>

            <p v-if="video.description" class="videoCard__text">{{ video.description }}</p>
        </article>
    </div>
</template>

<style scoped lang="scss">
@use "../../../../resources/css/css_main/defaultForm.scss";
@use "../../../../resources/css/css_main/defaultButton";

.videosContainer {
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    padding: 0 15px;

    display: flex;
    flex-direction: column;
    gap: 18px;
    color: #fff;

    &__info {
        text-align: center;
        text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
    }
}

.videoCard {
    padding: 14px 18px;
    border-radius: 12px;
    border-left: 4px solid #dc2929;

    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);

    h3 {
        margin: 0 0 10px;
        font-size: 1.15rem;
    }

    &__frame {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;      // hält das Seitenverhältnis responsiv
        border-radius: 8px;
        overflow: hidden;
        background: #000;

        iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    }

    &__text {
        margin: 10px 0 0;
        line-height: 1.5;
        white-space: pre-line;
    }
}

.videoCard {
    &__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;

        h3 {
            margin: 0;
        }
    }

    &__delete {
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s ease;

        &:hover {
            background: #dc2929;
        }
    }
}
</style>
