<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Issue - {{ selectedMonthLabel }}</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Issues" @click="$inertia.get(route('issues.index'))"/>
                </q-breadcrumbs>
            </div>

            <div class="flex q-gutter-sm">
<!--                <q-btn @click="showDialog = true" color="btn-primary" label="New Issue"/>-->
                <q-btn @click="$inertia.get(route('issues.create'))" color="btn-primary" label="New Issue"/>
            </div>
        </div>
        <br/>
        <q-table
            flat
            ref="tableRef"
            :rows="rows"
            :columns="columns"
            row-key="id"
            v-model:pagination="pagination"
            :loading="loading"
            :filter="filter"
            binary-state-sort
            :rows-per-page-options="[5,10,15,30,50]"
            @request="onRequest"
        >
            <template v-slot:top-left>
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
            </template>
            <template v-slot:top-right>
                <q-input borderless dense debounce="800"
                         @update:model-value="handleSearch"
                         bg-color="grey-2"
                         outlined
                         v-model="filter" clearable placeholder="Search">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>
            <template v-slot:body-cell-s_no="props">
                <q-td>
                    {{  props.row.s_no }}
                </q-td>
            </template>
            <template v-slot:body-cell-subject="props">
                <q-td style="min-width: 200px; max-width: 300px;">
                    <div class="break-words whitespace-normal">
                        {{ props.row.subject }}
                    </div>
                </q-td>
            </template>

            <template v-slot:body-cell-letter_date="props">
                <q-td>
                    {{formatDate(props.row.letter_date)}}
                </q-td>
            </template>

            <template v-slot:body-cell-letter_addressee_main="props">
                <q-td>
                    <div class="whitespace-pre break-words">
                        {{ props.row.letter_addressee_main }}
                    </div>
                </q-td>
            </template>

            <template v-slot:body-cell-letter_addressee_copy_to="props">
                <q-td>
                    <div class="whitespace-pre break-words">
                        {{ props.row.letter_addressee_copy_to }}
                    </div>
                </q-td>
            </template>

<!--            <template v-slot:body-cell-letter_addressee_copy_to="props">-->
<!--                <q-td>-->
<!--                    <div v-if="Array.isArray(props.row.letter_addressee_copy_to)">-->
<!--                        <div-->
<!--                            v-for="(item, index) in props.row.letter_addressee_copy_to"-->
<!--                            :key="index"-->
<!--                        >-->
<!--                            {{ item }}-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div v-else>-->
<!--                        <div-->
<!--                            v-for="(item, index) in parseJson(props.row.letter_addressee_copy_to)"-->
<!--                            :key="index"-->
<!--                        >-->
<!--                            {{ item }}-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </q-td>-->
<!--            </template>-->


            <template v-slot:body-cell-designated_cell="props">
                <q-td>
                    <q-chip :label="props.row?.cell?.name" square/>
                </q-td>
            </template>

            <template v-slot:body-cell-issue_date="props">
                <q-td>
                    {{formatDate(props.row.issue_date)}}
                </q-td>
            </template>
            <template v-slot:body-cell-action="props">
                <q-td>
                    <q-btn round icon="more_vert">
                        <q-menu>
                            <q-item clickable @click="openViewDialog(props.row)">
                                <q-item-section>View Detail</q-item-section>
                            </q-item>
                            <q-item clickable @click="$inertia.get(route('issues.edit',props.row.id))">
                                <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="handleDelete(props.row)">
                                <q-item-section>Delete</q-item-section>
                            </q-item>
                        </q-menu>
                    </q-btn>

                </q-td>
            </template>
        </q-table>

        <q-dialog v-model="showDialog"  transition-show="slide-up" transition-hide="slide-down">
            <q-card>
                <q-card-section class="row items-center justify-between">
                    <div class="stitle">Issue - {{selectedIssue.s_no}}</div>
                    <q-btn dense flat icon="close" @click="showDialog = false" />
                </q-card-section>

                <q-card-section>

                    <div class="row q-col-gutter-sm" style="max-width: 720px">

                        <!-- Subject -->
                        <div class="col-xs-12">
                            <q-field label="Subject" outlined stack-label bg-color="white">
                                <div class="subtitle whitespace-pre-line">
                                    {{ selectedIssue.subject || '—' }}
                                </div>
                            </q-field>
                        </div>
                        <!-- Letter Addressee (Main) -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Letter Addressee (Main)" outlined stack-label bg-color="white">
                                <div class="subtitle whitespace-pre-line">
                                    {{ selectedIssue.letter_addressee_main || '—' }}
                                </div>
                            </q-field>
                        </div>

                        <!-- Letter Addressee Copy-to -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Letter Addressee (Copy-to)" outlined stack-label bg-color="white">
                                <div class="subtitle whitespace-pre-line">
                                    {{ selectedIssue.letter_addressee_copy_to || '—' }}
                                </div>
                            </q-field>
                        </div>



                        <!-- Letter No -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Letter No" outlined stack-label bg-color="white">
                                <div class="subtitle">
                                    {{ selectedIssue.letter_no || '—' }}
                                </div>
                            </q-field>
                        </div>

                        <!-- Letter Date -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Letter Date" outlined stack-label bg-color="white">
                                <div class="subtitle">
                                    {{ formatDate(selectedIssue.letter_date) }}
                                </div>
                            </q-field>
                        </div>

                        <!-- Designated Cell -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Designated Cell" outlined stack-label bg-color="white">
                                <div class="subtitle">
                                    {{ selectedIssue?.cell?.name || '—' }}
                                </div>
                            </q-field>
                        </div>

                        <!-- Issue Date -->
                        <div class="col-xs-12 col-sm-6">
                            <q-field label="Issue Date" outlined stack-label bg-color="white">
                                <div class="subtitle">
                                    {{ formatDate(selectedIssue.issue_date) || '—' }}
                                </div>
                            </q-field>
                        </div>

                    </div>

                </q-card-section>
            </q-card>
        </q-dialog>





    </q-page>
