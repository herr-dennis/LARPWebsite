<script setup>
import {useAuthStore} from '../stores/authStore.js'
import {computed, onMounted, ref} from "vue";
import UserVerwaltung from './admin_sub_comps/UserVerwaltung.vue'
import StoryVerwaltung from './admin_sub_comps/StoryVerwaltung.vue'

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

const authStore = useAuthStore();
const admin = computed(() => authStore.adminState)
const isLoggedIn = computed(() => authStore.isLoggedIn)
const users = ref([]);
const stories = ref([]);
const activeView = ref('users')   // neu: welche Ansicht ist aktiv

async function getUsers(){

    const response = await fetch("/api/users",{
        method: "GET",
        headers:{
            "Content-Type": "application/json",
            "Accept": "application/json",
            'X-CSRF-TOKEN': csrf,
        }
    });

    if(!response.ok){
        alert("Fehler API Call");
    }

    const data = await response.json();
    users.value=data;

}

onMounted(()=>{
    getUsers();
});

function updateRole(id, role) {
    fetch(`/api/users/${id}/role`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Accept": "application/json"
        },
        body: JSON.stringify({
            role: role
        })
    })
        .then(res => res.json())
        .then(data => users.value=data);
}

function deleteUser(id) {

    if (!confirm("User wirklich löschen?")) return;

    fetch(`/api/users/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Accept": "application/json"
        }
    })
        .then(res => res.json())
        .then(data => {
            console.log(data);

            users.value=data;
        });

}

</script>

<template>
    <div   class ="tabs">
        <button  class="tabBtn"  :class="{ active: activeView === 'users' }" @click="activeView = 'users'">
            User Verwaltung
        </button>
        <button  class="tabBtn"  :class="{ active: activeView === 'stories' }" @click="activeView = 'stories'">
            Story Verwaltung
        </button>
    </div>

    <hr>

    <div v-if="isLoggedIn">
        <UserVerwaltung
            v-if="activeView === 'users'"
            :users="users"
            @update-role="updateRole"
            @delete-user="deleteUser"
        />
        <StoryVerwaltung v-else-if="activeView === 'stories'"/>
    </div>
</template>

<style scoped lang="scss">
.userContainer {
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    padding: 15px;

    display: flex;
    flex-direction: column;
    gap: 12px;
}

.userContainer > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;

    padding: 12px 15px;
    border-radius: 12px;

    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* Name */
.userContainer label {
    color: #fff;
    font-size: 0.95rem;
    text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
}

/* Select */
.userContainer select {
    padding: 6px 10px;
    border-radius: 8px;
    border: none;

    background: rgba(255, 255, 255, 0.1);
    color: #dc2929;

    outline: none;
    cursor: pointer;
}

/* Button */
.userContainer button {
    padding: 6px 12px;
    border-radius: 8px;
    border: none;

    background: rgba(255, 255, 255, 0.15);
    color: #fff;

    cursor: pointer;
    transition: all 0.2s ease;
}

.userContainer button:hover {
    background: rgba(255, 255, 255, 0.3);
}

@media (max-width: 600px) {
    .userContainer > div {
        flex-direction: column;
        align-items: stretch;
    }

    .userContainer select,
    .userContainer button {
        width: 100%;
    }
}
.tabBtn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    cursor: pointer;
    transition: all 0.2s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    &.active {
        background: #dc2929;
    }
}
</style>
