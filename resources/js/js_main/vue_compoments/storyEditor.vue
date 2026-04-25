<script setup>
import {computed,  onMounted, ref, watch} from "vue";
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
import { useAuthStore } from '../stores/authStore.js'
const authStore = useAuthStore();
const admin = computed( ()=> authStore.adminState)
const isLoggedIn = computed( ()=> authStore.isLoggedIn)
const showInsertWindow = ref(false);
const msg = ref("");
const stories = ref([]);
const showAlertDialog = ref(false);
const currentDeleteStory = ref(null);
import AlertDialog from "./altertWindow.vue"

//Einstellungen
//Images
const baseUrl = "/storage/";

const props = defineProps({
    postUrl: {
        type: String,
        required: true
    },
    getUrl: {
        type: String,
        required: true
    }
});

async function handleInsertAction(){

    const text = document.getElementById("inputFormText").value;
    const title = document.getElementById("inputFormTitle").value;
    const fileInput = document.getElementById("inputFormData");
    const file = fileInput.files[0];

    const formData = new FormData();
    formData.append("title", title);
    formData.append("text", text);

    if(file){
        formData.append("image", file);
    }

    try{

        const response = await fetch(props.postUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                "Accept": "application/json"
            },
            body: formData
        });

        const data = await response.json();

        if(!response.ok){
            msg.value = data.message || "Fehler beim Speichern";
            return;
        }

        msg.value = data.message || "Erfolgreich gespeichert";
        toggleInsertWindow();
         await getStories();
        const el = await existsDomElement(stories.value[stories.value.length - 1].id);
        el.scrollIntoView({ behavior: "smooth" });

    }catch(error){
        msg.value = "Server nicht erreichbar";

    }
}

function existsDomElement(id, maxAttempts = 15, intervalTime = 50) {
    let attempts = 0;
    return new Promise((resolve) => {
        const interval = setInterval(() => {
            const el = document.getElementById(id);
            attempts++;
            if (el) {
                clearInterval(interval);
                resolve(el);
            }

            if (attempts >= maxAttempts) {
                clearInterval(interval);
                resolve(null);
            }
        }, intervalTime);
    });
}

addEventListener("keydown", (e) => {
    if (e.key === "Escape" && showInsertWindow.value) {
        toggleInsertWindow()
    }
});

function toggleInsertWindow(){
    if(showInsertWindow.value){
        showInsertWindow.value = false;
    }else {
        showInsertWindow.value = true;
    }
}

//OnMounted
async function getStories() {
    try {
        const response = await fetch(props.getUrl, {
            headers: {
                "X-CSRF-TOKEN": csrf,
                "Accept": "application/json"
            }
        });

        const data = await response.json();

        if (!response.ok) {
            msg.value = data.message || "Fehler beim Abrufen";
            return;
        }

        stories.value = data;

    } catch (error) {
        msg.value = "Server nicht erreichbar";

    }
}

onMounted(() => {
    getStories();
});

//aus NewLine einen <p> machen.
function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

function openDialog(id) {
    showAlertDialog.value = true;
    currentDeleteStory.value = id;
}

 async function deleteStorys(){

    const response = await fetch(props.postUrl+"/"+currentDeleteStory.value, {
        method: "DELETE",
        headers:{
            "X-CSRF-TOKEN": csrf,
            "Accept": "application/json"
        }
    });
     if(!response.ok){
          alert("Fehler beim Löschvorgang");
     }

     const data = await response.json();
     stories.value = data;

}

function handleDelete(id) {
    showAlertDialog.value = false;
    deleteStorys();
}
function nl2p(text) {
    if (!text) return "";

    return text
        .split(/\n+/)
        .filter(line => line.trim() !== "")
        .map(line => `<p>${escapeHtml(line)}</p>`)
        .join("");
}
const filteredStories = computed(() => {
    return stories.value.filter(story => story != null);
});

</script>

<template>

    <teleport to="#StoryHeader"  >
        <div class="defaultContainer"    >
            <p>Liste der Geschichten</p>
            <div class="defaultContainer__a" >
                <div >
                    <a
                        v-for="story in filteredStories"
                        :key="story.id"
                        :href="'#' + story.id"
                    >
                        {{ story.title }}
                    </a>
                </div>

            </div>
        </div>

    </teleport>

        <div  class="DefaultBtnContainer"  v-if="isLoggedIn && admin">
            <button   class="DefaultBtn"   type="button"  @click="toggleInsertWindow"  >Neuer Eintrage verfassen</button>
        </div>

       <div  v-show="showInsertWindow"  class="FormDefaultContainer">
           <form  class="FormDefaultContainer__Form"    enctype="multipart/form-data" @submit.prevent="handleInsertAction">
               <label  class="FormDefaultContainer__Label" >Title</label>
               <input id="inputFormTitle" class="FormDefaultContainer__Input"  type="text" placeholder="Titel"/>
               <label class="FormDefaultContainer__Label" >Text</label>
               <textarea class="FormDefaultContainer__TextAreaInput" id="inputFormText" placeholder="Text"></textarea>
               <input id="inputFormData" class="FormDefaultContainer__Input"  type="file" name="image" >
               <label>Pro eingefügter Text ein Bild. Wenn ein Text mehrere Bilder haben soll,dann einfach Text splitten und Title weglassen.</label>
               <button type="submit"  class="FormDefaultContainer__Button">Einfügen</button>
               <label  class="FormDefaultContainer__Label">{{msg}} </label>
                <button   type="button" class="FormDefaultContainer__Button"   @click.stop="toggleInsertWindow" >Schließen</button>
           </form>
       </div>

    <div v-for="story in stories" :key="story.id" :id="story.id" class="defaultContainer">

        <div class="DefaultBtnContainer">
            <button type="button" class="DefaultBtn" v-if="isLoggedIn&&admin"  @click.stop="openDialog(story.id)" >Löschen</button>
        </div>


        <h2 v-if="story.title">{{ story.title}}</h2>

        <div v-html="nl2p(story.text)"></div>

        <img loading="lazy" v-if="story.image" :src="baseUrl + story.image" alt="Bild">

    </div>

    <AlertDialog
        :open="showAlertDialog"
        @close="showAlertDialog = false"
        @confirm="handleDelete"
    />

</template>

<style scoped lang="scss">
@use "../../../../resources/css/css_main/defaultForm.scss";
@use "../../../../resources/css/css_main/defaultButton";
</style>
