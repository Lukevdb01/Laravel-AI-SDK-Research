<script setup lang="ts">
import {onMounted} from "vue";

onMounted(async() => {
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
            return;
        }

        if (!contentType.includes('application/json')) {
            console.error('Expected JSON response but received:', contentType || 'unknown content-type');
            console.error('Response preview:', rawBody.slice(0, 200));
            return;
        }

        const payload = JSON.parse(rawBody);
        console.log(payload);
    } catch (error) {
        console.error('Failed to fetch AI handler response:', error);
    }
})
</script>

<template>

</template>

<style scoped>

</style>
