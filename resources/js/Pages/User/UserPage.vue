<template>
    <v-container class="user-page-container" fluid>
        <v-btn icon class="back-btn" @click="goBack">
            <v-icon>mdi-arrow-left</v-icon>
        </v-btn>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card class="user-card">
                    <v-card-title>
                        <div class="main-container">
                            <!-- Main Category Title -->
                            <div>Users</div>

                            <AddUser />
                        </div>
                        <v-spacer></v-spacer>

                        <v-text-field
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search on username"
                            single-line
                            hide-details
                            clearable
                        ></v-text-field>
                    </v-card-title>
                    <v-card-subtitle>
                        Manage users in the system.
                    </v-card-subtitle>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="users"
                            :search="search"
                            :items-per-page="5"
                            class="elevation-1"
                        >
                            <!-- Active Status Column -->
                            <template v-slot:[`item.is_active`]="{ item }">
                                <v-chip
                                    :color="
                                        Number(item.is_active) === 1
                                            ? 'green'
                                            : 'red'
                                    "
                                    dark
                                >
                                    {{
                                        Number(item.is_active) === 1
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </v-chip>
                            </template>

                            <!-- Admin Status Column -->
                            <template v-slot:[`item.isAdmin`]="{ item }">
                                <v-chip
                                    :color="
                                        Number(item.isAdmin) === 1
                                            ? 'blue'
                                            : 'grey'
                                    "
                                    dark
                                >
                                    {{
                                        Number(item.isAdmin) === 1
                                            ? "Admin"
                                            : "User"
                                    }}
                                </v-chip>
                            </template>

                            <!-- Actions Column -->
                            <template v-slot:[`item.actions`]="{ item }">
                                <div class="d-flex align-center">
                                    <v-btn
                                        icon
                                        @click="toggleActive(item)"
                                        class="m-2"
                                    >
                                        <v-icon
                                            :color="
                                                Number(item.is_active) === 1
                                                    ? 'red'
                                                    : 'green'
                                            "
                                        >
                                            {{
                                                Number(item.is_active) === 1
                                                    ? "mdi-toggle-switch-off"
                                                    : "mdi-toggle-switch"
                                            }}
                                        </v-icon>
                                    </v-btn>
                                    <!-- Change Password Button -->
                                    <v-btn
                                        icon
                                        @click="openPasswordDialog(item)"
                                        class="m-2"
                                    >
                                        <v-icon color="blue"
                                            >mdi-lock-reset</v-icon
                                        >
                                    </v-btn>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Change Password Dialog -->
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>
                    Change Password:
                    <div class="text-center font-bold">
                        {{ selectedUser.username }}
                    </div>
                </v-card-title>
                <v-card-text>
                    <!-- New Password Field with Show/Hide Icon -->
                    <v-text-field
                        v-model="newPassword"
                        :type="showPassword ? 'text' : 'password'"
                        label="New Password"
                        prepend-icon="mdi-lock"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="togglePasswordVisibility"
                        :rules="passwordRules"
                    ></v-text-field>
                    <!-- Confirm Password Field with Show/Hide Icon -->
                    <v-text-field
                        v-model="confirmPassword"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        label="Confirm Password"
                        prepend-icon="mdi-lock-check"
                        :append-icon="
                            showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'
                        "
                        @click:append="toggleConfirmPasswordVisibility"
                        :rules="confirmPasswordRules"
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="closeDialog">Cancel</v-btn>
                    <v-btn color="green" text @click="changePassword"
                        >Save</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="confirmDialog" max-width="500px">
            <v-card>
                <v-card-title class="headline"> Confirm Action </v-card-title>
                <v-card-text>
                    Are you sure you want to
                    {{
                        toggleUser && toggleUser.is_active
                            ? "deactivate"
                            : "activate"
                    }}
                    the user {{ toggleUser && toggleUser.username }}?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="confirmDialog = false"
                        >Cancel</v-btn
                    >
                    <v-btn color="green" text @click="confirmToggleActive"
                        >Confirm</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, inject } from "vue";
import { router } from "@inertiajs/vue3";
import AddUser from "./AddUser.vue";
const notyf = inject("notyf"); // Use Notyf injected globally

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const search = ref("");
const dialog = ref(false);
const confirmDialog = ref(false); // New ref for the confirmation dialog
const newPassword = ref("");
const confirmPassword = ref("");
const showPassword = ref(false);
const showConfirmPassword = ref(false);
let selectedUser = null;
let toggleUser = null; // To store the user for toggle action

const headers = [
    { title: "ID", key: "id" },
    { title: "Username", key: "username" },
    { title: "Admin Status", key: "isAdmin" },
    { title: "Active Status", key: "is_active" },
    { title: "Actions", key: "actions", sortable: false },
];

// Validation rules
const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => (v && v.length >= 6) || "Password must be at least 6 characters",
];

const confirmPasswordRules = [
    (v) => !!v || "Confirm password is required",
    (v) => v === newPassword.value || "Passwords do not match",
];

// Methods
const goBack = () => {
    window.history.back();
};

const openPasswordDialog = (user) => {
    selectedUser = user;
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    newPassword.value = "";
    confirmPassword.value = "";
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

// Open the confirmation dialog instead of the browser confirm
const toggleActive = (user) => {
    toggleUser = user; // Store the selected user for toggling action
    confirmDialog.value = true; // Open the confirmation dialog
};

const confirmToggleActive = () => {
    // Call API to toggle user status after confirming
    router.get(
        route("users.toggleActive", toggleUser.id),
        {},
        {
            onSuccess: () => {
                notyf.success(
                    `${toggleUser.username} is ${
                        toggleUser.is_active ? "deactivated" : "activated"
                    } now`
                );
            },
            onError: (error) => {
                notyf.error("Failed to toggle user status");
            },
        }
    );
    confirmDialog.value = false; // Close the dialog after action
};

const changePassword = () => {
    if (newPassword.value !== confirmPassword.value) {
        alert("Passwords do not match!");
        return;
    }

    router.post(
        route("users.changePassword", selectedUser.id),
        {
            newPassword: newPassword.value,
        },
        {
            onSuccess: () => {
                notyf.success("Password changed successfully!");
            },
            onError: (errors) => {
                notyf.error("Error in changing password!");
                console.error(errors);
            },
        }
    );

    closeDialog();
};
</script>

<style scoped>
.user-page-container {
    background-color: #ddd;
    min-height: 100vh;
    padding-top: 10px;
}

.main-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.back-btn {
    background-color: red;
    color: white;
    margin-bottom: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.user-card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    opacity: 0.9;
}
@media (max-width: 600px) {
    .user-card {
        padding: 10px;
    }
}
</style>
