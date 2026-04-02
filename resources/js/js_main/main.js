
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import RootApp from './RootApp.vue'

const el = document.getElementById('app')
const pinia = createPinia()

const app = createApp(RootApp, {
    page: el.dataset.page || 'default'
})

app.use(pinia)
app.mount('#app')









/*
import { createApp } from "vue";
import NavComp from './vue_compoments/navComp.vue';
import { createPinia } from 'pinia'

const pinia = createPinia();
createApp(NavComp).use(pinia).mount('#nav');

// Alle .vue-mount-Elemente auf der Seite holen
const mounts = document.querySelectorAll('.vue-mount'); // <-- wichtig: querySelectorAll!


// 3️⃣ Map für lazy Imports (kannst du später erweitern)
const registry = {
   Login: () => import('./vue_compoments/loginCom.vue'),
    Gallery:  ()=>import('./vue_compoments/galleryComp.vue'),
    News: () => import('./vue_compoments/newFeedComp.vue'),
    StoryCards: () => import('./vue_compoments/storyCardsComp.vue'),
    HandwerkCards: () => import('./vue_compoments/handwerkCardsComp.vue'),
    // Consent: () => import('./vueCompoments/ConsentComp.vue'),
    // usw.
};

// 4️⃣ Über alle Mount-Punkte iterieren
mounts.forEach(async (el) => {
    // Holt den Namen der Komponente aus dem data-Attribut (z. B. data-component="NewsFeed")
    const name = el.dataset.component;

    // Falls kein Name oder keine passende Komponente im Registry-Objekt existiert → abbrechen
    if (!name || !registry[name]) return;

    // Dynamisch importieren (Code-Splitting)
    // Lädt die Komponente erst, wenn sie wirklich auf der Seite gebraucht wird
    const { default: Comp } = await registry[name]();

    // Datenattribute aus dem HTML-Element holen
    // Erstellt eine flache Kopie des el.dataset-Objekts (z. B. { component: "NewsFeed", endpoint: "...", perPage: "10" })
    const raw = { ...el.dataset };

    // Entfernt das Steuerattribut "component", da es kein Prop ist (z. B. "NewsFeed")
    delete raw.component;

    // Erstellt das Props-Objekt, das an die Vue-Komponente übergeben wird
    // - Kopiert alle übrigen data-* Werte
    // - Wandelt perPage (falls vorhanden) von String → Number um
    const props = {
        ...raw,
        perPage: raw.perPage ? Number(raw.perPage) : undefined,
    };

    // Erstellt eine neue Vue-App-Instanz mit der Komponente und mountet sie in das aktuelle Element
    createApp(Comp, props).use(pinia).mount(el);
});

*/
