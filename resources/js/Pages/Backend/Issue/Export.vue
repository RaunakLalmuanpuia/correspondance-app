<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Export Issue</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el  class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>


        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Filter + Toolbar -->
                    <q-card flat class="q-mt-md bg-white shadow-1">
                        <q-card-section>
                            <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                                Export Filter
                            </div>

                            <div class="row q-col-gutter-md">

                                <q-select
                                    v-model="selectedMonth"
                                    :options="monthOptions"
                                    label="Select Month"
                                    outlined
                                    dense
                                    style="min-width: 180px"
                                    emit-value
                                    map-options
                                />
                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    label="Export Data"
                                    icon="download"
                                    color="primary"
                                    @click="exportData"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </q-card-section>

        </q-card>
    </q-page>
</template>


<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";

import {useQuasar} from "quasar";
import {computed, onMounted, ref} from "vue";
const $q = useQuasar();
defineOptions({layout:BackendLayout})

const props=defineProps(['office','canImport'])



const exportData = () => {
    $q.dialog({
        title: 'Confirm Export',
        message: 'Are you sure you want to export the data?',
        cancel: true,
        persistent: true
    }).onOk(() => {

        let params = {};

        if (selectedMonth.value === "all") {
            params.all = 1;
        } else if (selectedMonth.value) {
            const [y, m] = selectedMonth.value.split("-");
            params.year = y;
            params.month = m;
        }

        axios.get(route('issues.export-issue'), {
            params,
            responseType: 'blob' })
            .then(res => {
                const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = fileUrl;
                link.setAttribute('download', `Issue_${Date.now()}.xlsx`);
                link.click();
                $q.notify({
                    type: 'positive',
                    message: "Data Successfully Imported"
                })
            })
            .catch(error => {
                if (error.response) {
                    const errors = error.response.data.errors
                    const messages = []

                    if (errors) {
                        for (const field in errors) {
                            if (errors[field]) {
                                messages.push(...errors[field])
                            }
                        }

                        $q.notify({
                            type: 'negative',
                            message: messages,
                        })

                    } else if (error.response.data.message) {

                        $q.notify({
                            type: 'negative',
                            message: error.response.data.message,
                        })
                    }
                } else {
                    $q.notify({
                        type: 'negative',
                        message: 'System Error. Please try again later.'
                    })
                }
            })
            .finally(() => $q.loading.hide());
    })
}
const goBack = () => {
    window.history.back()
}


// DROPDOWN OPTIONS (unchanged)
const monthNames = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

const monthOptions = ref([]);

function generateMonthOptions() {
    const today = new Date();
    const options = [];

    // ✅ Add ALL option
    options.push({
        label: "All",
        value: "all"
    });

    for (let i = 0; i < 12; i++) {
        const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, "0");

        options.push({
            label: `${monthNames[d.getMonth()]} ${y}`,
            value: `${y}-${m}`
        });
    }

    monthOptions.value = options;
}


// ✅ selectedMonth is a STRING "YYYY-MM" because of emit-value + map-options
const selectedMonth = ref(null);

// Helpers

const selectedMonthLabel = computed(() => {
    if (selectedMonth.value === "all") return "All";
    if (!selectedMonth.value) return "";
    const [y, m] = selectedMonth.value.split("-");
    const date = new Date(Number(y), Number(m) - 1, 1);
    return date.toLocaleString("en-US", { month: "long", year: "numeric" });
});

// ✅ Safe month/year computations
const month = computed(() => {
    if (selectedMonth.value === "all") return null;
    const parts = selectedMonth.value?.split("-") || [];
    return Number(parts[1] || null);
});

const year = computed(() => {
    if (selectedMonth.value === "all") return null;
    const parts = selectedMonth.value?.split("-") || [];
    return Number(parts[0] || null);
});

const currentMonth = ref(null);

onMounted(() => {
    generateMonthOptions();

    // [0] = All, [1] = Latest Month
    currentMonth.value = monthOptions.value[1]?.value || null;


    // set default to latest month
    selectedMonth.value = monthOptions.value[1]?.value || null;

})

</script>
<style scoped>

</style>
