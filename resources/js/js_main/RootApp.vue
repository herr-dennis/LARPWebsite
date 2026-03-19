<script setup>
import { computed } from 'vue';
import NavComp from './vue_compoments/navComp.vue';
import { resolvePageComponent } from './pageResolver.js';

const props = defineProps({
    page: String
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const currentComponent = computed(() => resolvePageComponent(props.page));

const currentProps = computed(() => {
    if (props.page === 'donnerfels') {
        return {
            postUrl: '/api/ueber-uns/donnerfels',
            getUrl: '/api/ueber-uns/donnerfels'
        };
    }

    if (props.page === 'talagrad') {
        return {
            postUrl: '/api/ueber-uns/talagrad',
            getUrl: '/api/ueber-uns/talagrad'
        };
    }
    if (props.page === 'eckland') {
        return {
            postUrl: '/api/ueber-uns/eckland',
            getUrl: '/api/ueber-uns/eckland'
        };
    }


    return {};
});
</script>

<template>
    <div class="scrollToTop" @click="scrollToTop">
        ↑
    </div>

    <NavComp />

    <component
        :is="currentComponent"
        v-if="currentComponent"
        v-bind="currentProps"
    />
</template>
