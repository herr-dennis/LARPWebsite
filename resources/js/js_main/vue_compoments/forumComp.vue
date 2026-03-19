<script setup>

import {computed, onMounted, ref} from 'vue'
import {useAuthStore} from "../stores/authStore.js";

const authStore = useAuthStore()
const isLoggedIn = computed(() => authStore.isLoggedIn)
const authState = computed(() => {authStore.authState})
const msg = ref("");
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const rubrics = ref([]);
const wantInsertRubric= ref(false);
const msgInsertRubrik=ref("");


const showAlertDialog = ref(false);
const rubricToDelete = ref(null)

 async function getRubric (){

    try{
        const response = await fetch("/api/forum/rubrik",{
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        });

        if(response.ok){
            return await response.json();
        }

    }catch(error){
        msg.value="Network Error"
    }

};


 onMounted(async ()=>{
     rubrics.value = await getRubric();
 });

function moveToTopic(rubrik){
    window.location.href = "/forum/rubrik/"+rubrik+"/topics";
}

function showFormInsertRubric(){

    if(!wantInsertRubric.value){
        wantInsertRubric.value = true;
    }else {
        wantInsertRubric.value = false;
    }

}

async function insertRubrik(rubrik){

    try{
        const user = authStore.user.name;

    const response = await fetch("/api/forum/rubrik", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrf,
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            rubrik: rubrik,
            verfasser:user
        })
    });

    const data = await response.json();

    if(response.ok){
        msgInsertRubrik.value = "Erfolgreich eingefügt";
        rubrics.value= data;
    }else{
        msgInsertRubrik.value = data.message ?? "Fehler beim Einfügen";
    }
    }catch(error){
        msgInsertRubrik.value="Network Error"+error;
    }



}


function HandleDeleteRubric(id){
    rubricToDelete.value = id
    showAlertDialog.value = true
}
function toggleAlertDialog(){
    if(!showAlertDialog.value){
        showAlertDialog.value = true;
    }else {
        showAlertDialog.value = false;
    }
}

function confirmDelete(){
    deleteRubrik(rubricToDelete.value)
    showAlertDialog.value = false
}

async function deleteRubrik(id){

    const response =  await fetch(`/api/forum/rubrik/${id}`,{
        method: "DELETE",
        headers:{
            "X-CSRF-TOKEN": csrf,
            "Accept":"application/json"
        }
    })


    if(!response.ok){
        console.log(response.message)
    }

    const data = await response.json();
    console.log(data.message);

}


function HandleInsertRubrik(){
      const dataInput = document.getElementById("inputFormRubrik");
      const rubrik = dataInput.value;
      insertRubrik(rubrik);


}




</script>

<template>

    <div class="msgForum" v-if="!isLoggedIn"><p class="msgForum__p">Sie müssen eingeloggt sein, um das Forum zu benutzen</p>  </div>
    <div v-if="isLoggedIn">
    <div class="ForumHeader">
        <h2>Forum – Rubriken</h2>
    </div>
    <div  class="ForumContainer">
        <button  @click="showFormInsertRubric()" class="ForumContainer__button">Neue Rubrik</button>

        <div v-for="rubric in rubrics"  @click="moveToTopic(rubric.id)"  class="ForumContainer__topics">
            <label>{{rubric.rubric_name}} ({{rubric.id}})</label> <button @click.stop="HandleDeleteRubric(rubric.id)"   class="ForumContainer__delete" >X</button>
            <label>Verfasser: {{rubric.rubric_verfasser}}</label>
            <label class="forumNewRubric" v-if="rubric.rubric_status" >NEU!</label>

        </div>

    </div>

    </div>

    <div class="FormRubricContainer" v-if="isLoggedIn&&wantInsertRubric" >
        <form class="FormRubricContainer__Form" @submit.prevent="HandleInsertRubrik()">
            <label class="FormRubricContainer__Label"  >Rubrik</label>
            <input id="inputFormRubrik"  class="FormRubricContainer__Input" type="text" placeholder="Rubrik"/>
            <button  class="FormRubricContainer__Button"  > Einfügen </button>
            <button @click="showFormInsertRubric()" class="FormRubricContainer__Button" >Schließen</button>
            <label class="FormRubricContainer__Msg">{{msgInsertRubrik}}</label>
        </form>
    </div>


    <div v-if="showAlertDialog" class="altertContainer">

        <p class="altertContainer__p">
            Möchtest du es wirklich löschen?
        </p>

        <button
            @click="confirmDelete"
            class="altertContainer__buttonYes">
            Löschen
        </button>

        <button
            @click="toggleAlertDialog"
            class="altertContainer__buttonNo">
            Nein
        </button>

    </div>


</template>

<style scoped lang="scss">

/*  Formular  */

.FormRubricContainer{
    position: fixed;
    inset: 0;
    z-index: 3000;

    display: flex;
    justify-content: center;
    align-items: center;

    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(4px);
}
.ForumContainer__delete{
    position: absolute;
    top: 6px;
    right: 8px;

    background: transparent;
    border: none;
    color: #c5b98f;
    font-size: 16px;
    font-weight: bold;
}

.ForumContainer__delete:hover{
    color: #ff3b3b;
}

.FormRubricContainer__Form{
    width: min(70vw, 700px);
    padding: 2rem 1.5rem;

    display: flex;
    flex-direction: column;
    gap: 1rem;

    background: rgba(20,20,20,0.94);
    border: 1px solid rgba(242,234,208,0.15);
    border-left: 4px solid #8b1e1e;
    border-radius: 12px;

    box-shadow:
        0 12px 40px rgba(0,0,0,0.75),
        inset 0 1px 0 rgba(255,255,255,0.04);

    color: #f2ead0;
}

