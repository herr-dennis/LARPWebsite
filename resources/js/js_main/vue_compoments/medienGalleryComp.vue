<script setup>

import { useAuthStore } from '../stores/authStore.js'
import {computed, onBeforeUnmount, onMounted, ref} from "vue";
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const authStore = useAuthStore();
const isLoggedIn = computed(() => authStore.isLoggedIn );
const isAdmin = computed(() => authStore.adminState );
const files = ref([])
const loading = ref(false)
const message = ref("")
const fileInput = ref(null);
const galleryPic = ref([]);
const selectedImage = ref(null)

function openLightbox(pic) {
    selectedImage.value = pic
}

function closeLightbox() {
    selectedImage.value = null
}function handleKeydown(event) {
    if (event.key === 'Escape') {
        closeLightbox()
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
})
function handleFiles(event) {
    files.value = Array.from(event.target.files)
}

function formatSize(size) {
    if (size < 1024) return size + " B"
    if (size < 1024 * 1024) return (size / 1024).toFixed(1) + " KB"
    return (size / (1024 * 1024)).toFixed(1) + " MB"
}

async function uploadFiles() {
    loading.value = true
    message.value = ""

    try {
        const formData = new FormData()

        files.value.forEach((file) => {
            formData.append("files[]", file)
        })

        const response = await fetch("/api/storage/multi", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: formData
        })

        const data = await response.json()

        if (!response.ok) {
            message.value = data.message + data.errors || "Upload fehlgeschlagen"
            return
        }
        message.value = "Upload erfolgreich"
        files.value = []
    } catch (error) {
        message.value = "Netzwerkfehler"
    } finally {
        loading.value = false
    }
}


async function getPicsForGallery(){

    try{
        const response = await fetch("/api/storage/pics",{
            headers:{
                "Accept": "application/json",
            }
        })
        if(!response.ok){
            alert("API Call Fehler")
        }

        const data = await response.json();
        galleryPic.value = data;

    }catch(error){
        alert("Netzwerkfehler")
    }

}

getPicsForGallery();

function resetAuswahl(){
    files.value = []

    if (fileInput.value) {
        fileInput.value.value = null
    }
}
</script>

<template>
    <div class="trennlinie"></div>
    <div class="medienGallery">
        <div
            v-for="(pic, i) in galleryPic"
            :key="i"
            class="medienGallery__item"
        >
            <img
                class="medienGallery__image"
                :src="pic"
                alt=""
                loading="lazy"
                @click="openLightbox(pic)"
            >
        </div>
    </div>

    <div v-if="selectedImage" class="lightbox" @click="closeLightbox">
        <div class="lightbox__content" @click.stop>
            <button class="lightbox__close" @click="closeLightbox">×</button>
            <img class="lightbox__image" :src="selectedImage" alt="">
        </div>
    </div>


    <div class="forms-wrapper"  v-if="isLoggedIn&&isAdmin">

        <form  class="Formcontainer"  enctype="multipart/form-data"  @submit.prevent="uploadFiles" >
            <label>Du kannst mehrere Dateien direkt hochladen. </label>
            <button
                type="button"
                @click="resetAuswahl"
                v-bind:disabled="files.length === 0"
            >
                Löschen Auswahl
            </button>
            <div class="form-group">
                <label>Upload</label>
                <input   @change="handleFiles" ref="fileInput"  multiple id="fileInput" type="file"  >
                <ul v-if="files.length">
                    <li v-for="(file, index) in files" :key="index">
                        {{ file.name }} - {{ formatSize(file.size) }}
                    </li>
                </ul>
             <button type="submit" >Upload....</button>
                <p  class="altertContainer" v-if="message">{{ message }}</p>
            </div>

        </form>
    </div>
</template>

<style scoped lang="scss">

@import "./../../../../resources/css/css_main/defaultForm.scss";

.medienGallery {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.2rem;
    padding: 1rem 0;
    box-sizing: border-box;
}

.medienGallery__item {
    width: 100%;
    background: rgba(20, 20, 20, 0.75);
    border: 1px solid rgba(120, 30, 30, 0.95);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.22);
    padding: 0.75rem;
    box-sizing: border-box;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    backdrop-filter: blur(4px);
}

.medienGallery__item:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.28);
}

.medienGallery__image {
    display: block;
    width: 100%;
    height: 100%;
    max-height: 420px;
    object-fit: contain;
    border-radius: 12px;
    background: #111;
}

@media (max-width: 900px) {
    .medienGallery {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .medienGallery {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .medienGallery__item {
        padding: 0.6rem;
        border-radius: 14px;
    }

    .medienGallery__image {
        max-height: 320px;
        border-radius: 10px;
    }
}



.lightbox {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.82);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    z-index: 9999;
    box-sizing: border-box;
}

.lightbox__content {
    position: relative;
    max-width: 95vw;
    max-height: 95vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.lightbox__image {
    max-width: 95vw;
    max-height: 90vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 14px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.45);
    background: #111;
}

.lightbox__close {
    position: absolute;
    top: -0.75rem;
    right: -0.75rem;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 50%;
    background: rgba(20, 20, 20, 0.92);
    color: white;
    font-size: 1.6rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.28);
}
</style>
