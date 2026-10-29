<script setup>
import { reactive, ref } from 'vue'

const props = defineProps({
    postUrl: { type: String, default: '/api/events' }
})

const emit = defineEmits(['created'])

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

const empty = () => ({ title: '', description: '', starts_at: '', ends_at: '' })

const form = reactive(empty())
const errors = ref({})
const saving = ref(false)
const success = ref('')

async function submit() {
    if (saving.value) return
    saving.value = true
    errors.value = {}
    success.value = ''

    try {
        const res = await fetch(props.postUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                title: form.title,
                description: form.description || null,
                starts_at: form.starts_at,
                ends_at: form.ends_at || null,
            }),
        })

        if (res.status === 422) {
            const data = await res.json()
            errors.value = data.errors ?? {}
            return
        }

        if (!res.ok) {
            alert('Fehler API Call')
            return
        }

        const created = await res.json()
        Object.assign(form, empty())
        success.value = 'Event gespeichert'
        setTimeout(() => (success.value = ''), 3000)
        emit('created', created)
    } catch (e) {
        alert('Server nicht erreichbar')
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <form class="eventForm" @submit.prevent="submit">
        <label>
            Titel
            <input type="text" v-model="form.title" maxlength="255" required>
            <span class="error" v-if="errors.title">{{ errors.title[0] }}</span>
        </label>

        <label>
            Beschreibung
            <textarea v-model="form.description" rows="4"></textarea>
            <span class="error" v-if="errors.description">{{ errors.description[0] }}</span>
        </label>

        <label>
            Beginn
            <input type="datetime-local" v-model="form.starts_at" required>
            <span class="error" v-if="errors.starts_at">{{ errors.starts_at[0] }}</span>
        </label>

        <label>
            Ende (optional)
            <input type="datetime-local" v-model="form.ends_at">
            <span class="error" v-if="errors.ends_at">{{ errors.ends_at[0] }}</span>
        </label>

        <button type="submit" :disabled="saving">
            {{ saving ? 'Speichere...' : 'Event speichern' }}
        </button>

        <p class="success" v-if="success">{{ success }}</p>
    </form>
</template>

<style scoped lang="scss">
.eventForm {
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    padding: 15px;

    display: flex;
    flex-direction: column;
    gap: 14px;

    border-radius: 12px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);

    label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: #fff;
        font-size: 0.95rem;
        text-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
    }

    input,
    textarea {
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font: inherit;
        outline: none;
        color-scheme: dark;   // dunkler Datepicker

        &:focus {
            border-color: #dc2929;
        }
    }

    textarea {
        resize: vertical;
    }

    button {
        align-self: flex-start;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        background: #dc2929;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s ease;

        &:hover:not(:disabled) {
            background: #e84a4a;
        }

        &:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    }

    .error {
        color: #ff8080;
        font-size: 0.85rem;
    }

    .success {
        margin: 0;
        color: #8fdc8f;
    }
}
</style>
