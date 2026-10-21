<script setup>
const stories = ref([]);
import {ref, watch} from "vue";
const selectedRegion = ref('Donnerfels')
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            console.error(data.message || "Fehlgeschlagen");
            return;
        }

        stories.value = data;

    } catch (error) {
        console.error("Server nicht erreichbar");

    }
}

watch(selectedRegion, (neu, alt) => {
    console.log(`von ${alt} zu ${neu}`)
    // loadStories(neu)   // hier deine Funktion
})
</script>

<template>

     <p  class="defaultContainer">Welche Gliederung soll bearbeitet werden:</p>

    <div class="checkboxGroup">
        <label>
            <input type="radio" name="region" value="Donnerfels" v-model="selectedRegion">
            Donnerfels
        </label>

        <label>
            <input type="radio" name="region" value="Talagrad" v-model="selectedRegion">
            Talagrad
        </label>

        <label>
            <input type="radio" name="region" value="Eckland" v-model="selectedRegion">
            Eckland
        </label>
    </div>

    <p class="defaultContainer"  >Ausgewählt: {{ selectedRegion }}</p>


    <button  @click="getStories" >dhuawhndad</button>

</template>

<style scoped lang="scss">
.checkboxGroup {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 15px 0;

    label {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 8px 14px;
        border-radius: 10px;

        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(6px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);

        color: #fff;
        text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
        cursor: pointer;
        transition: all 0.2s ease;

        &:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        // ganzes Label rot hervorheben, wenn angehakt
        &:has(input:checked) {
            background: rgba(220, 41, 41, 0.35);
            box-shadow: 0 0 0 1px #dc2929, 0 4px 12px rgba(0, 0, 0, 0.3);
        }
    }

    input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;

        width: 18px;
        height: 18px;
        margin: 0;

        border: 2px solid rgba(255, 255, 255, 0.6);
        border-radius: 4px;
        background: transparent;
        cursor: pointer;

        display: grid;
        place-content: center;
        transition: all 0.2s ease;

        &::after {
            content: "";
            width: 10px;
            height: 10px;
            border-radius: 2px;
            background: #fff;
            transform: scale(0);
            transition: transform 0.15s ease;
        }

        &:checked {
            background: #dc2929;
            border-color: #dc2929;

            &::after {
                transform: scale(1);
            }
        }

        &:focus-visible {
            outline: 2px solid #dc2929;
            outline-offset: 2px;
        }
    }
}
</style>
