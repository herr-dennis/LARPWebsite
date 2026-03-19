
<script setup>
import {computed, onMounted, ref} from 'vue';
import logo from '../../images/logo_deus_taal.png'
import {useAuthStore} from "../stores/authStore.js";
const lang = document.documentElement.lang;
const authStore = useAuthStore();
const hoverUser = ref(false)
let currentRoutes = null;

//Hier werden Routen gesetzt, für mehrsprachig Website
const routes = {
    de: {
        1: { label: "Home", path: "" },
        2: { label: "Forum", path: "forum" },

        3: {
            label: "Über uns",
            path: "ueber-uns",
            children: [
                { label: "Story", path: "ueber-uns/story" },
                { label: "Pioniere", path: "ueber-uns/pioniere" },
                { label: "Kontakt", path: "ueber-uns/kontakt" },
            ],
        },

        4: { label: "Medien", path: "medien" },
        5: { label: "Handwerk", path: "handwerk" },
        6: { label: "Kontakt", path: "kontakt" },
        7: { label: "Login", path: "login" },
    },

    en: {
        1: { label: "Home", path: "" },
        2: { label: "Forum", path: "forum" },

        3: {
            label: "About us",
            path: "about",
            children: [
                { label: "Story", path: "about/story" },
                { label: "Team", path: "about/team" },
                { label: "Contact", path: "about/contact" },
            ],
        },

        4: { label: "Media", path: "media" },
        5: { label: "Craft", path: "craft" },
        6: { label: "Contact", path: "contact" },
        7: { label: "Login", path: "login" },
    },
}

function logout () {
    authStore.logout();
}

currentRoutes = getRoutes(lang);

const isLogged = computed(() => authStore.isLoggedIn);
onMounted(async () => {
    const body    = document.body;
    const nav     = document.querySelector('.nav');
    const burger  = document.querySelector('.nav__burger');
    const layer   = document.getElementById('nav-layer');
    if (!nav || !burger || !layer) return;

    await authStore.fetchUser();

    const backdrop = layer.querySelector('.nav-layer__backdrop');
    const closeBtn = layer.querySelector('#closeBtn');

    // Desktop- & Drawer-Items aktiv schalten
    const navItemDrawer  = document.querySelectorAll('.nav-layer__li');

    navItemDrawer.forEach(item => {
        item.addEventListener('click', () => {
            navItemDrawer.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            //closeNav(); // Drawer nach Auswahl schließen (üblich auf Mobile)
        });
    });

    // ARIA initial
    burger.setAttribute('aria-expanded', 'false');
    layer.setAttribute('aria-hidden', 'true');

    function preparePathname(){
        let url_ = window.location.pathname || "/";
        url_ = url_.toLowerCase();
        url_ = url_.replace("/","");
        return url_;
    }
    function getRoutes(lang) {
        return routes[lang] ?? routes.de
    }
    function openNav() {
        body.classList.add('no-scroll');
        layer.classList.add('is-open');
        nav.classList.add('is-open');            // <-- für Burger-Animation
        layer.setAttribute('aria-hidden', 'false');
        burger.setAttribute('aria-expanded', 'true');
    }
    function closeNav() {
        body.classList.remove('no-scroll');
        layer.classList.remove('is-open');
        nav.classList.remove('is-open');         // <-- zurücksetzen
        layer.setAttribute('aria-hidden', 'true');
        burger.setAttribute('aria-expanded', 'false');
    }

    function toggleNav() {
        layer.classList.contains('is-open') ? closeNav() : openNav();
    }

    burger.addEventListener('click', toggleNav);
    backdrop?.addEventListener('click', closeNav);
    closeBtn?.addEventListener('click', closeNav);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && layer.classList.contains('is-open')) closeNav();
    });

    const app = document.querySelector('#app');
    const currentPage = app.dataset.page;

    const navItems_ = document.querySelectorAll('.nav__li');
    navItems_.forEach(item => {
        if (item.dataset.nav === currentPage) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
});
function toggleDrawerSubmenu(key) {
    openDrawerSubmenu.value = openDrawerSubmenu.value === key ? null : key
}
// Mobile: welches Submenü ist offen?
const openDrawerSubmenu = ref(null) // z.B. 3


function getRoutes(lang){
    if(lang === 'de'){
        return routes.de;
    }
    //DefaultCase soll DE sein
    return routes.de;
}

function directionTo(path) {
    if (!path) {
        window.location.href = "/"
        return
    }
    window.location.href = "/" + path
}
</script>

