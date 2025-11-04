<template>
    <q-layout view="hHh Lpr lff">

        <SHeader hideToggleBtn @onLeftDrawerToggle="val=state.leftDrawerOpen=!val">
            <template #right-menu>
                <q-list>
                    <q-item clickable :href="route('dashboard')">
                        <q-item-section>
                            <q-item-label>Go to Dashboard</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-item v-close-popup clickable @click="$inertia.get(route('profile.edit'))">
                        <q-item-section>
                            <q-item-label>My Profile</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-item v-close-popup clickable @click="$inertia.get(route('profile.edit-password'))">
                        <q-item-section>
                            <q-item-label>Change Password</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-item v-close-popup clickable @click="$inertia.delete(route('login.destroy'))">
                        <q-item-section>
                            <q-item-label>Logout</q-item-label>
                        </q-item-section>
                    </q-item>
                </q-list>
            </template>
        </SHeader>

        <q-page-container>
            <slot/>
        </q-page-container>

<!--        <q-footer  class="bg-white">-->
<!--            <SFooter/>-->
<!--        </q-footer>-->

    </q-layout>
</template>

<script setup>
import {onMounted, reactive, watch,computed} from 'vue'
import SHeader from "@/Components/Common/SHeader.vue";

import {usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import SFooter from "@/Components/Common/SFooter.vue";

const state=reactive({
    leftDrawerOpen:false,
    rightDrawerOpen:true,
})
const q = useQuasar();
const notification=computed(()=>usePage().props.flash_notification)




watch(notification,(newVal,oldVal)=>{
    if (newVal) {
        const {type, message} = newVal;
        q.notify({type,message})
    }

},{immediate:true})

onMounted(()=>{
    console.info('hook in backend layout ',Math.random())
})
</script>
