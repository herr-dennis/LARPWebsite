<script setup>




import gallery from "./../../images/medien/liedgut.png"
import bilder from "./../../images/medien/bilder.png"
import theater from "./../../images/medien/theater.png"
function  diretionToURL(url){
    window.location.href = url;
}

</script>

<template>

    <div class="storyCardsWrapper">
        <div class="storyCards">
            <label>Liedgut</label>
            <div @click="diretionToURL('/medien/liedgut')"  class="storyCard" >
                <img :src="gallery" alt="Bild"> >
            </div>
        </div>

        <div class="storyCards">
            <label>Gallery</label>
            <div @click="diretionToURL('/medien/gallery')"   class="storyCard" >
                <img :src="bilder" alt="Bild"> >
            </div>
        </div>
        <div class="storyCards">
            <label>Videos</label>
            <div  @click="diretionToURL('/medien/videos')"  class="storyCard" >
                <img :src="theater" alt="Bild"> >
            </div>
        </div>
    </div>


</template>

<style scoped lang="scss">


.storyCardsWrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    width: 100%;
    padding: 2rem 1rem;
    box-sizing: border-box;
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
