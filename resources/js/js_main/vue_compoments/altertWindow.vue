<script setup>
import {ref} from "vue";

const props = defineProps({
    open: Boolean
});

const emit = defineEmits(['close', 'confirm']);



</script>

<template>
    <div v-if="open" class="altertContainer">
        <p class="altertContainer__p">
            Möchtest du es wirklich löschen?
        </p>

        <button
            @click="emit('confirm')"
            class="altertContainer__buttonYes">
            Löschen
        </button>

        <button
            @click="emit('close')"
            class="altertContainer__buttonNo">
            Nein
        </button>
    </div>
</template>

<style scoped lang="scss">
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
</style>