<template>

    <Teleport to="#nav-target">
    <nav class="nav">
        <div class="nav__brand">
            <img @click="directionTo()"  id="logo_taal" :src="logo" alt="Logo">
        </div>

        <!-- Desktop-Nav -->
        <!-- Desktop-Nav -->
        <!-- Desktop-Nav -->
        <ul class="nav__ul">
            <li @click="directionTo()"  data-nav="home"  class="nav__li active">Home</li>
            <li @click="directionTo(currentRoutes[2].path)"  data-nav="forum"  class="nav__li">{{ currentRoutes[2].label }}</li>

            <!-- Dropdown (Desktop: Hover via CSS) -->
            <li  data-nav="story" class="nav__li nav__li--hasDropdown">
                  <span class="nav__link">
                  {{ currentRoutes[3].label }}
                     </span>

                <ul class="nav__dropdown"  v-if="currentRoutes[3]?.children?.length">
                    <li
                        class="nav__dropdownItem"
                        v-for="(child, i) in currentRoutes[3].children"
                        :key="child.path + i"
                        @click="directionTo(child.path)"
                    >
                        {{ child.label }}
                    </li>
                </ul>
            </li>

            <li @click="directionTo(currentRoutes[4].path)"  data-nav="medien"   class="nav__li">{{ currentRoutes[4].label }}</li>
            <li @click="directionTo(currentRoutes[5].path)"   data-nav="handwerk"   class="nav__li">{{ currentRoutes[5].label }}</li>

            <li v-if="isLogged"    data-nav="login"  class="nav__li "
                @mouseenter="hoverUser = true"
                @mouseleave="hoverUser = false"
                @click="hoverUser && logout()"
            >
                {{ hoverUser ? "Logout" : authStore.user.name }}

            </li>

            <li
                v-else
                @click="directionTo(currentRoutes[7].path)"
                class="nav__li"
                data-nav="login"
            >
                {{ currentRoutes[7].label }}
            </li>
        </ul>
        <!--
        <div class="nav__lang">
            <button @click="changeLang()">
                <svg class="icon-globe" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M2 12h20" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 2a15 15 0 0 1 0 20a15 15 0 0 1 0-20" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
                {{ lang }}
            </button>
        </div>
        -->



        <!-- Mobile-Nav -->
        <!-- Mobile-Nav -->
        <!-- Mobile-Nav -->

        <!-- Hamburger (nur mobile sichtbar) -->
        <button class="nav__burger" aria-label="Menü öffnen/schließen">
            <span></span>
        </button>
    </nav>

    <!-- Drawer-Ebene -->
    <!-- Drawer-Ebene via Teleport außerhalb des <header> -->
    <teleport to="body">
        <div id="nav-layer" class="nav-layer" aria-hidden="true">
            <div class="nav-layer__backdrop"></div>
            <aside class="nav-layer__drawer" role="dialog" aria-modal="true">
                <ul class="nav-layer__ul">
                    <li class="nav-layer__li active">Home</li>
                    <li  @click="directionTo(currentRoutes[2].path)" class="nav-layer__li">{{ currentRoutes[2].label }}</li>


                    <!-- Mobile: Klick klappt Submenü auf -->
                    <li class="nav-layer__li nav-layer__li--submenu">
                        <button
                            type="button"
                            class="nav-layer__submenuBtn"
                            @click="toggleDrawerSubmenu(3)"
                        >
                            <span>{{ currentRoutes[3].label }}</span>
                            <span class="chev" :class="{ open: openDrawerSubmenu === 3 }">›</span>
                        </button>

                        <ul
                            class="nav-layer__submenu"
                            v-if="currentRoutes[3]?.children?.length && openDrawerSubmenu === 3"
                        >
                            <li
                                class="nav-layer__submenuItem"
                                v-for="(child, i) in currentRoutes[3].children"
                                :key="child.path + i"
                                @click="directionTo(child.path)"
                            >
                                {{ child.label }}
                            </li>
                        </ul>
                    </li>



                    <li  @click="directionTo(currentRoutes[4].path)"   class="nav-layer__li">{{ currentRoutes[4].label }}</li>
                    <li  @click="directionTo(currentRoutes[5].path)"  class="nav-layer__li">{{ currentRoutes[5].label }}</li>



                    <li v-if="isLogged" class="nav-layer__li "
                        @mouseenter="hoverUser = true"
                        @mouseleave="hoverUser = false"
                        @click="hoverUser && logout()"
                    >
                        {{ hoverUser ? "Logout" : "Angemeldet: "+authStore.user.name }}

                    </li>

                    <li
                        v-else
                        @click="directionTo(currentRoutes[7].path)"
                        class="nav__li"

                    >
                        {{ currentRoutes[7].label }}
                    </li>
                </ul>

                <button class="nav_btn" id="closeBtn" aria-label="Menü schließen">&#10005;</button>
                <img @click="directionTo()" class="nav-layer__img" :src="logo" alt="Logo" />

                <div class="nav-layer__social">
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram" class="social-link instagram">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243
        5-5V7c0-2.757-2.243-5-5-5H7zm0-2h10c3.866 0 7 3.134 7 7v10c0 3.866-3.134
        7-7 7H7c-3.866 0-7-3.134-7-7V7c0-3.866 3.134-7 7-7zm5 7a5 5 0 100 10 5
        5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6zm6.5-3a1.5 1.5 0 100 3 1.5 1.5
        0 000-3z"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook" class="social-link facebook">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2
        6.477 2 12c0 4.991 3.657 9.128 8.438
        9.878v-6.987h-2.54V12h2.54V9.797c0-2.507
        1.492-3.89 3.777-3.89 1.094 0 2.238.195
        2.238.195v2.46h-1.26c-1.242 0-1.63.771-1.63
        1.562V12h2.773l-.443 2.891h-2.33v6.987C18.343
        21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
                </div>
            </aside>
        </div>
    </teleport>

    </Teleport>
