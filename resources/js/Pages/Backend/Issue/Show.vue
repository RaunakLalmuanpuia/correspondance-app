<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">ISSUE</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="User Accounts" @click="$inertia.get(route('user.index'))"/>
                    <q-breadcrumbs-el :label="data?.name"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>

        <div class="row q-col-gutter-sm" style="max-width: 720px">

            <!-- Letter Addressee (Main) -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Letter Addressee (Main)"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle whitespace-pre-line">{{ data.letter_addressee_main || '—' }}</div>
                </q-field>
            </div>

            <!-- Letter Addressee (Copy-to) -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Letter Addressee (Copy-to)"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle whitespace-pre-line">{{ data.letter_addressee_copy_to || '—' }}</div>
                </q-field>
            </div>

            <!-- Subject -->
            <div class="col-xs-12">
                <q-field
                    label="Subject"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle whitespace-pre-line">{{ data.subject || '—' }}</div>
                </q-field>
            </div>

            <!-- Letter No -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Letter No"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle">{{ data.letter_no || '—' }}</div>
                </q-field>
            </div>

            <!-- Letter Date -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Letter Date"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle">{{ formatDate(data.letter_date) }}</div>
                </q-field>
            </div>

            <!-- Designated Cell -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Designated Cell"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle">
                        {{ data.cell?.name || '—' }}
                    </div>
                </q-field>
            </div>
            <!-- Issue Date -->
            <div class="col-xs-12 col-sm-6">
                <q-field
                    label="Issue Date"
                    outlined
                    stack-label
                    bg-color="white"
                >
                    <div class="subtitle">
                        {{ formatDate(data.issue_date) || '—' }}
                    </div>
                </q-field>
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
const props=defineProps(['data',]);

const state=reactive({

    selected:null
})

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
</script>