</template>
<script setup>
import {ref, onMounted, computed, watch} from 'vue'
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {date, useQuasar} from "quasar";
import {router, useForm} from "@inertiajs/vue3";
defineOptions({layout:BackendLayout})
const props=defineProps(['cell','canCreate','canEdit','canDelete'])

const columns = [
    { name: 's_no', align: 'left', label: 'S. No', field: 's_no', sortable: false },
    { name: 'subject', align: 'left', label: 'Subject', field: 'subject', sortable: false},
    { name: 'letter_no', align: 'left', label: 'Letter No', field: 'letter_no', sortable: false },
    { name: 'letter_addressee_main', align:'left', label: 'Address(Main)', field: 'letter_addressee_main', sortable: false },
    { name: 'letter_addressee_copy_to',align:'left', label: 'Address(Copy to)', field: 'letter_addressee_copy_to', sortable: false },
    { name: 'letter_date', align:'left',label: 'Letter Date', field: 'letter_date', sortable: true },
    { name: 'designated_cell', align:'left',label: 'Designated Cell', field: 'designated_cell', sortable: false },
    { name: 'issue_date', align:'left',label: 'Issue Date', field: 'issue_date', sortable: true },
    { name: 'action',align:'left', label: 'Action', field: 'action', sortable: false },
]

const q = useQuasar();
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0
})

const showDialog = ref(false)
const selectedIssue = ref({})


const openViewDialog = (row) => {
    selectedIssue.value = row
    showDialog.value = true
}

const handleDelete=item=>{
    q.dialog({
        title:'Confirmation',
        message:'Do you want to delete?',
        ok:'Yes',
        cancel:'No'
    })
        .onOk(()=>{
            router.delete(route('issues.destroy', item.id),{
                onStart:params => q.loading.show(),
                onFinish:params => q.loading.hide(),
                preserveState: false
            })
        })
}

const parseJson = (value) => {
    try {
        const parsed = JSON.parse(value);
        return Array.isArray(parsed) ? parsed : [parsed];
    } catch (e) {
        return value ? [value] : [];
    }
};




const handleSearch=val=>{
    filter.value = val;

    if (val && selectedMonth.value !== "all") {
        selectedMonth.value = "all";   // ✅ auto-switch to All when searching
    }

    if (!val) {
        // Search cleared → return to current month
        selectedMonth.value = currentMonth.value;
    }

    onRequest({pagination:pagination.value,filter:val})
}
function onRequest (props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter

    loading.value = true
    axios.get(route('issues.json-index'),{
        params:{
            filter,
            page,
            rowsPerPage,
            month: month.value,   // ✅ add month
            year: year.value,     // ✅ add year
        }
    })
        .then(res=>{
            console.log(res.data);
            const {list} = res.data;
            const {data,per_page,current_page,total,to,from} = list;
            rows.value = data;
            pagination.value.page = current_page;
            pagination.value.rowsNumber = total;
            pagination.value.rowsPerPage = per_page;

        })
        .catch(err=>{
            q.notify({type:'negative',message:err?.response?.data?.message})
        })
        .finally(()=>loading.value=false)
}
const formatDate = (value, pattern) => {
    try {
        if (!value) return 'N/A'   // ✅ handle null/undefined/empty
        if (pattern) {
            return date.formatDate(new Date(value), pattern)
        }
        // Default to dd-mm-yyyy
        return date.formatDate(new Date(value), 'DD-MM-YYYY')
    } catch (er) {
        return 'Invalid Date'
    }
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

// function generateMonthOptions() {
//     const today = new Date();
//     const options = [];
//     for (let i = 0; i < 12; i++) {
//         const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
//         const y = d.getFullYear();
//         const m = String(d.getMonth() + 1).padStart(2, "0");
//         options.push({ label: `${monthNames[d.getMonth()]} ${y}`, value: `${y}-${m}` });
//     }
//     monthOptions.value = options;
// }

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

// const selectedMonthLabel = computed(() => {
//     if (!selectedMonth.value) return "";
//     const [y, m] = selectedMonth.value.split("-");
//     const date = new Date(Number(y), Number(m) - 1, 1);
//     return date.toLocaleString("en-US", { month: "long", year: "numeric" });
// });

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
// const month = computed(() => {
//     const parts = (selectedMonth.value || "").split("-");
//     return Number(parts[1] || 0);
// });
// const year = computed(() => {
//     const parts = (selectedMonth.value || "").split("-");
//     return Number(parts[0] || 0);
// });
const currentMonth = ref(null);

onMounted(() => {
    generateMonthOptions();

    // [0] = All, [1] = Latest Month
    currentMonth.value = monthOptions.value[1]?.value || null;


    // set default to latest month
    selectedMonth.value = monthOptions.value[1]?.value || null;

    // get initial data from server (1st page)
    // tableRef.value.requestServerInteraction()
    onRequest({pagination:pagination.value,
        filter:filter.value
    })
})

watch(selectedMonth, () => {
    onRequest({ pagination: pagination.value, filter: filter.value });
});

</script>
