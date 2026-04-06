<script setup>

import {ref} from "vue";

const name = ref("");
const email =ref("");
const nachricht = ref("");
const checked = ref(false);
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')


   async function handleSubmit(){


      const formData = {
          name: name.value,
          email: email.value,
          message: nachricht.value
      };

      const response = await fetch("/api/ueber-uns/kontakt",{
          method: "POST",
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json'
          },
          body: JSON.stringify(formData)
      })



      if(!response.ok){
          alert("Fehler.")
      }

      const data = await response.json();

      console.log(data);


  }


</script>

<template>



        <div class="forms-wrapper">
            <div class="Formcontainer"> >
            <form  @submit.prevent="handleSubmit">

                <div class="form-group">
                    <label class="FormDefaultContainer__Label"  >Ihr Name</label>
                    <input  class="FormDefaultContainer__Input"  type="text" v-model="name" />
                </div>

                <div class="form-group">
                <label  class="FormDefaultContainer__Label" >Ihre E-Mail</label>
                <input  class="FormDefaultContainer__Input" v-model="email"  type="email" >
                </div>

                 <div class="form-group">
                     <label>Ihre Nachricht</label>
                     <textarea class="FormDefaultContainer__TextAreaInput" v-model="nachricht" placeholder="Ihre Nachricht an uns" ></textarea>
                 </div>

                <div style="position:absolute; left:-9999px;">
                    <label>Bitte nicht ausfüllen</label>
                    <input type="text" name="website">
                </div>
                <input type="hidden" name="Angeben">

                <div class="form-group">
                    <label>Stimme der Datenschutzerklärung zu</label>
                <input  type="checkbox" required   v-model="checked" />

                    <input type="submit" class="FormDefaultContainer__Submit" />

                </div>
            </form>
        </div>
    </div>


    <div class="trennlinie"></div>


</template>

<style scoped lang="scss">
@use "../../../../resources/css/css_main/defaultForm.scss";
@use "../../../../resources/css/css_main/defaultButton";
</style>
