<template>
<q-page class="container" padding>
    <div class="flex items-center justify-between q-pa-md bg-white">
        <div>
            <div class="stitle">{{data?.name}}</div>
            <q-breadcrumbs class="text-dark">
                <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                <q-breadcrumbs-el class="cursor-pointer" label="User Accounts" @click="$inertia.get(route('user.index'))"/>
                <q-breadcrumbs-el :label="data?.name"/>
            </q-breadcrumbs>
        </div>
    </div>
    <br/>

        <div class="row q-col-gutter-md">
            <div class="col-xs-12 col-sm-5 q-pa-md">
                <div class="bg-white q-pa-md">
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Name</div>
                        <div class="text-weight-medium">{{ data?.name }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Designation</div>
                        <div class="text-weight-medium">{{ data?.designation }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Email</div>
                        <div class="text-weight-medium">{{ data?.email }}</div>
                    </div>
                </div>

            </div>

        </div>




</q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar, date} from "quasar";

import {reactive} from "vue";

defineOptions({layout:BackendLayout})
const props=defineProps(['data','audits']);

const state=reactive({

    selected:null
})

const formatDateTime = value => {
    try {
        return date.formatDate(new Date(value), 'DD MMM YYYY hh:mm a')
    } catch (er) {
        return 'invalid Date'
    }
}
</script>
