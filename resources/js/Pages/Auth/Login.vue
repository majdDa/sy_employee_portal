<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    username: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <ApplicationLogo class="w-100 h-25" />

        <!-- Status message display -->
        <div v-if="status" class="mb-4 font-medium text-sm text-green-500">
            {{ status }}
        </div>

        <!-- Form container -->
        <div class="max-w-md mx-auto bg-gray-50 p-10 rounded-xl shadow-lg">
            <form @submit.prevent="submit">
                <!-- Username Input -->
                <div>
                    <InputLabel for="username" value="Username" />
                    <TextInput
                        id="username"
                        type="text"
                        class="mt-1 block w-full rounded-full border border-gray-300 focus:border-red-500 focus:ring-red-500 focus:ring-2"
                        v-model="form.username"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError
                        class="mt-2 text-red-500"
                        :message="form.errors.username"
                    />
                </div>

                <!-- Password Input -->
                <div class="mt-6">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-full border border-gray-300 focus:border-red-500 focus:ring-red-500 focus:ring-2"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError
                        class="mt-2 text-red-500"
                        :message="form.errors.password"
                    />
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-8">
                    <PrimaryButton
                        class="py-2 px-8 rounded-full bg-red-500 text-white font-semibold hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150 ease-in-out"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
