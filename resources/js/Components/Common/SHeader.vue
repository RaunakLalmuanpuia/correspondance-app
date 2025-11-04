<template>
    <q-header style="height: 70px;"  class="bg-white text-dark flex items-center">
        <q-toolbar style="padding-left: 16px;padding-right: 16px" class="container q-px-md">
            <slot name="burger">

            </slot>
            <!-- Toolbar Title (Logo + Nav Links) -->
            <q-toolbar-title class="flex items-center gap-8">
                <!-- Logo -->
                <q-img
                    class="cursor-pointer"
                    @click="$inertia.get(route('dashboard'))"
                    width="50px"
                    src="/images/logo.png"
                />


                <slot name="nav-links">

                </slot>



            </q-toolbar-title>
            <q-item flat v-if="!!$page.props.auth?.user">
                <q-item-section>
                    <div class="column">
                        <div class="text-dark q-pa-none">{{$page.props.auth?.user?.name}}</div>
                        <div style="font-size: 12px;color: #9b9b9b;margin-top: -6px"
                             class="text-dark text-caption q-pa-none">{{$page.props.auth?.user?.name}}</div>
                    </div>
                </q-item-section>
                <q-item-section side>
                    <ZAvatar/>
                </q-item-section>
                <q-menu>
                    <slot name="right-menu"/>

                </q-menu>
            </q-item>

        </q-toolbar>
    </q-header>
</template>
<script setup>
import {reactive} from "vue";
import ZAvatar from "@/Components/Icons/ZAvatar.vue";
const props = defineProps(['hideToggleBtn']);
const emit=defineEmits(['onLeftDrawerToggle'])


const toggleLeftDrawer=()=> emit('onLeftDrawerToggle');


</script>
<style scoped>
.q-toolbar-title .q-btn {
    transition: color 0.2s;
}

.q-toolbar-title .q-btn:hover {
    color: #027be3; /* Quasar primary color */
}
</style>
