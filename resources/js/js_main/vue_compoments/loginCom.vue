


<template>
    <div   v-if="showLogin"  class="forms-wrapper">
    <div class="FormcontainerLogin">
        <form class="Form" @submit.prevent="login">
            <input type="hidden" name="_token" :value="csrf">

            <div class="form-group">
            <label>Email</label>
            <input type="text" v-model="loginName" placeholder="Ihre E-Mail" required />
            </div>

            <div class="form-group">
            <label>Passwort</label>
            <input type="password" v-model="loginPassword" placeholder="Ihr Passwort" required />
                <button type="submit">Login</button>
            </div>
            <div class="msgContainer" >
                <p v-if="msgLogin">{{ msgLogin }}</p>
            </div>
            <div>
                <br>
            </div>
            <div class="form-group">
                <button type="button" @click="showRegis">Registrieren</button>
            </div>
        </form>
    </div>
    </div>


    <div  v-if="showRegister" class="forms-wrapper">
    <div   class="FormContainerRegistierung">
        <form @submit.prevent="regis">
            <input type="hidden" name="_token" :value="csrf">

            <div class="form-group">
            <label>Name</label>
            <input type="text" v-model="name" required />
            </div>

            <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="email" required />
            </div>

            <div class="form-group">
            <label>Passwort</label>
            <input type="password" v-model="password" required />
            </div>

            <div class="form-group">
            <label>Passwort wiederholen</label>
            <input type="password" v-model="password_confirmation" required />
            </div>

            <div class="form-group form-group--checkbox" >
                <label>Ich akzeptiere die Datenschutzerklärung</label>
                <input type="checkbox" v-model="isChecked" />
            </div>


            <button :disabled="!isChecked" type="submit">Registrieren</button>

            <div class="msgContainer" >
                <p v-if="msg">{{ msg }}</p>
            </div>
            <br>
            <br>
            <div class="form-group">
                <button type="button"  @click="showRegis">Zum Login</button>
            </div>




        </form>
    </div>
        </div>

    <div class="trennlinie"></div>

    <<div class="defaultContainer">
    <p>
        Durch die Registrierung erhalten Sie Zugriff auf das Forum.
        Mit der Registrierung stimmen Sie außerdem den allgemeinen Nutzerregeln der LARP-Gruppe zu.
        Den Link dazu finden Sie hier:
    </p>
    <div class="defaultContainer__a">
        <a href="#">Nutzerregeln</a>
    </div>

</div>


</template>

<script setup>
import {ref} from "vue";

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const showRegister = ref(false)
const showLogin = ref(true)
const msg = ref("");
const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const msgLogin = ref("");
const isChecked = ref(false);
//login v-variablen
const loginName = ref("");
const loginPassword = ref("");

import { useAuthStore } from '../stores/authStore.js'

const authStore = useAuthStore();


function showRegis() {

    if(showLogin.value){
        showRegister.value = true
        showLogin.value = false
    }else {
        showRegister.value = false
        showLogin.value = true
    }
}


async function regis() {

        const formdata = {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        };


    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formdata)
        });

        const data = await response.json();

        console.log(data);

        if (!response.ok) {
            msg.value = data.message || 'Registrierung fehlgeschlagen';
            return;
        }

        msg.value = data.message || 'Registrierung erfolgreich';
    } catch (error) {
        //console.error(error);
        msg.value = 'Netzwerkfehler';
    }
}

async function login() {
    const formdataLogin = {
       email: loginName.value,
        password: loginPassword.value,
    }

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formdataLogin)
        });

        const data = await response.json();

        if (!response.ok) {
            msgLogin.value = data.message || "Fehlgeschlagen";
            return;
        }

        if (data.user) {
            authStore.setUser(data.user);
        }

        window.location.href = "/";

    } catch (error) {
        msgLogin.value = "Netzwerkfehler";
        console.error(error);
    }
}



</script>


<style scoped lang="scss">

.msgContainer{
    color: #eee;
}


.FormcontainerLogin,
.FormContainerRegistierung {
    display: flex;
    flex-direction: column;
    gap: 1rem;

    background: rgba(20, 20, 20, 0.75);
    backdrop-filter: blur(4px);
    padding: 2rem;
    border-radius: 8px;
    width: 100%;
    max-width: 400px;

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);

    label {
        font-size: 0.9rem;
        color: #ddd;
        margin-bottom: 0.2rem;
    }

    input {
        padding: 0.6rem 0.8rem;
        border-radius: 4px;
        border: 1px solid #555;
        background: #2a2a2a;
        color: #eee;
        font-size: 0.95rem;

        &:focus {
            outline: none;
            border-color: #a32020;
            box-shadow: 0 0 5px rgba(163, 32, 32, 0.6);
        }
    }

    button,
    input[type="submit"] {
        margin-top: 1rem;
        padding: 0.7rem;
        background: #7a1c1c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.2s ease;

        &:hover {
            background: #a32020;
        }
    }
}
.form-group {
    display: flex;
    flex-direction: column; // <-- DAS ist der entscheidende Punkt
    gap: 0.4rem;
}

/* Wrapper für beide Formulare */
.forms-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 3rem;
    padding: 4rem 2rem;
    flex-wrap: wrap; // wichtig für mobile
}

/* Checkbox-Formgruppe */
.form-group--checkbox {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.6rem;

    input[type="checkbox"] {
        width: auto;
        margin: 0;
        padding: 0;
        accent-color: #a32020;
        cursor: pointer;
    }

    label {
        margin: 0;
        cursor: pointer;
        color: #ddd;
        font-size: 0.9rem;
    }
}
/* Mobile Anpassung */
@media (max-width: 768px) {
    .FormcontainerLogin,
    .FormContainerRegistierung {
        max-width: 77%;
    }

    .forms-wrapper {
        flex-direction: column;
        align-items: center;
    }
}
</style>
