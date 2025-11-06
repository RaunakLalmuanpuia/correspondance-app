<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Dashboard</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="outbox" label="Issue" @click="$inertia.get(route('issues.index'))"/>
                    <q-breadcrumbs-el class="cursor-pointer"  icon="inbox" label="Receipt" @click="$inertia.get(route('receipts.index'))"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <div class="q-pa-md bg-white">
            <div class="mb-8">
                <p class="text-gray-600 stitle">Overview - November 2025</p>
            </div>

<!--            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">-->
<!--                <StatCard title="Total Cells" :value="statCards.cells" color="#3b82f6" icon="users" />-->
<!--                <StatCard title="Total Issues" :value="statCards.issues" color="#8b5cf6" icon="file" />-->
<!--                <StatCard title="Total Receipts" :value="statCards.receipts" color="#ec4899" icon="mail" />-->
<!--                <StatCard-->
<!--                    title="Total Correspondence"-->
<!--                    :value="statCards.correspondence"-->
<!--                    color="#10b981"-->
<!--                    icon="trending-up"-->
<!--                />-->
<!--            </div>-->
        </div>
        <br>
        <!-- CHARTS ROW 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- BAR CHART -->

            <div class="q-pa-md bg-white full-width">
                <div class="text-lg text-primary text-bold">Issues & Receipts by Cell</div>
                <br />
                <div class="q-gutter-y-md">
                    <BarChart :chart-data="barChart" :chart-options="defaultOptions" />
                </div>
            </div>

<!--            <div class="bg-white rounded-lg shadow-md p-6">-->
<!--                <h2 class="stitle" style="padding-left: 20px">Issues & Receipts by Cell</h2>-->
<!--                <BarChart :chart-data="barChartData" :chart-options="defaultOptions" />-->
<!--            </div>-->

            <!-- PIE CHART FOR ISSUES -->

            <div class="q-pa-md bg-white full-width">
                <div class="text-lg text-primary text-bold">Correspondence Timeline</div>
                <br />
                <div class="q-gutter-y-md">
                    <LineChart :chart-data="timeline" :chart-options="defaultOptions" />
                </div>
            </div>


        </div>




    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import StatCard from "@/Components/Common/StatCard.vue";
import { onMounted, computed, ref } from "vue";
import axios from "axios";
defineOptions({ layout: BackendLayout });
import { BarChart, LineChart } from "vue-chart-3";

// DATA
const statCards = ref(null);
const barData = ref([]);
const timelineData = ref([]);

// FIX 1 ✅ Convert Backend Bar Data to Chart.js format
const barChart = computed(() => ({
    labels: barData.value.map(i => i.name),
    datasets: [
        {
            label: "Issues",
            data: barData.value.map(i => i.issues),
            backgroundColor: "#3b82f6"
        },
        {
            label: "Receipts",
            data: barData.value.map(i => i.receipts),
            backgroundColor: "#ec4899"
        }
    ]
}));

// FIX 2 ✅ Convert Backend Timeline Data to Chart.js format
const timeline = computed(() => ({
    labels: timelineData.value.map(i => formatMonthYear(i.month)),
    datasets: [
        {
            label: "Issues",
            data: timelineData.value.map(i => i.issues),
            borderColor: "#3b82f6",
            backgroundColor: "#3b82f6",
            tension: 0.4
        },
        {
            label: "Receipts",
            data: timelineData.value.map(i => i.receipts),
            borderColor: "#ec4899",
            backgroundColor: "#ec4899",
            tension: 0.4
        },
        {
            label: "Total",
            data: timelineData.value.map(i => i.total),
            borderColor: "#10b981",
            backgroundColor: "#10b981",
            borderWidth: 3,
            tension: 0.4
        }
    ]
}));


// Format function
const formatMonthYear = (ym) => {
    const [year, month] = ym.split("-");
    const date = new Date(Number(year), Number(month) - 1);
    return date.toLocaleString("en-US", { month: "short", year: "numeric" });
};

// Chart Options
const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false
};

// ✅ Load Data
onMounted(async () => {
    // Stat Cards
    const statRes = await axios.get(route('statCards'), {
        params: { month: 11, year: 2025 }
    });
    statCards.value = statRes.data;

    // Bar Chart
    const barRes = await axios.get(route('barChart'), {
        params: { month: 11, year: 2025 }
    });
    barData.value = barRes.data;

    // Timeline
    const timeRes = await axios.get(route('timeline'));
    timelineData.value = timeRes.data;
});
</script>

