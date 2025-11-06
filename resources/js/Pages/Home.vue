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


        <div class="min-h-screen bg-gray-50 p-8">
            <div class="max-w-7xl mx-auto">

                <!-- TITLE -->
                <div class="mb-8">
                    <h1 class="stitle">
                        Correspondence Management System
                    </h1>
                    <p class="text-gray-600">Dashboard Overview - November 2025</p>
                </div>

                <!-- STAT CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard title="Total Cells" :value="cells.length" color="#3b82f6" icon="users" />
                    <StatCard title="Total Issues" :value="issues.length" color="#8b5cf6" icon="file" />
                    <StatCard title="Total Receipts" :value="receipts.length" color="#ec4899" icon="mail" />
                    <StatCard
                        title="Total Correspondence"
                        :value="issues.length + receipts.length"
                        color="#10b981"
                        icon="trending-up"
                    />
                </div>

                <!-- CHARTS ROW 1 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- BAR CHART -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Issues & Receipts by Cell</h2>
                        <BarChart :chart-data="barChartData" :chart-options="defaultOptions" />
                    </div>

                    <!-- PIE CHART FOR ISSUES -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Issues Distribution by Cell</h2>
                        <PieChart :chart-data="issuesPieData" :chart-options="pieOptions" />
                    </div>
                </div>

                <!-- CHARTS ROW 2 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- LINE CHART -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Correspondence Timeline</h2>
                        <LineChart :chart-data="timelineChartData" :chart-options="defaultOptions" />
                    </div>

                    <!-- PIE CHART RECEIPTS -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Receipts Distribution by Cell</h2>
                        <PieChart :chart-data="receiptsPieData" :chart-options="pieOptions" />
                    </div>
                </div>

                <!-- SUMMARY TABLE -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Cell-wise Summary</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cell Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issues</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(c, i) in issuesByCell" :key="i" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ c.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ c.issues }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ c.receipts }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ c.issues + c.receipts }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>


    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import StatCard from "@/Components/Common/StatCard.vue";
import { computed } from "vue";
defineOptions({layout:BackendLayout})
import { BarChart, PieChart, LineChart } from "vue-chart-3";


/* -----------------------------
   RAW DATA (Same as React)
------------------------------*/
const cells = [
    { id: 1, name: "Accounts" },
    { id: 2, name: "DIC" },
    { id: 3, name: "Establishment" },
    { id: 4, name: "Technical (Monitoring)" },
    { id: 5, name: "Technical (Urban)" },
    { id: 6, name: "Technical (Rural)" }
];

const issues = [
    { id: 1, cell_id: 5, letter_date: "2025-10-12" },
    { id: 2, cell_id: 2, letter_date: "2025-10-12" },
    { id: 3, cell_id: 4, letter_date: "2025-10-16" },
    { id: 4, cell_id: 6, letter_date: "2025-10-26" },
    { id: 5, cell_id: 1, letter_date: "2025-10-17" },
    { id: 6, cell_id: 1, letter_date: "2025-11-03" },
    { id: 7, cell_id: 3, letter_date: "2025-10-10" },
    { id: 8, cell_id: 4, letter_date: "2025-10-21" },
    { id: 9, cell_id: 4, letter_date: "2025-10-19" },
    { id: 10, cell_id: 3, letter_date: "2025-10-07" }
];

const receipts = [
    { id: 1, cell_id: 2, letter_date: "2025-10-19" },
    { id: 2, cell_id: 1, letter_date: "2025-10-26" },
    { id: 3, cell_id: 5, letter_date: "2025-10-07" },
    { id: 4, cell_id: 4, letter_date: "2025-11-02" },
    { id: 5, cell_id: 1, letter_date: "2025-10-17" },
    { id: 6, cell_id: 1, letter_date: "2025-10-11" },
    { id: 7, cell_id: 4, letter_date: "2025-11-03" },
    { id: 8, cell_id: 4, letter_date: "2025-10-27" },
    { id: 9, cell_id: 6, letter_date: "2025-10-18" },
    { id: 10, cell_id: 4, letter_date: "2025-10-22" }
];

/* -----------------------------
   COMPUTED VALUES
------------------------------*/
const issuesByCell = computed(() =>
    cells.map((cell) => ({
        name: cell.name,
        issues: issues.filter((i) => i.cell_id === cell.id).length,
        receipts: receipts.filter((r) => r.cell_id === cell.id).length
    }))
);

/* -----------------------------
   BAR CHART DATA
------------------------------*/
const barChartData = computed(() => ({
    labels: issuesByCell.value.map((i) => i.name),
    datasets: [
        {
            label: "Issues",
            data: issuesByCell.value.map((i) => i.issues),
            backgroundColor: "#3b82f6"
        },
        {
            label: "Receipts",
            data: issuesByCell.value.map((i) => i.receipts),
            backgroundColor: "#ec4899"
        }
    ]
}));

/* -----------------------------
   PIE CHART DATA (Issues)
------------------------------*/
const issuesPieData = computed(() => ({
    labels: issuesByCell.value.map((i) => i.name),
    datasets: [
        {
            data: issuesByCell.value.map((i) => i.issues),
            backgroundColor: ["#3b82f6", "#8b5cf6", "#ec4899", "#f59e0b", "#10b981", "#06b6d4"]
        }
    ]
}));

/* -----------------------------
   PIE CHART DATA (Receipts)
------------------------------*/
const receiptsPieData = computed(() => ({
    labels: issuesByCell.value.map((i) => i.name),
    datasets: [
        {
            data: issuesByCell.value.map((i) => i.receipts),
            backgroundColor: ["#3b82f6", "#8b5cf6", "#ec4899", "#f59e0b", "#10b981", "#06b6d4"]
        }
    ]
}));

/* -----------------------------
   TIMELINE CHART
------------------------------*/
const timelineChartData = computed(() => {
    const issueMonths = issues.map(i => i.letter_date.substring(0, 7));
    const receiptMonths = receipts.map(r => r.letter_date.substring(0, 7));

    const issueCount = {};
    const receiptCount = {};
    const totalCount = {};

    issueMonths.forEach(m => issueCount[m] = (issueCount[m] || 0) + 1);
    receiptMonths.forEach(m => receiptCount[m] = (receiptCount[m] || 0) + 1);

    // Combine all months
    const allMonths = Array.from(new Set([...issueMonths, ...receiptMonths])).sort();

    allMonths.forEach(m => {
        totalCount[m] = (issueCount[m] || 0) + (receiptCount[m] || 0);
    });

    return {
        labels: allMonths.map(m => formatMonthYear(m)),

        datasets: [
            {
                label: "Issues",
                data: allMonths.map(m => issueCount[m] || 0),
                borderColor: "#3b82f6",
                backgroundColor: "#3b82f6",
                tension: 0.4
            },
            {
                label: "Receipts",
                data: allMonths.map(m => receiptCount[m] || 0),
                borderColor: "#ec4899",
                backgroundColor: "#ec4899",
                tension: 0.4
            },
            {
                label: "Total Correspondence",
                data: allMonths.map(m => totalCount[m] || 0),
                borderColor: "#10b981",
                backgroundColor: "#10b981",
                borderWidth: 3,
                tension: 0.4
            }
        ]
    };
});

const formatMonthYear = (ym) => {
    const [year, month] = ym.split("-");
    const date = new Date(Number(year), Number(month) - 1);
    return date.toLocaleString("en-US", { month: "short", year: "numeric" });
};
/* -----------------------------
   CHART OPTIONS
------------------------------*/
const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false
};

const pieOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: "bottom"
        }
    }
};

</script>
