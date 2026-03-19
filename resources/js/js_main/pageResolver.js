import { defineAsyncComponent } from 'vue';

export function resolvePageComponent(page) {
    const pageMap = {
        home: defineAsyncComponent(() => import('./vue_compoments/galleryComp.vue')),
        login: defineAsyncComponent(() => import('./vue_compoments/loginCom.vue')),
        kontakt: defineAsyncComponent(() => import('./vue_compoments/kontaktComp.vue')),
        handwerk: defineAsyncComponent(() => import('./vue_compoments/handwerkCardsComp.vue')),
        story: defineAsyncComponent(() => import('./vue_compoments/storyCardsComp.vue')),
        forum: defineAsyncComponent(() => import('./vue_compoments/forumComp.vue')),
        forum_topic: defineAsyncComponent(() => import('./vue_compoments/topicForumComp.vue')),
        forum_posts: defineAsyncComponent(() => import('./vue_compoments/postsForumComp.vue')),
        donnerfels: defineAsyncComponent(() => import('./vue_compoments/storyEditor.vue')),
        eckland: defineAsyncComponent(() => import('./vue_compoments/storyEditor.vue')),
        talagrad:defineAsyncComponent(() => import('./vue_compoments/storyEditor.vue')),
        pioniere: defineAsyncComponent(() => import('./vue_compoments/pioniereComp.vue')),
        medien: defineAsyncComponent(() => import('./vue_compoments/medienComp.vue')),
    };

    return pageMap[page] || null;
}
