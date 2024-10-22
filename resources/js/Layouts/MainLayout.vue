<template>
    <v-app>
        <v-app-bar color="teal-darken-4">
            <template v-slot:image>
                <v-img
                    gradient="to top right, rgba(220,34,41,1), rgba(253, 63, 63, 1)"
                ></v-img>
            </template>

            <!-- Back button -->
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>

            <v-app-bar-title>Home</v-app-bar-title>
            <v-spacer></v-spacer>

            <!-- <SearchBar /> -->

            <v-menu offset-y bottom>
                <template v-slot:activator="{ props }">
                    <v-btn icon v-bind="props">
                        <v-icon>mdi-account-circle</v-icon>
                    </v-btn>
                </template>

                <!-- Dropdown menu content -->
                <v-list>
                    <v-list-item
                        @click="goToProfile"
                        prepend-icon="mdi-account-outline"
                    >
                        <v-list-item-title>Profile</v-list-item-title>
                    </v-list-item>

                    <v-list-item
                        v-if="isAdmin"
                        @click="goToUsers"
                        prepend-icon="mdi-account-group-outline"
                    >
                        <v-list-item-title>Users</v-list-item-title>
                    </v-list-item>

                    <v-list-item @click="logout" prepend-icon="mdi-logout">
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <v-main>
            <slot></slot>
        </v-main>
    </v-app>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import SearchBar from "@/Components/SearchBar.vue";

const props = defineProps({
    isAdmin: {
        type: Boolean,
        required: true,
    },
});
const isAdmin = ref(props.isAdmin || false);

// Composition API setup
const goBack = () => {
    window.history.back();
};

const logout = () => {
    router.post(route("logout"));
};

const goToProfile = () => {
    router.get(route("profile"));
};

const goToUsers = () => {
    router.get(route("users"));
};
</script>
