<script setup lang="ts">
import { computed, ref } from 'vue';

const response = ref<unknown>([]);
const error = ref<string>('');

const hasResponse = computed(() => {
    if (Array.isArray(response.value)) {
        return response.value.length > 0;
    }

    return response.value !== null && response.value !== undefined;
});

const hasError = computed(() => {
    return error.value !== '';
});

const prettyJson = (value: unknown): string => {
    if (value === null || value === undefined) {
        return '';
    }

    try {
        return JSON.stringify(value, null, 2);
    } catch {
        return String(value);
    }
};

const handleAIPrompt = async () => {
    try {
        const resp = await fetch('/ai/handle', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });

        const contentType = resp.headers.get('content-type') ?? '';
        const rawBody = await resp.text();

        if (!resp.ok) {
            console.error('Request failed:', resp.status, rawBody);
        }

        if (!contentType.includes('application/json')) {
            console.error('Expected JSON response but received:', contentType || 'unknown content-type');
            console.error('Response preview:', rawBody.slice(0, 200));
            error.value = `Unexpected response format: ${contentType || 'unknown content-type'}`;
        }

        const payload = JSON.parse(rawBody);
        console.log(payload);
        response.value = payload;
    } catch (e) {
        console.error('Failed to fetch AI handler response:', e);
        error.value = 'Failed to fetch AI handler response';
    }
}
</script>

<template>

    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-6xl px-4 pb-10 pt-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="m-0 text-2xl font-bold">Dashboard</h1>
                </div>
                <a href="/history" class="text-sm font-semibold hover:underline">Go to History</a>
            </div>
            <div class="border border-slate-300 bg-white p-5 text-slate-500">
                <button @click="handleAIPrompt"
                    class="inline-flex items-cente bg-red-500 px-3 py-2 text-sm font-semibold text-white">
                    Send AI Prompt
                </button>
            </div>
            <div v-if="hasResponse" class="mt-4 border border-slate-300 p-4">
                <p class="font-semibold">AI Response:</p>
                <pre
                    class="mt-2 overflow-x-auto p-3 text-sm">{{ prettyJson(response) }}</pre>
            </div>
            <div v-if="hasError" class="mt-4 border border-slate-300">
                <p class="font-semibold">Error:</p>
                <pre class="mt-2 overflow-x-auto">{{ error }}</pre>
            </div>
            <div v-if="!hasResponse && !hasError"
                class="mt-4 border border-slate-300 bg-white p-5">
                No response yet. Click the button above to send an AI prompt.
            </div>
        </div>
    </div>
</template>

<style scoped></style>
