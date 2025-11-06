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

            <div class="flex items-center gap-3">
                <q-select
                    v-model="selectedMonth"
                    :options="monthOptions"
                    label="Select Month"
                    outlined
                    dense
                    style="width: 180px"
                    emit-value
                    map-options
                />

            </div>
        </div>
        <br/>
        <div class="q-pa-md bg-white">
            <div class="mb-8">
                <p class="text-gray-600 stitle">Overview - {{ selectedMonthLabel }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div style="padding-left: 10px; padding-top: 5px">
                            <p  class="text-gray-500 text-sm font-medium">Total Correspondance</p>
                            <p class="text-3xl font-bold mt-2">{{ statCards.correspondence }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div style="padding-left: 10px; padding-top: 5px">
                            <p  class="text-gray-500 text-sm font-medium">Total Issues</p>
                            <p class="text-3xl font-bold mt-2">{{ statCards.issues }}</p>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div style="padding-left: 10px; padding-top: 5px">
                            <p  class="text-gray-500 text-sm font-medium">Total Receipts</p>
                            <p class="text-3xl font-bold mt-2">{{ statCards.receipts }}</p>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div style="padding-left: 10px; padding-top: 5px">
                            <p  class="text-gray-500 text-sm font-medium">Total Cell</p>
                            <p class="text-3xl font-bold mt-2">{{ statCards.cells }}</p>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        <br>
        <!-- CHARTS ROW 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- BAR CHART -->

            <div class="q-pa-md bg-white full-width">
                <div class="text-lg text-primary text-bold">Issues & Receipts by Cell - {{ selectedMonthLabel }}</div>
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
import { onMounted, computed, ref, watch } from "vue";
import axios from "axios";
defineOptions({ layout: BackendLayout });
import { BarChart, LineChart } from "vue-chart-3";

// DROPDOWN OPTIONS (unchanged)
const monthNames = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

const monthOptions = ref([]);
function generateMonthOptions() {
    const today = new Date();
    const options = [];
    for (let i = 0; i < 12; i++) {
        const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, "0");
        options.push({ label: `${monthNames[d.getMonth()]} ${y}`, value: `${y}-${m}` });
    }
    monthOptions.value = options;
}

// ✅ selectedMonth is a STRING "YYYY-MM" because of emit-value + map-options
const selectedMonth = ref(null);

// Helpers
const selectedMonthLabel = computed(() => {
    if (!selectedMonth.value) return "";
    const [y, m] = selectedMonth.value.split("-");
    const date = new Date(Number(y), Number(m) - 1, 1);
    return date.toLocaleString("en-US", { month: "long", year: "numeric" });
});

// ✅ Safe month/year computations
const month = computed(() => {
    const parts = (selectedMonth.value || "").split("-");
    return Number(parts[1] || 0);
});
const year = computed(() => {
    const parts = (selectedMonth.value || "").split("-");
    return Number(parts[0] || 0);
});

// DATA
const statCards = ref({
    cells: 0,
    issues: 0,
    receipts: 0,
    correspondence: 0,
});
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

// ✅ Called when user clicks Filter button
function applyFilter() {
    loadDashboard();
}


// ✅ MAIN FUNCTION TO LOAD DATA
async function loadDashboard() {
    try {
        // Stat Cards
        const statRes = await axios.get(route('statCards'), {
            params: { month: month.value, year: year.value }
        });
        statCards.value = statRes.data;

        // Bar Chart
        const barRes = await axios.get(route('barChart'), {
            params: { month: month.value, year: year.value }
        });
        barData.value = barRes.data;

        // Timeline (usually not filtered)
        const timeRes = await axios.get(route('timeline'));
        timelineData.value = timeRes.data;

    } catch (err) {
        console.error("Dashboard Load Error:", err);
    }
}
watch(selectedMonth, loadDashboard);
// ✅ Load Data
onMounted(async () => {
    // Stat Cards
    generateMonthOptions();
    // set default to latest month
    selectedMonth.value = monthOptions.value[0]?.value || null;

    loadDashboard();
});
</script>