</template>

<style scoped lang="scss">
$defaultColor: rgb(124, 27, 27);
$breakpoint: 768px;
$drawer-width: 280px;
$backdrop-color: rgba(43, 40, 40, 0.45);


/* Desktop-Nav */
/* Desktop-Nav */
.nav {
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;   // sorgt für links <-> rechts
    &__ul {
        list-style: none;
        display: flex;
        gap: 8px;
        margin-right: auto;
    }
    &__li {
        font-size: 1.3rem;        // etwas größer (≈ 18px)
        font-weight: 1500;         // halbfett für bessere Lesbarkeit
        letter-spacing: 0.5px;    // mehr Luft zwischen Buchstaben
        line-height: 1.6;         // angenehmer Zeilenabstand
        padding: 12px 20px;       // mehr Klickfläche
        cursor: pointer;
        color: #000000;              // Textfarbe (auf hellem BG)
        border-radius: 6px;
        transition: background-color .2s ease, color .2s ease;
        background-color: rgba(227, 216, 216, 0.45);
        &:hover { background-color:  lighten($defaultColor, 5%); }
        &.active { background-color: darken($defaultColor, 10%);color: white; border-radius: 6px; }
    }

    &__burger{
        width: 42px; height: 42px;
        border: none; background: transparent;
        position: relative; cursor: pointer; padding: 0;
        display: none;
    }
}
#logo_taal{
    width: 150px; height: 150px;
}
.nav__lang {
    margin-left: 20px;                // kleiner Abstand zum Menü
}
.nav__lang button {
    background: rgb(255,255,255,0.2);
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;          // Abstand Icon <-> Text
    font-size: 15px;
    transition: background .2s ease;
}
.icon-globe {
    width: 18px;
    height: 18px;
}
.nav__lang button:hover {
    background: #444;
}

// Mobile
@media (max-width: $breakpoint) {
    .nav {
        &__ul { display: none; }    // Desktop-Liste ausblenden
        &__burger { display: block;margin-left: auto;} // Burger zeigen

    }
}

// Desktop
@media (min-width: $breakpoint) {
    .nav-layer { display: none; }
}

$burger-size: 42px;
$burger-line-width: 24px;
$burger-line-height: 2px;
$burger-color: $defaultColor;

.nav__burger {
    width: $burger-size;
    height: $burger-size;
    border: none;
    background: transparent;
    position: relative;
    cursor: pointer;
    padding: 0;

    // drei Linien
    &::before,
    &::after,
    span {
        content: "";
        display: block;
        position: absolute;
        left: calc(($burger-size - $burger-line-width) / 2); // zentrieren
        width: $burger-line-width;
        height: $burger-line-height;
        background: $burger-color;
        border-radius: 2px;
        transition: transform .3s ease, opacity .3s ease;
    }

    span { top: 50%; transform: translateY(-50%); }
    &::before { top: 30%; }
    &::after  { top: 70%; }

}


/* ========== Desktop Dropdown (Hover) ========== */
.nav__li--hasDropdown {
    position: relative;

    .nav__link {
        display: inline-block;
        width: 100%;
    }
    .nav__dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        min-width: 220px;
        list-style: none;
        padding: 10px;
        margin: 0;

        background: rgba(20, 20, 20, 0.88);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);

        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        z-index: 9999;
    }

    &:hover .nav__dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .nav__dropdownItem {
        padding: 10px 12px;
        border-radius: 8px;
        color: rgba(255,255,255,0.9);
        cursor: pointer;
        transition: background .2s ease;

        &:hover {
            background: rgba(124, 27, 27, 0.55);
        }
    }
}


