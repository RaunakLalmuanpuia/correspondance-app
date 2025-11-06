<template>
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4" :style="{ borderLeftColor: color }">
        <div class="flex items-center justify-between">
            <div style="padding-left: 10px; padding-top: 5px">
                <p  class="text-gray-500 text-sm font-medium">{{ title }}</p>
                <p class="text-3xl font-bold mt-2"  :style="{ color }">{{ value }}</p>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
// import { Mail, FileText, Users, TrendingUp } from "lucide-vue-next";

const props = defineProps({
    title: String,
    value: Number,
    color: String,
    icon: String
});


</script>
