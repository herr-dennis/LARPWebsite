<script setup>
import {useAuthStore} from "../stores/authStore.js";
import {computed, onMounted, ref} from "vue";
const authStore = useAuthStore();
const isLoggedIn = computed(() => authStore.isLoggedIn)
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const currentRubrik = ref("");
const currentTopic = ref("");
const errorWidow = ref(false);
const posts = ref([]);

    onMounted(async ()=>{


    const app = document.getElementById("app");
    currentRubrik.value = app.dataset.rubrikId;
    currentTopic.value = app.dataset.topicId;

    async  function getPosts(){
           ///api/rubrik/{rubic}/topics/{topic}/posts
       try{
           const response = await fetch(`/api/rubrik/${currentRubrik.value}/topics/${currentTopic.value}/posts`, {
               'X-CSRF-TOKEN': csrf,
               'Accept': 'application/json'
           });

           if(response.ok){
               return await response.json();
           }else {
               displayErrorWindow("Server Error");
           }

       }catch(error){
           displayErrorWindow("Network Error");
       }


    }

    function displayErrorWindow(error){
        errorWidow.value = true;
        const frame =  document.getElementById("errorWindow__p");
        if(frame!=null){
            frame.textContent = error;
        }
    }

      posts.value= await getPosts();
      console.log(posts.value);
});

function formatDate(timestamp) {
    const date = new Date(timestamp)
    return date.toLocaleString("de-DE")
}
</script>

<template>
    <div  v-if="errorWidow" class="errorWindow"> <p class="errorWindow__p"></p>  </div>
    <div class="ForumContainer" v-if="isLoggedIn">

    <div  class="ForumContainer__posts"    v-for="post in posts">
         <label class="ForumContainer__text" ></label> {{post.post_text}}
         <label  class="ForumContainer__author" >{{post.post_author}}</label>
        <label  class="ForumContainer__date">{{  formatDate(post.created_at) }}</label>
        <span class="ForumContainer__antwortPfeil" ></span>
    </div>
        <button class="ForumContainer__button" >Antworten</button>

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


.ForumContainer{
    width: min(900px, 70%);
    margin: 4rem auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ForumContainer__posts{
    position: relative;

    display: flex;
    flex-direction: column;
    gap: .5rem;

    padding: 1.2rem 1.4rem;

    background: rgba(20,20,20,0.65);   // dunkler
    border-left: 5px solid #8f1717;
    border-radius: 6px;

    backdrop-filter: blur(6px);

    color: #e7e2c6;

    box-shadow: 0 4px 18px rgba(0,0,0,0.45);

    transition: all 0.15s ease;
}

.ForumContainer__posts:hover{
    background: rgba(25,25,25,0.75);
    transform: translateX(3px);
}

.ForumContainer__text{
    font-size: 1rem;
    line-height: 1.45;
    color: #e8e3c9;
}

.ForumContainer__author{
    font-size: 0.85rem;
    color: #bfb996;
}

.ForumContainer__date{
    font-size: 0.75rem;
    color: #8e8972;
}

/* Antwort Pfeil */

.ForumContainer__antwortPfeil{
    position: absolute;
    right: 14px;
    bottom: 10px;

    width: 24px;
    height: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 18px;
    color: #8f1717;

    opacity: .75;
    transition: 0.2s;
}

.ForumContainer__antwortPfeil::before{
    content: "↩";
}

.ForumContainer__posts:hover .ForumContainer__antwortPfeil{
    opacity: 1;
    transform: translateX(-2px);
}
</style>
