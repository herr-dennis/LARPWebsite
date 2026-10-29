<script setup>
const stories = ref([]);
import {onMounted, ref, watch} from "vue";
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
    },

});

function getUrl(choose) {
    if (choose === "Donnerfels") {
        return '/api/ueber-uns/donnerfels';
    } else if (choose === "Eckland") {
        return '/api/ueber-uns/eckland';
    } else if (choose === "Talagrad") {
        return '/api/ueber-uns/talagrad';
    }

    return '';
}

async function getStories() {
    try {
        const response = await fetch(getUrl(selectedRegion.value), {
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
        props.story = data;

    } catch (error) {
        console.error("Server nicht erreichbar"+error);

    }
}


async function handleMove(pos, direction) {
    let db =  selectedRegion.value.toLowerCase();

    const res = await fetch('/api/stories/move', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify({pos, direction ,db })
    })

    if (!res.ok) {
        alert("Fehler API Call")
        return
    }

    stories.value = await res.json()
}


async function handleTestClick(){
    await getStories();
}

watch(selectedRegion, (neu, alt) => {
   getStories(neu);
})

onMounted(()=>{
    getStories();
});





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



    <div class="listContainer">

        <div class="listContainer-row" v-for="(story, index) in stories" :key="story.id" >
            <div  class="listItem">
                {{ "Datenbank-ID: " + story.id }} -- {{ "Title: " + (story.title ?? "Nicht vorhanden") }}
                <br>
                <hr>
                <div class="listContainer__changePos" >
                    Position: {{story.position}}
                    <div class="changPos_Up" >
                        <svg  @click="handleMove(story.position, 'up')"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75 12 3m0 0 3.75 3.75M12 3v18" />
                        </svg>

                    </div>
                    <div class="changPos_Down">
                        <svg  @click="handleMove(story.position, 'down')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                        </svg>

                    </div>


                </div>


            </div>
            <div class="listContainer-arrow" >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25" />
                </svg>

            </div>
        </div>

    </div>

</template>

<style scoped lang="scss">
.listContainer {
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    color: #fff;

    display: flex;
    flex-direction: column;
    gap: 12px;

    .listContainer-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .listItem {
        flex: 1;
        padding: 12px 15px;
        border-radius: 12px;

        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(6px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);

        hr {
            margin: 8px 0;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
    }

    .listContainer-arrow {
        width: 28px;
        flex-shrink: 0;
        opacity: 0.5;
    }

    .changPos_Up,
    .changPos_Down {
        width: 36px;
        padding: 4px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.2s ease;

        svg {
            display: block;
            width: 100%;
            cursor: pointer;
        }

        &:hover {
            background: #dc2929;
        }

        &:active {
            transform: scale(0.92);
        }
    }

    .changPos_Up {
        margin-left: auto;   // schiebt beide Pfeile nach rechts
    }
}

.listContainer__changePos {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}

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

        &:has(input:checked) {
            background: rgba(220, 41, 41, 0.35);
            box-shadow: 0 0 0 1px #dc2929, 0 4px 12px rgba(0, 0, 0, 0.3);
        }
    }

    input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;

        width: 18px;
        height: 18px;
        margin: 0;

        border: 2px solid rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        background: transparent;
        cursor: pointer;

        display: grid;
        place-content: center;
        transition: all 0.2s ease;

        &::after {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
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
