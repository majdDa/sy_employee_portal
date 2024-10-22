<template>
    <div>
        <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ props }">
                <v-btn class="edit-btn" icon v-bind="props">
                    <v-icon>mdi-pencil</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <div class="head-title mb-4">Update Category</div>
                </v-card-title>
                <v-card-subtitle>
                    <v-form v-model="isFormValid">
                        <v-img
                            :src="
                                category.picture &&
                                typeof category.picture === 'string'
                                    ? category.picture
                                    : 'https://via.placeholder.com/400x200'
                            "
                            height="200px"
                            class="white--text mb-4"
                            contain
                        ></v-img>

                        <v-text-field
                            prepend-icon="mdi-text-short"
                            v-model="category.title"
                            label="Title"
                            required
                            :rules="[titleRule]"
                        ></v-text-field>
                        <v-textarea
                            prepend-icon="mdi-text"
                            v-model="category.description"
                            label="Description"
                            rows="3"
                        ></v-textarea>
                        <v-text-field
                            prepend-icon="mdi-text-short"
                            v-model="category.location"
                            label="Location"
                        ></v-text-field>
                        <v-file-input
                            class="w-50"
                            v-model="picture"
                            variant="outlined"
                            label="Update picture"
                            accept="image/*"
                            prepend-icon="mdi-image"
                        ></v-file-input>
                    </v-form>
                </v-card-subtitle>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="dialog = false">Cancel</v-btn>
                    <v-btn
                        color="#dc2227"
                        :disabled="!isFormValid"
                        @click="updateCategory"
                        >Update</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import { router } from "@inertiajs/vue3";

const dialog = ref(false);
const isFormValid = ref(false);
const notyf = inject("notyf");

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const picture = ref(null);

// Validation rule for title
const titleRule = (v) => !!v || "Title is required";

const updateCategory = async () => {
    const formData = new FormData();
    formData.append("id", props.category.id);
    formData.append("title", props.category.title);
    formData.append("description", props.category.description);
    formData.append("location", props.category.location);

    // Check if picture is a new file before appending
    if (picture.value instanceof File) {
        formData.append("picture", picture.value);
    }

    try {
        // Use PUT or PATCH method for updating
        await router.post(
            route("categories.edit", props.category.id),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        notyf.success("Category updated successfully");
        dialog.value = false;
    } catch (error) {
        console.error("Error updating category:", error);
        notyf.error("Failed to update category");
    }
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
