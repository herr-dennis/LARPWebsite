<script setup>

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
import {useAuthStore} from '../stores/authStore.js'
import {computed, onMounted, ref} from "vue";

const authStore = useAuthStore();
const admin = computed( ()=> authStore.adminState)
const isLoggedIn = computed( ()=> authStore.isLoggedIn)
const pioniers = ref([]);
const showInsertDialog = ref(false);
const name = ref("Geheim")
const rang = ref("Geheim")
const geburtstag = ref("Geheim")
const text = ref("Geheim")
const waffen = ref("Geheim")
const dienstjahre = ref("Geheim")
const image = ref(null)
const previewUrl = ref(null)

 async function getPioniere(){

     const response = await fetch("/api/ueber-uns/pioniere", {
         headers:  {"Accept": "application/json"}
         })

       const data = await response.json();

     console.log(data)

     pioniers.value = data;


}

function toggleInsetCharWindow(){

     if(showInsertDialog.value){
         showInsertDialog.value = false;
     }else {
         showInsertDialog.value = true;
     }

}

 onMounted( async ()=>{
     await getPioniere();
 });

async function insertPioniere(){

    const formData = new FormData();

    formData.append("name", name.value);
    formData.append("rang", rang.value);
    formData.append("geburtstag", geburtstag.value);
    formData.append("text", text.value);
    formData.append("waffen", waffen.value);
    formData.append("dienstjahre", dienstjahre.value);
    formData.append("image", image.value);



    const response = await fetch("/api/ueber-uns/pioniere", {
        method: "POST",
        headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: formData
    })
    if(!response.ok){
        alert("Fehler")
    }
    pioniers.value = await response.json();
}


addEventListener("keydown", (e) => {
    if (e.key === "Escape" && showInsertDialog.value) {
        toggleInsetCharWindow();
    }
});

function handleInsertClick(){

    insertPioniere();
   toggleInsetCharWindow();
}

function handleFileChange(event) {
    const file = event.target.files[0]
    image.value = file

    if (file) {
        previewUrl.value = URL.createObjectURL(file)
    }
}

async function deletePioniere(id){

    const response = await fetch("/api/ueber-uns/pioniere/"+id, {
        method: "DELETE",
        headers:
            {"Accept": "application/json",
             "X-CSRF-TOKEN": csrf,}

    });

    if(!response){
        alert("Fehler");
    }
    pioniers.value=await response.json();
}

function handleRemoveClick(id){

    console.log("handleRemoveClick", id);
    deletePioniere(id);
}

</script>

<template>

    <div class="steckbriefWrapper"  >
        <div  v-for="pionier in pioniers" :key="pionier.id" class="steckbriefContainer">
            <div class="trennlinie"></div>
            <button class="steckbriefContainer__button"  @click="handleRemoveClick(pionier.id)" >X</button>

            <p class="steckbriefContainer__p"  >Quartett {{pionier.name}}</p>
            <img loading="lazy" :src="'/storage/'+pionier.image" alt="SteckbriefLogo" >

            <label class="steckbriefContainer__label"  >Bewaffnung:</label>
            <p class="steckbriefContainer__p"  >{{pionier.waffen}}</p>
            <label class="steckbriefContainer__label" >Rang</label>
            <p  class="steckbriefContainer__p" >{{pionier.rang}}</p>
            <label class="steckbriefContainer__label" >Dienstjahre:</label>
            <p class="steckbriefContainer__p" >{{pionier.dienstjahre}}</p>
            <label class="steckbriefContainer__label" >Geburtstag:</label>
            <p class="steckbriefContainer__p" >{{pionier.geburtstag}}</p>
            <label class="steckbriefContainer__label" >Beschreibung</label>
            <blockquote class="steckbriefContainer__blockquote" >{{pionier.text}}</blockquote>

        </div>

    </div>

    <div v-show="isLoggedIn && admin "  class="plus-insert-container" >
            <button  @click="toggleInsetCharWindow"  class="plus-insert-container__button" >    + </button>
    </div>


    <div     v-show="showInsertDialog" class="FormDefaultContainer" >
        <form @submit.prevent="handleInsertClick" class="FormDefaultContainer__Form">

            <label class="FormDefaultContainer__Label">Name</label>
            <input v-model="name" class="FormDefaultContainer__Input" type="text" placeholder="Name" />

            <label class="FormDefaultContainer__Label">Rang</label>
            <input v-model="rang" class="FormDefaultContainer__Input"  placeholder="Wenn kein Geheimnis"  type="text" />

            <label class="FormDefaultContainer__Label">Dienstjahre</label>
            <input v-model="dienstjahre" class="FormDefaultContainer__Input"   placeholder="Wenn kein Geheimnis"  type="text" />

            <label class="FormDefaultContainer__Label">Waffen</label>
            <input v-model="waffen" class="FormDefaultContainer__Input"  placeholder="Wenn kein Geheimnis"  type="text" />

            <label class="FormDefaultContainer__Label">Geburtstag</label>
            <input v-model="geburtstag" class="FormDefaultContainer__Input"  placeholder="Wenn kein Geheimnis"  type="text" />

            <label class="FormDefaultContainer__Label">Beschreibung</label>
            <textarea v-model="text" class="FormDefaultContainer__TextAreaInput"></textarea>
            <input required  type="file" @change="handleFileChange">
            <img  v-if="previewUrl" :src="previewUrl" />
            <button type="submit" class="FormDefaultContainer__Button">
                Einfügen
            </button>

            <button @click="toggleInsetCharWindow" type="button" class="FormDefaultContainer__delete">
                Schließen
            </button>

        </form>
    </div>

