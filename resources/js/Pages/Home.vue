<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";

defineOptions({layout:BackendLayout})
</script>

<template>
    <q-page class="container" >
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Dashboard</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="outbox" label="Issue" @click="$inertia.get(route('issues.index'))"/>
                    <q-breadcrumbs-el class="cursor-pointer"  icon="inbox" label="Receipt" @click="$inertia.get(route('receipts.index'))"/>
                </q-breadcrumbs>
            </div>
        </div>
    </q-page>


</template>

<style scoped>

</style>
