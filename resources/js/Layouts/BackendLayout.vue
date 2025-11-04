<template>
    <q-layout view="hHh Lpr lff">
        <!-- HEADER -->
        <SHeader hideToggleBtn @onLeftDrawerToggle="toggleDrawer">
            <!-- 👇 Inject burger into slot -->
            <template #burger>
                <q-btn
                    flat
                    dense
                    round
                    icon="menu"
                    v-if="!$q.screen.gt.sm"
                    @click="toggleDrawer"
                />
            </template>

            <template #nav-links v-if="$q.screen.gt.sm" >
                <q-btn @click="$inertia.get(route('dashboard'))" class="text-dark sized-btn" color="btn-primary" outline label="Dashboard" no-caps/>

                <q-btn @click="$inertia.get(route('issues.index'))" class="text-dark sized-btn" color="btn-primary" outline label="Issue" no-caps/>

                <q-btn @click="$inertia.get(route('receipts.index'))" class="text-dark sized-btn" color="btn-primary" outline label="Receipt" no-caps/>
            </template>

            <!-- 👇 Right Menu (user dropdown) -->
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

        <!-- DRAWER -->
        <q-drawer
            v-model="state.leftDrawerOpen"
            side="left"
            overlay
            behavior="mobile"
            class="bg-white"
        >
            <q-list>
                <q-item clickable v-close-popup :to="route('dashboard')">
                    <q-item-section>Dashboard</q-item-section>
                </q-item>
                <q-item clickable v-close-popup :to="route('issues.index')">
                    <q-item-section>Issues</q-item-section>
                </q-item>
                <q-item clickable v-close-popup :to="route('receipts.index')">
                    <q-item-section>Receipts</q-item-section>
                </q-item>
            </q-list>
        </q-drawer>

        <!-- PAGE -->
        <q-page-container>
            <slot />
        </q-page-container>

        <!-- FOOTER -->
        <q-footer class="bg-white">
<!--            <SFooter />-->
        </q-footer>
    </q-layout>
</template>

<script setup>
import { reactive, computed, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useQuasar } from "quasar";
import SHeader from "@/Components/Common/SHeader.vue";
import SFooter from "@/Components/Common/SFooter.vue";

const state = reactive({
    leftDrawerOpen: false,
});

const toggleDrawer = () => {
    state.leftDrawerOpen = !state.leftDrawerOpen;
};

const q = useQuasar();
const notification = computed(() => usePage().props.flash_notification);

watch(
    notification,
    (newVal) => {
        if (newVal) {
            const { type, message } = newVal;
            q.notify({ type, message });
        }
    },
    { immediate: true }
);

onMounted(() => {
    console.info("hook in frontend layout", Math.random());
});
</script>
