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
                    <div class="head-title mb-4">Upload New File</div>
                </v-card-title>
                <v-card-subtitle>
                    <v-form>
                        <!-- Title Field with required rule -->
                        <v-text-field
                            prepend-icon="mdi-text-short"
                            v-model="newFile.title"
                            :rules="[titleRule]"
                            label="Title"
                            required
                        ></v-text-field>

                        <!-- File Input Field with required rule -->
                        <v-file-input
                            class="w-75"
                            v-model="newFile.file"
                            variant="outlined"
                            label="Upload File"
                            accept="*/*"
                            prepend-icon="mdi-image"
                            :rules="[fileRule]"
                            required
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
                    >
                        Cancel
                    </v-btn>
                    <!-- Upload button is disabled until both fields are valid -->
                    <v-btn color="#dc2227" :disabled="!isFormValid" @click="uploadFile">
                        Upload
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, computed, inject } from "vue";
import { router } from "@inertiajs/vue3";

// Initialize Notyf
const notyf = inject("notyf");

const dialog = ref(false);
const newFile = ref({
    title: "",
    file: null,
});

// Validation rules
const titleRule = (v) => !!v || "Title is required";
const fileRule = (v) => !!v || "File is required";

// Computed property to check if both fields are valid
const isFormValid = computed(() => {
    return newFile.value.title && newFile.value.file;
});

const reset = () => {
    newFile.value.title = "";
    newFile.value.file = null;
};

const getParentIdFromUrl = () => {
    const urlSegments = window.location.pathname.split("/");
    return urlSegments[urlSegments.length - 1];
};

const uploadFile = async () => {
    if (!isFormValid.value) {
        return; // Prevent the upload if form is invalid
    }

    const formData = new FormData();
    formData.append("title", newFile.value.title);
    formData.append("category_id", getParentIdFromUrl());
    formData.append("file", newFile.value.file);

    try {
        await router.post(route("file.upload"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        dialog.value = false;

        // Show success notification
        notyf.success("File uploaded successfully!");

    } catch (error) {
        console.error("Error uploading file:", error);
        notyf.error("Failed to upload file."); // Show error notification
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
    border-radius: 1px;
    padding: 10px;
    white-space: normal;
}
</style>
