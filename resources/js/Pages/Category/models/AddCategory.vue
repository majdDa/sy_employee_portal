<template>
    <div>
        <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ props }">
                <v-btn icon class="mx-2" color="#000" v-bind="props">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <div class="head-title mb-4">Add New Category</div>
                </v-card-title>
                <v-card-subtitle>
                    <v-form ref="categoryForm" v-model="isFormValid">
                        <v-text-field
                            prepend-icon="mdi-text-short"
                            v-model="newCategory.title"
                            :rules="[titleRule]"
                            label="Title"
                            required
                        ></v-text-field>
                        <v-textarea
                            prepend-icon="mdi-text"
                            v-model="newCategory.description"
                            label="Description"
                            rows="3"
                        ></v-textarea>
                        <v-text-field
                            prepend-icon="mdi-text-short"
                            v-model="newCategory.location"
                            label="Location"
                        ></v-text-field>
                        <v-file-input
                            class="w-50"
                            v-model="picture"
                            variant="outlined"
                            label="Upload picture"
                            accept="image/*"
                            prepend-icon="mdi-image"
                        ></v-file-input>
                    </v-form>
                </v-card-subtitle>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="
                            reset();
                            dialog = false;
                        "
                        >Cancel</v-btn
                    >
                    <v-btn
                        :disabled="!isFormValid"
                        color="#dc2227"
                        @click="saveCategory"
                        >Save</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import { router } from "@inertiajs/vue3";

const notyf = inject("notyf");
const dialog = ref(false);
const isFormValid = ref(false);
const newCategory = ref({
    title: "",
    description: "",
    location: "",
});

const picture = ref(null);

// Validation rule for title field
const titleRule = (v) => !!v || "Title is required";

const reset = () => {
    newCategory.value.title = "";
    newCategory.value.description = "";
    newCategory.value.location = "";
    picture.value = null;
    isFormValid.value = false;
};

const getParentIdFromUrl = () => {
    const urlSegments = window.location.pathname.split("/");
    return urlSegments[urlSegments.length - 1];
};

const saveCategory = async () => {
    const formData = new FormData();
    formData.append("title", newCategory.value.title);
    formData.append("description", newCategory.value.description);
    formData.append("location", newCategory.value.location);
    formData.append("parent_category_id", getParentIdFromUrl());

    if (picture.value) {
        formData.append("picture", picture.value);
    }

    try {
        await router.post(route("categories.store"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        notyf.success("Category created successfully");
        dialog.value = false;
    } catch (error) {
        notyf.error("Failed to create category");
        console.error("Error saving category:", error);
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
