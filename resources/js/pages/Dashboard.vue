<script setup lang="ts">
import {onMounted} from "vue";

onMounted(async() => {
    let resp = await fetch('/ai/handle', {
        method: 'GET',
    });

    resp = await resp.json();
    console.log(resp);
})
</script>

<template>

</template>

<style scoped>

</style>
