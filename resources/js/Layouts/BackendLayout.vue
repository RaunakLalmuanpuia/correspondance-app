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
<!--                <q-btn @click="$inertia.get(route('dashboard'))" class="text-dark sized-btn" color="btn-primary" outline label="Dashboard" no-caps/>-->

                <q-btn @click="$inertia.get(route('issues.index'))"  class="text-dark sized-btn"
                       :class="{
                        'active-menu text-accent': route().current()?.includes('issues'),
                        'text-dark': !route().current()?.includes('issues')
                      }"

                       color="btn-primary" outline label="Issue" no-caps/>

                <q-btn @click="$inertia.get(route('receipts.index'))" class="text-dark sized-btn"
                       :class="{
                        'active-menu text-accent': route().current()?.includes('receipts'),
                        'text-dark': !route().current()?.includes('receipts')
                      }"
                       color="btn-primary" outline label="Receipt" no-caps/>
            </template>

            <!-- 👇 Right Menu (user dropdown) -->
            <template #right-menu>

                <q-list>
                    <q-item clickable :href="route('dashboard')">
                        <q-item-section>
                            <q-item-label>Go to Dashboard</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-expansion-item
                        expand-separator
                        label="Settings"
                        header-class="text-primary"
                    >
                        <q-separator/>
                        <q-item v-close-popup clickable @click="$inertia.get(route('profile.edit'))">
                            <q-item-section>
                                <q-item-label>Cells</q-item-label>
                            </q-item-section>
                        </q-item>

                        <q-item v-close-popup clickable @click="$inertia.get(route('user.index'))">
                            <q-item-section>
                                <q-item-label>Users</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-expansion-item>

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
                <div @click="$inertia.get(route('dashboard'))"  class="column items-center q-gutter-md q-pa-lg bg-secondary text-white cursor-pointer">
                    <q-img src="/images/logo.png" width="52px"/>
                    <div style="line-height: 1" class="text-lg text-grey-7 text-black-medium text-center">
                        Public Health Engineering Department
                        <span class="text-sm text-grey-7">(Government of Mizoram)</span>
                    </div>
                </div>

                <q-separator class="q-my-sm"/>

                <q-item :key="5451" :active="route().current()==='dashboard'"
                        active-class="active-menu"
                        clickable class="default-list" @click="$inertia.get(route('dashboard'))">
                    <q-item-section avatar><q-icon name="dashboard"/></q-item-section>
                    <q-item-section><q-item-label>Dashboard</q-item-label></q-item-section>
                </q-item>

                <q-separator class="q-my-sm"/>

                <q-item :key="5451" :active="route().current()==='issues.index'"
                        active-class="active-menu"
                        clickable class="default-list" @click="$inertia.get(route('issues.index'))">
                    <q-item-section avatar><q-icon name="outbox"/></q-item-section>
                    <q-item-section><q-item-label>Issue</q-item-label></q-item-section>
                </q-item>

                <q-separator class="q-my-sm"/>

                <q-item :key="1123" :active="route().current()==='receipts.index' "
                        active-class="active-menu"
                        clickable class="default-list" @click="$inertia.get(route('receipts.index'))">
                    <q-item-section avatar><q-icon name="inbox"/></q-item-section>
                    <q-item-section><q-item-label>Receipt</q-item-label></q-item-section>
                </q-item>

                <q-separator class="q-my-sm"/>
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
import { reactive} from "vue";

import SHeader from "@/Components/Common/SHeader.vue";
import SFooter from "@/Components/Common/SFooter.vue";

const state = reactive({
    leftDrawerOpen: false,
});

const toggleDrawer = () => {
    state.leftDrawerOpen = !state.leftDrawerOpen;
};

</script>
