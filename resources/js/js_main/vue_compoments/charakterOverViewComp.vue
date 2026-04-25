<script setup>
import {computed,  onMounted, ref, watch} from "vue";
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
import { useAuthStore } from '../stores/authStore.js'
const authStore = useAuthStore();
const admin = computed( ()=> authStore.adminState)
const isLoggedIn = computed( ()=> authStore.isLoggedIn)
const showInsertWindow = ref(false);
const msg = ref("");
const charakter =ref([]);

function  diretionToURL(url){
    window.location.href = url;
}
function toggleInsertWindow(){
    if(showInsertWindow.value){
        showInsertWindow.value = false;
    }else {
        showInsertWindow.value = true;
    }
}

onMounted(()=>{
    getChars();
})

async function getChars() {
    try {
        const response = await fetch("/api/ueber-uns/story/charakter", {
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

        charakter.value = data;

    } catch (error) {
        msg.value = "Server nicht erreichbar";

    }
}

async function deleteStorys(id){

    const response = await fetch(`/api/ueber-uns/story/charakter/${id}`, {
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
    charakter.value = data.data;

}

function openDeleteDialog(id) {
    const sicher = confirm("Sicher löschen?");

    if (!sicher) {
        return;
    }

    deleteStorys(id);
}

async function handleInsertAction(){

    const text = document.getElementById("inputFormText").value;
    const name = document.getElementById("inputFormName").value;
    const fileInput = document.getElementById("inputFormData");
    const file = fileInput.files[0];

    const formData = new FormData();
    formData.append("name", name);
    formData.append("text", text);

    if(file){
        formData.append("image", file);
    }

    try{

        const response = await fetch("/api/ueber-uns/story/charakter", {
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
        charakter.value =data;
        toggleInsertWindow();

    }catch(error){
        msg.value = "Server nicht erreichbar";

    }
}
</script>

<template>

    <div  class="DefaultBtnContainer"  v-if="isLoggedIn && admin">
        <button   class="DefaultBtn"   type="button"  @click="toggleInsertWindow()"   >Neuer Charakter einfügen</button>
    </div>
    <div  v-show="showInsertWindow"  class="FormDefaultContainer">
        <form  class="FormDefaultContainer__Form"    enctype="multipart/form-data" @submit.prevent="handleInsertAction">

            <label class="FormDefaultContainer__Label" >Name</label>
            <input type="text" class="FormDefaultContainer__Input" id="inputFormName" placeholder="Name"></input>

            <label class="FormDefaultContainer__Label" >Text</label>
            <textarea class="FormDefaultContainer__TextAreaInput" id="inputFormText" placeholder="Text"></textarea>

            <input id="inputFormData" class="FormDefaultContainer__Input"  type="file" name="image" >
            <label>Pro eingefügter Text ein Bild. Wenn ein Text mehrere Bilder haben soll,dann einfach Text splitten und Title weglassen.</label>

            <button type="submit"  class="FormDefaultContainer__Button">Einfügen</button>
            <label  class="FormDefaultContainer__Label">{{msg}} </label>
            <button   type="button" class="FormDefaultContainer__Button"   @click.stop="toggleInsertWindow" >Schließen</button>
        </form>

    </div>


    <div class="storyCardsWrapper">
        <div
            v-for="char in charakter"
            :key="char.id"
            class="storyCards"
            @click.stop="diretionToURL('/ueber-uns/story/charakter/' + char.id)"
        >
            <label>{{ char.name }}</label>

            <div class="storyCard">
                <img
                    :src="'/storage/' + char.image"
                    :alt="char.name"
                >
            </div>

            <div class="DefaultBtnContainer">
                <button type="button" class="DefaultBtn" v-if="isLoggedIn&&admin"  @click.stop=" openDeleteDialog(char.id)" >Löschen</button>
            </div>
        </div>

    </div>


</template>

<style scoped lang="scss">
@use "../../../../resources/css/css_main/defaultForm.scss";
@use "../../../../resources/css/css_main/defaultButton";

.storyCardsWrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    width: 90%;
    padding: 2rem 1rem;
    box-sizing: border-box;
    max-width: 1200px;
    margin: 0 auto;
}

.storyCards {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;

    width: min(360px, 100%);
    min-height: 420px;
    padding: 1.4rem 1.2rem 1.8rem 1.2rem;

    background: rgba(20, 20, 20, 0.78);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;

    box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.45),
        inset 0 0 20px rgba(255, 255, 255, 0.03);

    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;

    cursor: pointer;
}

.storyCards:hover {
    transform: translateY(-6px);
    border-color: rgba(133, 19, 19, 0.65);
    box-shadow:
        0 16px 35px rgba(0, 0, 0, 0.55),
        0 0 18px rgba(133, 19, 19, 0.18);
}

.storyCards label {
    margin-bottom: 1rem;
    font-size: 1.7rem;
    font-weight: 700;
    color: #f1ebe0;
    letter-spacing: 0.04em;
    text-align: center;
    cursor: pointer;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.7);
}

.storyCard {
    width: 100%;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;

    padding: 1rem;
    border-radius: 10px;

    background:
        linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.03) 0%,
                rgba(0, 0, 0, 0.12) 100%
        );
}

.storyCard img {
    width: 100%;
    max-width: 240px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 8px 12px rgba(0, 0, 0, 0.45));
    transition: transform 0.25s ease;
}

.storyCards:hover .storyCard img {
    transform: scale(1.04);
}

@media (max-width: 900px) {
    .storyCards {
        width: min(420px, 100%);
        min-height: 380px;
    }

    .storyCards label {
        font-size: 1.5rem;
    }
}

@media (max-width: 600px) {
    .storyCardsWrapper {
        flex-direction: column;
        align-items: center;
        gap: 1.4rem;
        padding: 1rem;

    }

    .storyCards {
        width: 100%;
        min-height: 320px;
        padding: 1rem;
    }

    .storyCards label {
        font-size: 1.3rem;
    }

    .storyCard img {
        max-width: 180px;
    }
}
</style>
