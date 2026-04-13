<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface HistoryLog {
    id: number;
    created_at: string | null;
    ai_module_used: string;
    prompt: string;
    total_tokens_used: number;
    response: unknown;
}

defineProps<{
    logs: HistoryLog[];
}>();

const selectedLog = ref<HistoryLog | null>(null);

const hasResponseContent = (value: unknown): boolean => {
    if (Array.isArray(value)) {
        return value.length > 0;
    }

    return value !== null && value !== undefined;
};

const promptPreview = (value: string): string => {
    if (value.length <= 90) {
        return value;
    }

    return `${value.slice(0, 90)}...`;
};

const openDetails = (log: HistoryLog): void => {
    selectedLog.value = log;
};

const closeDetails = (): void => {
    selectedLog.value = null;
};
</script>

<template>
    <Head title="History" />

    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-6xl px-4 pb-10 pt-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="m-0 text-2xl font-bold">Prompt and Response History</h1>
                </div>
                <a href="/" class="text-sm font-semibold hover:underline">Back to dashboard</a>
            </div>

            <div v-if="logs.length === 0" class="border border-slate-300 bg-white p-5">
                No logging records found yet.
            </div>

            <div v-else class="overflow-x-auto border border-slate-300  bg-white">
                <table class="min-w-[760px] w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap bg-gray-200 px-3 py-3 text-left text-sm font-semibold">Created</th>
                            <th class="whitespace-nowrap bg-gray-200 px-3 py-3 text-left text-sm font-semibold">Model</th>
                            <th class="whitespace-nowrap bg-gray-200 px-3 py-3 text-left text-sm font-semibold">Tokens</th>
                            <th class="whitespace-nowrap bg-gray-200 px-3 py-3 text-left text-sm font-semibold">Prompt Preview</th>
                            <th class="whitespace-nowrap bg-gray-200 px-3 py-3 text-left text-sm font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="log in logs"
                            :key="log.id"
                            class="cursor-pointer border-b border-slate-300 hover:bg-slate-50"
                            @click="openDetails(log)"
                        >
                            <td class="px-3 py-3 align-top text-sm">{{ log.created_at }}</td>
                            <td class="whitespace-nowrap px-3 py-3 align-top text-sm font-semibold text-slate-900">{{ log.ai_module_used }}</td>
                            <td class="whitespace-nowrap px-3 py-3 align-top text-sm font-bold text-teal-700">{{ log.total_tokens_used }}</td>
                            <td class="max-w-[360px] px-3 py-3 align-top text-sm break-words">{{ promptPreview(log.prompt) }}</td>
                            <td class="px-3 py-3 align-top">
                                <button
                                    type="button"
                                    class="border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-800 hover:bg-slate-100"
                                    @click.stop="openDetails(log)"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="selectedLog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click="closeDetails">
                <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto border border-slate-300  bg-white p-4" @click.stop>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h2 class="m-0 text-lg font-semibold">Log Details</h2>
                        <button
                            type="button"
                            class="border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-800 hover:bg-slate-100"
                            @click="closeDetails"
                        >
                            Close
                        </button>
                    </div>

                    <div class="mb-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div><strong>ID:</strong> {{ selectedLog.id }}</div>
                        <div><strong>Date:</strong> {{ selectedLog.created_at }}</div>
                        <div><strong>Model:</strong> {{ selectedLog.ai_module_used }}</div>
                        <div><strong>Tokens:</strong> {{ selectedLog.total_tokens_used }}</div>
                    </div>

                    <div class="mt-3">
                        <h3 class="mb-2 text-sm font-semibold">Prompt</h3>
                        <pre class="m-0 max-w-full break-words whitespace-pre-wrap border border-slate-300 bg-slate-50 p-3 font-mono text-xs text-slate-900">{{ selectedLog.prompt }}</pre>
                    </div>

                    <div v-if="hasResponseContent(selectedLog.response)" class="mt-3">
                        <h3 class="mb-2 text-sm font-semibold">Response</h3>
                        <pre class="m-0 max-w-full break-words whitespace-pre-wrap border border-slate-300 bg-slate-50 p-3 font-mono text-xs text-slate-900">{{ selectedLog.response }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
