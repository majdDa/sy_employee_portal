<template>
    <AuthenticatedLayout>
        <main-layout :isAdmin="isAdmin">
            <ListCategories
                :categories="categories"
                :isAdmin="isAdmin"
                :flash="flash"
            />
        </main-layout>
    </AuthenticatedLayout>
</template>

<script setup>
import ListCategories from "@/Pages/Category/ListCategories.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    isAdmin: {
        type: Number,
        required: true,
    },
    flash: {
        type: Array,
        required: true,
    },
});
</script>
