<script setup>
//Normaler Import
import {useAuthStore} from "../stores/authStore.js";
import {computed, onMounted, ref} from "vue";
//Holen uns die Instanz
const authStore = useAuthStore();
//Greifen auf die Methode im authStore zu "Getter" um die User-Status zu holen!
//Das NavComp setzt erstellt die Instanz und fetched den User!
const isLoggedIn = computed(() => authStore.isLoggedIn)
const currentRubric = ref(null);
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const errorWidow = ref(false);
const topics = ref([]);
onMounted(async ()=>{
    const app = document.getElementById("app")
    currentRubric.value = app.dataset.topicId;


    async function getTopics(){

        try {

            const response = await fetch("/api/rubrik/"+currentRubric.value+"/topics", {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            });

            if(response.ok){
                return await response.json();
            }

        }catch(error){
         displayErrorWindow("Network Error");
        }

    }

     function  displayErrorWindow(error){
         errorWidow.value = true;
         const frame =  document.getElementById("errorWindow__p");
         if(frame!=null){
             frame.value = error;
         }

     }

     topics.value = await getTopics();
    console.log(topics);

});

function directionToPosts(topicID) {
    window.location.href = `/forum/rubrik/${currentRubric.value}/topics/${topicID}/posts`;
}


</script>

<template>
    <div  v-if="errorWidow" class="errorWindow"> <p class="errorWindow__p"></p>  </div>
    <div class="msgForum" v-if="!isLoggedIn"><p class="msgForum__p">Sie müssen eingeloggt sein, um das Forum zu benutzen</p>  </div>

    <div class="ForumHeader">
        <h2>Forum – Topics in der Rubrik {{currentRubric}}</h2>
    </div>
    <div v-if="isLoggedIn">
     <div class="ForumContainer">

         <button class="ForumContainer__button" >Neues Topic</button>
    <div v-for="topic in topics"  @click="directionToPosts(topic.id)"    class="ForumContainer__topics"   >
        <label  style="color: red">  {{topic.topic_name}} ({{topic.id}}) </label>
        <label>Verfasser:{{topic.topic_verfasser}}</label>

        <label class="forumNewRubric" >Neu</label>

    </div>
    </div>
    </div>
</template>

<style scoped lang="scss">

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

.ForumContainer__topics {
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

.ForumContainer__topics label:nth-child(2) {
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
