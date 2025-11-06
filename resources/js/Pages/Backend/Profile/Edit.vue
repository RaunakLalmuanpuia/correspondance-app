<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Profile</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Profile" @click="$inertia.get(route('profile.edit'))"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <div class="column justify-between items-center">
            <q-form @submit="handleSubmit" style="width: 650px" class="q-pa-md column q-gutter-sm bg-white">
                <q-input v-model="form.name"
                         outlined
                         label="User Name"
                         :error="!!form?.errors?.name"
                         :error-message="form?.errors?.name?.toString()"
                         :rules="[
                         val=>!!val || 'Username is required'
                     ]"
                />

                <q-input v-model="form.designation"
                         outlined
                         label="Designation"
                         :error="!!form?.errors?.designation"
                         :error-message="form?.errors?.designation?.toString()"
                         :rules="[
                         val=>!!val || 'Designation is required'
                     ]"
                />

                <q-input v-model="form.email"
                         outlined
                         label="Email"
                         :error="!!form?.errors?.email"
                         :error-message="form?.errors?.email?.toString()"
                         :rules="[
                         val=>!!val || 'Email is required'
                     ]"
                />
                <div class="flex q-gutter-sm">
                    <q-btn  class="sized-btn" color="primary" label="Update" type="submit"/>

                    <q-btn color="negative" outline @click="handleBack" label="Cancel"/>
                </div>
            </q-form>

        </div>
    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

defineOptions({
    layout:BackendLayout
})

const q = useQuasar();
const form=useForm({
    name:usePage().props.auth?.user?.name,
    designation:usePage().props.auth?.user?.designation,
    email:usePage().props.auth?.user?.email
})

const handleSubmit=e=>{
    form.put(route('profile.update'),{
        preserveState:true,
        onStart: params => q.loading.show({
            boxClass: 'bg-grey-2 text-grey-9',
            spinnerColor: 'primary', message: 'Updating Profile...'
        }),
        onFinish:params => q.loading.hide()
    })
}

const handleBack=e=>{
    window.history.back();
}

</script>
