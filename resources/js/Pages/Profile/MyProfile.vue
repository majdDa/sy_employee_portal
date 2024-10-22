<template>
    <v-container>
        <v-btn icon class="back-btn" @click="goBack">
            <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
        <v-row justify="center">
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>
                        <h2>My Profile</h2>
                    </v-card-title>

                    <v-card-text>
                        <!-- Username Field with Icon -->
                        <v-text-field
                            label="Username"
                            v-model="username"
                            disabled
                            prepend-inner-icon="mdi-account"
                        ></v-text-field>

                        <v-text-field
                            label="Role"
                            v-model="role"
                            disabled
                            prepend-inner-icon="mdi-account-circle-outline"
                        ></v-text-field>

                        <v-divider class="my-4"></v-divider>

                        <h3>Change Password</h3>

                        <v-form v-model="formValid">
                            <!-- New Password Field with Show/Hide Password Icon -->
                            <v-text-field
                                label="New Password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                :append-icon="
                                    showPassword ? 'mdi-eye' : 'mdi-eye-off'
                                "
                                @click:append="toggleShowPassword"
                                :rules="passwordRules"
                                prepend-inner-icon="mdi-lock"
                                required
                            ></v-text-field>

                            <!-- Confirm Password Field with Show/Hide Password Icon -->
                            <v-text-field
                                label="Confirm Password"
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                :append-icon="
                                    showPassword ? 'mdi-eye' : 'mdi-eye-off'
                                "
                                @click:append="toggleShowPassword"
                                :rules="confirmPasswordRules"
                                prepend-inner-icon="mdi-lock-check"
                                required
                            ></v-text-field>

                            <v-btn
                                :disabled="!formValid"
                                @click="changePassword"
                                color="rgba(220,34,41,1)"
                                >Update Password</v-btn
                            >
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, inject } from "vue";
import { router } from "@inertiajs/vue3";

const notyf = inject("notyf");

const props = defineProps({
    username: {
        type: String,
        required: true,
    },
    role: {
        type: String,
        required: true,
    },
});

const username = ref(props.username || "");
const role = ref(props.role || "");
const formValid = ref(false);
const showPassword = ref(false);

const form = ref({
    password: "",
    password_confirmation: "",
});

const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => (v && v.length >= 6) || "Password must be at least 6 characters",
];

const confirmPasswordRules = [
    (v) => !!v || "Confirm password is required",
    (v) => v === form.value.password || "Passwords must match",
];

// Function to toggle password visibility
const toggleShowPassword = () => {
    showPassword.value = !showPassword.value;
};

const changePassword = async () => {
    if (!formValid.value) {
        notyf.error("Form is invalid. Please check the inputs."); // Display error notification
        return;
    }

    try {
        await router.post(route("profile.password.update"), form.value);

        // Show success notification
        notyf.success("Password updated successfully.");

        // Clear form fields
        delete form.value.password;
        delete form.value.password_confirmation;
    } catch (error) {
        console.error("Error updating password:", error);

        // Show error notification
        notyf.error("Failed to update password.");
    }
};

const goBack = () => {
    window.history.back();
};
</script>

<style scoped>
h2 {
    text-align: center;
    color: #333;
}

h3 {
    margin-bottom: 10px;
}

.back-btn {
    background-color: red;
    color: white;
    margin-bottom: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
