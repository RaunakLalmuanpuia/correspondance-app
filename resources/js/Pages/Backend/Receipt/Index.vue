<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Receipts</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Receipt" @click="$inertia.get(route('receipts.index'))"/>
                </q-breadcrumbs>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn @click="$inertia.get(route('receipts.create'))" color="btn-primary" label="New Receipt"/>
            </div>
        </div>
        <br/>
        <q-table
            flat
            ref="tableRef"
            title="List of Receipt"
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
            <template v-slot:top-right>
                <q-input borderless dense debounce="800"
                         @update:model-value="handleSearch"
                         bg-color="grey-2"
                         outlined
                         v-model="filter" placeholder="Search">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>
            <template v-slot:body-cell-s_no="props">
                <q-td>
                    {{ (pagination.page - 1) * pagination.rowsPerPage + (props.pageIndex + 1) }}
                </q-td>
            </template>
            <template v-slot:body-cell-letter_date="props">
                <q-td>
                    {{formatDate(props.row.letter_date)}}
                </q-td>
            </template>

            <template v-slot:body-cell-designated_cell="props">
                <q-td>
                    <q-chip :label="props.row?.cell.name" square/>
                </q-td>
            </template>

            <template v-slot:body-cell-created_at="props">
                <q-td>
                    {{formatDate(props.row.created_at)}}
                </q-td>
            </template>
            <template v-slot:body-cell-action="props">
                <q-td>
                    <q-btn round icon="more_vert">
                        <q-menu>
                            <q-item clickable @click="$inertia.get(route('receipt.show',props.row.id))">
                                <q-item-section>View Detail</q-item-section>
                            </q-item>
                            <q-item clickable @click="$inertia.get(route('receipt.edit',props.row.id))">
                                <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item @click="handleDelete(props.row)" :disable="!canDelete">
                                <q-item-section>Delete</q-item-section>
                            </q-item>
                        </q-menu>
                    </q-btn>


                </q-td>
            </template>

        </q-table>
    </q-page>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar, date} from "quasar";
import {router} from "@inertiajs/vue3";
defineOptions({layout:BackendLayout})
const props=defineProps(['canCreate','canEdit','canDelete'])

const columns = [
    { name: 's_no', align: 'left', label: 'S. No', field: 's_no', sortable: false },
    { name: 'subject', align: 'left', label: 'Subject', field: 'subject', sortable: false },
    { name: 'letter_no', align: 'left', label: 'Letter No', field: 'letter_no', sortable: false },
    { name: 'letter_date', align:'left',label: 'Letter Date', field: 'letter_date', sortable: true },
    { name: 'received_from',align:'left', label: 'Received From', field: 'received_from', sortable: true },
    { name: 'designated_cell', align:'left',label: 'Designated Cell', field: 'designated_cell', sortable: true },
    { name: 'name_of_da', align:'left',label: 'Name of Da', field: 'name_of_da', sortable: true },
    { name: 'created_at', align:'left',label: 'Issue Date', field: 'created_at', sortable: true },
    { name: 'action',align:'left', label: 'Action', field: 'action', sortable: true },
]

const q = useQuasar();
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 15,
    rowsNumber: 0
})

const handleDelete=item=>{
    q.dialog({
        title:'Confirmation',
        message:'Do you want to delete?',
        ok:'Yes',
        cancel:'No'
    })
        .onOk(()=>{
            router.delete(route('receipts.destroy',item.id),{
                onStart:params => q.loading.show(),
                onFinish:params => q.loading.hide(),
                preserveState: false
            })
        })
}
//
// const updateTableData = list => {
//     const {current_page, per_page, data, to, total} = list
//     tableData.data = data;
//     tableData.pagination.rowsNumber = total;
//     tableData.pagination.page = current_page;
//     tableData.pagination.rowsPerPage = per_page;
// }

const handleSearch=val=>{
    onRequest({pagination:pagination.value,filter:val})
}
function onRequest (props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter

    loading.value = true
    axios.get(route('receipts.json-index'),{
        params:{
            filter,
            page,
            rowsPerPage,
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

onMounted(() => {
    // get initial data from server (1st page)
    // tableRef.value.requestServerInteraction()
    onRequest({pagination:pagination.value,
        filter:filter.value
    })
})


</script>