/* ========== Mobile Submenu (Klick) ========== */
/* ========== Mobile: Hamburger + Drawer ========== */

/* Gemeinsamer Layer über der Seite */
.nav-layer {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 9990;
}

/* Backdrop unter Drawer */
.nav-layer__backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
    z-index: 1;
    pointer-events: auto;
}

/* Drawer über Backdrop */
.nav-layer__drawer {
    position: fixed;
    top: 16px;
    right: 16px;
    bottom: 16px;
    width: min(82vw, 380px);
    height: auto;
    background: rgba(20, 20, 20, 0.82);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow:
        0 18px 40px rgba(0, 0, 0, 0.55),
        inset 0 0 0 1px rgba(255,255,255,0.06);
    transform: translateX(110%);
    transition: transform .3s ease;
    z-index: 2;
    padding: 72px 16px 24px 16px;
    pointer-events: auto;
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

/* Liste im Drawer */
.nav-layer__ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 14px;
}

/* Einzelne Menü-Punkte */
.nav-layer__ul li {
    cursor: pointer;
    color: rgba(236, 224, 196, 0.95);
    font-size: 1.15rem;
    font-weight: 400;
    letter-spacing: 0.3px;
    padding: 18px 18px;
    width: 80%;
    transition: background .2s ease, transform .2s ease, color .2s ease;
    border: none;
    border-left: 4px solid #9f1818;
    border-radius: 12px;
    background: rgba(255,255,255,0.06);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
}

/* Aktiver Punkt */
.nav-layer .nav-layer__li.active {
    color: #f3e6bf;
    font-weight: 700;
    transform: none;
    background: rgba(255,255,255,0.09);
    border-left: 4px solid #c51d1d;
    border-radius: 12px;
}

/* Optional etwas nicer beim Hover/Tap */
.nav-layer .nav-layer__ul li:hover {
    background: rgba(255,255,255,0.1);
    transform: translateX(2px);
}

/* Submenu Button an Kartenstil anpassen */
.nav-layer__submenuBtn {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: transparent;
    border: none;
    color: inherit;
    font: inherit;
    padding: 0;
    cursor: pointer;

}

/* Submenu selbst */
.nav-layer__submenu {
    margin-top: 12px;
    padding-left: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-layer__submenu.open {
    display: block;

}
.nav-layer__submenuItem {
    list-style: none;
    padding: 12px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,0.04);
    color: rgba(236, 224, 196, 0.88);
    font-size: 1rem;
    border-left: 3px solid rgba(197, 29, 29, 0.75);
}

/* Chevron */
.chev {
    font-size: 1.2rem;
    transition: transform .25s ease;

}

.chev.open {
    transform: rotate(90deg);
}

/* Close-Button */
.nav_btn {
    position: absolute;
    top: 16px;
    right: 16px;
    color: rgba(236, 224, 196, 0.9);
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 1.4rem;
    font-weight: 500;
    width: auto;
    padding: 4px 8px;
    transition: color .2s ease, transform .2s ease;
}

.nav_btn:hover {
    color: #fff;
    transform: scale(1.08);
}

/* Logo */
.nav-layer__img {
    height: 110px;
    width: auto;
    align-self: center;
    margin-top: 22px;
    opacity: 0.92;
}

/* Socials */
.nav-layer__social {
    margin-top: auto;
    padding-top: 24px;
    display: flex;
    justify-content: center;
    gap: 16px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    color: rgba(236, 224, 196, 0.95);
    text-decoration: none;
    transition: background .3s ease, transform .2s ease;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);

    &:hover {
        background: rgba(255,255,255,0.16);
        transform: scale(1.08);
    }

    svg {
        width: 22px;
        height: 22px;
    }
}/* Offen-Zustand */
.is-open .nav-layer__drawer { transform: translateX(0); }
.is-open .nav-layer__backdrop { opacity: 1; visibility: visible; }

/* Mobile */
@media (max-width: $breakpoint) {
    .nav {
        &__ul { display: none; }
        &__burger { display: block; margin-left: auto; }
    }
}

/* Desktop */
@media (min-width: $breakpoint) {
    .nav-layer { display: none; }
}

.no-scroll {
    overflow: hidden;
    height: 100dvh;
}
// Farben individuell für Branding
.social-link.instagram { background: #5e1b1b; }
.social-link.facebook  { background: #5e1b1b; }

</style>