</template>
<style scoped lang="scss">

@import '../../../../resources/css/css_main/defaultButton';
@import '../../../../resources/css/css_main/defaultForm';
@import '../../../../resources/css/css_main/plus_insert_btn';

.steckbriefWrapper{
    display: flex;
    flex-wrap: wrap;
    gap:40px;
    justify-content: center;
    padding: 40px 20px 80px;
}
/* einzelne Karte */
.steckbriefContainer{
    position: relative;
    width: 450px;
    background: rgba(26, 28, 28, 0.5);
    padding: 16px;
    font-family: "Cinzel", cursive;
    min-height:640px;

    background:
        linear-gradient(
                180deg,
                rgba(32, 34, 36, 0.88) 0%,
                rgba(20, 21, 23, 0.92) 55%,
                rgba(12, 12, 12, 0.95) 100%
        );

    border: 1px solid rgba(150, 130, 90, 0.55);

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.06),
        inset 0 -8px 30px rgba(0,0,0,0.55),
        0 12px 30px rgba(0,0,0,0.45),
        0 0 0 3px rgba(25,25,25,0.6);

    backdrop-filter: blur(5px);
    overflow: hidden;
    color: #ddd4c5;

}
/* Titel oben */
.steckbriefContainer__p:first-of-type {
    margin: 0 0 18px 0;
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: #f0e7d2;
    text-shadow:
        0 1px 0 #000,
        0 0 8px rgba(255,255,255,0.05);
    position: relative;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(170, 145, 88, 0.22);
}
.steckbriefContainer__label{
    font-size: 12px;
    display: block;
    margin-top: 5px;
}

/* Werte */
.steckbriefContainer__p {
    margin: 0;
    font-size: 1rem;
    line-height: 1.45;
    color: #e3dccf;
}


.steckbriefContainer img{
    width: 100%;
    height: 460px;
    object-fit: cover;
    border: 2px solid #5e1b1b;
    margin-bottom: 12px;


}
.steckbriefContainer__label {
    display: block;
    margin-top: 12px;
    margin-bottom: 4px;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #b79b63;
    text-shadow: 0 1px 0 #000;
}

/* leichtes Ornament  */
.steckbriefContainer::after{
    content:"";
    position: absolute;
    top: 0;
    left: -20%;
    width: 70%;
    height: 100%;

    background: linear-gradient(
            115deg,
            rgba(255, 255, 255, 0.05) 0% ,
            rgba(255, 255, 255, 0.015) 35% ,
            rgba(255,255,255,0.0) 70%
      );

}
/* Beschreibung */
.steckbriefContainer__blockquote {
    margin: 14px 0 0 0;
    padding: 12px 14px;
    border-left: 3px solid rgba(169, 132, 70, 0.75);
    border-radius: 6px;
    background: rgba(255,255,255,0.03);
    color: #d7cfbf;
    font-style: italic;
    line-height: 1.55;
    box-shadow: inset 0 0 12px rgba(0,0,0,0.25);
}


.steckbriefContainer::before {
    content: "";
    position: absolute;
    inset: 10px;
    border-radius: 12px;
    border: 1px solid rgba(185, 158, 103, 0.25);
    pointer-events: none;
    box-shadow:
        inset 0 0 12px rgba(201, 169, 92, 0.06),
        0 0 10px rgba(0,0,0,0.2);
}

.steckbriefContainer__button{
    position: relative;
    z-index: 11;
}

</style>