.FormRubricContainer__Label{
    font-size: 1.1rem;
    font-weight: 700;
    color: #f2ead0;
    letter-spacing: 0.03em;
}

.FormRubricContainer__Input{
    width: 75%;
    padding: 0.9rem 1rem;

    background: rgba(245,239,219,0.06);
    color: #f2ead0;

    border: 1px solid rgba(242,234,208,0.18);
    border-radius: 8px;
    outline: none;

    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.FormRubricContainer__Input::placeholder{
    color: rgba(242,234,208,0.55);
}

.FormRubricContainer__Input:focus{
    border-color: rgba(165,42,42,0.8);
    box-shadow: 0 0 0 3px rgba(165,42,42,0.18);
    background: rgba(245,239,219,0.08);
}

.FormRubricContainer__Button{
    padding: 0.85rem 1rem;
    background: rgba(245,239,219,0.08);
    color: #f2ead0;
    width: 80%;
    border: 1px solid rgba(242,234,208,0.18);
    border-left: 4px solid #a52a2a;
    border-radius: 8px;

    font-size: 1rem;
    font-weight: 600;

    transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.FormRubricContainer__Button:hover{
    cursor: pointer;
    background: rgba(245,239,219,0.14);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.45);
}

.FormRubricContainer__Msg{
    min-height: 1.2rem;
    color: #d8cfae;
    font-size: 0.95rem;
}



/* Forum Container */

.ForumContainer {
    width: min(900px, 70%);
    margin: 10rem auto 0 auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: rgba(18,18,18,0.82);
    border-radius: 0.8rem;
    box-shadow: 0 0 28px rgba(0,0,0,0.45);
    border: 1px solid rgba(255,255,255,0.04);
    backdrop-filter: blur(2px);
}

/* Forum Titel */

.ForumHeader {
    text-align: center;
    margin-bottom: 1rem;
}

.ForumHeader h2 {
    color: #f2ead0;
    font-weight: 600;
    letter-spacing: 0.08rem;
    font-size: 1.35rem;
    margin-bottom: 0.6rem;
}

.ForumHeader::after {
    content: "";
    display: block;
    width: 80%;
    height: 1px;
    margin: 0.5rem auto 0 auto;
    background: linear-gradient(
            to right,
            transparent,
            rgba(255,255,255,0.25),
            transparent
    );
}

/* Rubrik Box */

.ForumContainer__button{
    margin: 6px auto;
    padding: 14px 20px;

    width: 50%;

    background: rgba(30,30,30,0.65);
    backdrop-filter: blur(4px);

    color: #f2ead0;

    border: 1px solid rgba(245,239,219,0.25);
    border-left: 4px solid #a52a2a;

    border-radius: 8px;

    box-shadow:
        0 4px 12px rgba(0,0,0,0.6),
        inset 0 1px 0 rgba(255,255,255,0.05);

    transition:
        transform 0.25s ease,
        background 0.25s ease,
        box-shadow 0.25s ease;
}

.ForumContainer__button:hover{

    background: rgba(40,40,40,0.8);

    transform: translateY(-2px);

    box-shadow:
        0 6px 18px rgba(0,0,0,0.8),
        0 0 8px rgba(165,42,42,0.4);

    color: #ffffff;

    cursor: pointer;
}


.ForumContainer__topics {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    padding: 1rem 1.2rem;
    background: rgba(245,239,219,0.08);
    border-left: 5px solid #8f1717;
    border-radius: 0.45rem;
    cursor: pointer;
    transition: transform 0.18s ease,
    background 0.18s ease,
    box-shadow 0.18s ease;
    box-shadow: 0 0 10px rgba(0,0,0,0.18);
}

.ForumContainer__topics:hover {
    background: rgba(245,239,219,0.13);
    transform: translateY(-2px);
    box-shadow: 0 0 16px rgba(0,0,0,0.28);
}

/* Titel */

.ForumContainer__topics label:first-child {
    font-size: 1.15rem;
    font-weight: 700;
    color: #f2ead0;
    letter-spacing: 0.02rem;
}

/* Verfasser */

.ForumContainer__topics label:nth-child(3) {
    font-size: 0.95rem;
    color: #cfc6aa;
}

/* NEU Badge */

.forumNewRubric {
    width: fit-content;
    margin-top: 0.35rem;
    padding: 0.25rem 0.65rem;
    background: #9f1919;
    color: #f7f1de;
    font-size: 0.8rem;
    font-weight: 700;
    border-radius: 0.3rem;
    letter-spacing: 0.04rem;
    box-shadow: 0 0 8px rgba(120,10,10,0.35);
}
.altertContainer{
    position: fixed;
    inset: 0;

    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    gap: 1rem;

    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(3px);

    z-index: 2000;
}

.altertContainer__p{
    color: #f2ead0;
    font-size: 1.1rem;
}

.altertContainer__buttonYes,
.altertContainer__buttonNo{
    padding: 0.5rem 1.2rem;

    border: none;
    border-radius: 6px;

    background: rgba(245,239,219,0.08);
    color: #f2ead0;

    cursor: pointer;
}

.altertContainer__buttonYes:hover{
    background: #8f1717;
}

.altertContainer__buttonNo:hover{
    background: rgba(245,239,219,0.18);
}
/* Responsive */

@media (max-width: 768px) {

    .ForumContainer {
        width: 92%;
        margin-top: 8rem;
        padding: 1rem;
    }

    .ForumContainer__topics {
        padding: 0.9rem 1rem;
    }

    .ForumContainer__topics label:first-child {
        font-size: 1rem;
    }

    .ForumContainer__topics label:nth-child(2) {
        font-size: 0.88rem;
    }

    .defaultContainer {
        width: 90%;
        margin-top: 9rem;
        text-align: center;
    }
}
</style>
