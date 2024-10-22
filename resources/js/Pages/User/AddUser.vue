<template>
    <div>
        <!-- Dialog to add new user -->
        <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ props }">
                <v-btn icon class="mx-2" color="#333" v-bind="props">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <div class="head-title mb-4">Add New User</div>
                </v-card-title>
                <v-card-subtitle>
                    <v-form ref="form" v-model="formValid">
                        <v-text-field
                            prepend-icon="mdi-account"
                            v-model="newUser.username"
                            label="Username"
                            required
                        ></v-text-field>

                        <!-- Password Field with Show/Hide Icon -->
                        <v-text-field
                            prepend-icon="mdi-lock"
                            :append-icon="
                                showPassword ? 'mdi-eye' : 'mdi-eye-off'
                            "
                            v-model="newUser.password"
                            :type="showPassword ? 'text' : 'password'"
                            @click:append="togglePasswordVisibility"
                            :rules="passwordRules"
                            label="Password"
                            required
                        ></v-text-field>

                        <!-- Confirm Password Field with Show/Hide Icon -->
                        <v-text-field
                            prepend-icon="mdi-lock-check"
                            :append-icon="
                                showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'
                            "
                            v-model="newUser.confirmPassword"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            @click:append="toggleConfirmPasswordVisibility"
                            :rules="confirmPasswordRules"
                            label="Confirm Password"
                            required
                        ></v-text-field>

                        <v-select
                            prepend-icon="mdi-account-box-multiple"
                            v-model="newUser.role"
                            :items="roles"
                            label="Role"
                            required
                        ></v-select>
                    </v-form>
                </v-card-subtitle>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="
                            reset();
                            dialog = false;
                        "
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="#dc2227"
                        :disabled="!formValid"
                        @click="saveUser"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import { router } from "@inertiajs/vue3";
const notyf = inject("notyf"); // Use Notyf injected globally

const dialog = ref(false);
const formValid = ref(false);
const newUser = ref({
    username: "",
    password: "",
    confirmPassword: "",
    role: "User",
});

// New refs for toggling password visibility
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const roles = ref(["Admin", "User"]);

// Password validation rules
const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => (v && v.length >= 6) || "Password must be at least 6 characters",
];

const confirmPasswordRules = [
    (v) => !!v || "Confirm password is required",
    (v) => v === newUser.value.password || "Passwords do not match",
];

// Toggle password visibility
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const reset = () => {
    newUser.value.username = "";
    newUser.value.password = "";
    newUser.value.confirmPassword = "";
    newUser.value.role = "User";
    showPassword.value = false;
    showConfirmPassword.value = false;
};

const saveUser = async () => {
    if (!formValid.value) {
        notyf.error("Form is invalid. Please check the inputs.");
        return;
    }

    const formData = {
        username: newUser.value.username,
        password: newUser.value.password,
        role: newUser.value.role,
    };

    try {
        // Send the request to save the user (modify the route as needed)
        await router.post(route("users.store"), formData);

        // Show success notification using Notyf
        notyf.success("User added successfully.");
        dialog.value = false;
    } catch (error) {
        console.error("Error saving user:", error);
        notyf.error("Failed to save the user.");
    }

    reset();
};
</script>

<style scoped>
.head-title {
    text-align: center;
    background-color: #dc2227;
    color: rgb(255, 255, 255);
    font-size: large;
    font-weight: bold;
    border-radius: 10px;
    padding: 10px;
    white-space: normal;
}
</style>
