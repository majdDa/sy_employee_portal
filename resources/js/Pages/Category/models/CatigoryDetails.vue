<template>
    <MainLayout :isAdmin="isAdmin">
        <v-container>
            <div class="main-container">
                <div class="main-category">
                    {{ data.data.title + " - " + data.data.description }}
                </div>
                <AddCategory v-if="isAdmin == 1" />
            </div>

            <v-row>
                <v-col
                    v-for="category in data.data.subcategories"
                    :key="category.id"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                    <v-card>
                        <v-img
                            :src="
                                getPicture(category.picture) ||
                                'https://via.placeholder.com/400x200'
                            "
                            height="200px"
                            class="white--text"
                            contain
                        ></v-img>
                        <v-card-title>
                            {{ category.title }}
                        </v-card-title>
                        <v-card-subtitle>
                            {{ category.description }}
                        </v-card-subtitle>
                        <v-card-subtitle>
                            Location: {{ category.location }}
                        </v-card-subtitle>
                        <v-card-actions>
                            <v-btn
                                class="details-btn"
                                text
                                @click="showDetails(category.id)"
                            >
                                Details
                            </v-btn>

                            <v-spacer></v-spacer>

                            <UpdateCategory
                                v-if="isAdmin == 1"
                                :category="category"
                            />

                            <v-btn
                                v-if="isAdmin == 1"
                                class="delete-btn"
                                icon
                                @click="openDeleteCategoryDialog(category.id)"
                            >
                                <v-icon color="red">mdi-delete</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Files Section -->
            <div class="section-separator">
                <span class="section-title">Files</span>
            </div>

            <UploadFile v-if="isAdmin == 1" class="mb-3" />
            <v-row>
                <v-col
                    v-for="file in data.data.files"
                    :key="file.id"
                    cols="12"
                    sm="4"
                    md="3"
                    lg="3"
                >
                    <v-card class="file-card">
                        <v-card-title>
                            <v-icon class="file-icon" large>mdi-file</v-icon>
                        </v-card-title>
                        <v-card-subtitle>{{ file.title }}</v-card-subtitle>
                        <v-card-subtitle>{{ file.mime }} File</v-card-subtitle>
                        <v-card-subtitle>
                            {{
                                file.size < 1024
                                    ? file.size + "KB"
                                    : file.size + "MB"
                            }}
                        </v-card-subtitle>

                        <v-card-actions>
                            <v-btn icon @click="fileOpen(file.id)">
                                <v-icon>mdi-open-in-new</v-icon>
                            </v-btn>

                            <v-spacer></v-spacer>

                            <v-btn icon @click="fileDownload(file.id)">
                                <v-icon>mdi-download</v-icon>
                            </v-btn>

                            <v-btn
                                v-if="isAdmin == 1"
                                icon
                                @click="openDeleteFileDialog(file.id)"
                            >
                                <v-icon color="red">mdi-delete</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Custom Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="400px">
                <v-card>
                    <v-card-title class="headline"
                        >Confirm Deletion</v-card-title
                    >
                    <v-card-text>
                        Are you sure you want to delete this {{ deleteType }}?
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red" text @click="confirmDelete"
                            >Delete</v-btn
                        >
                        <v-btn text @click="deleteDialog = false">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </MainLayout>
</template>

<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { ref, defineProps, inject } from "vue";
import AddCategory from "./AddCategory.vue";
import UpdateCategory from "./UpdateCategory.vue";
import UploadFile from "./UploadFile.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    isAdmin: {
        type: Number,
        required: true,
    },
});

const files = ref(props.data.files || []);
const deleteDialog = ref(false);
const deleteType = ref("");
const deleteItemId = ref(null);

const notyf = inject("notyf");

// Open delete confirmation dialog for category
const openDeleteCategoryDialog = (id) => {
    deleteItemId.value = id;
    deleteType.value = "Category";
    deleteDialog.value = true;
};

// Open delete confirmation dialog for file
const openDeleteFileDialog = (id) => {
    deleteItemId.value = id;
    deleteType.value = "File";
    deleteDialog.value = true;
};

// Confirm the deletion process
const confirmDelete = async () => {
    deleteDialog.value = false;

    try {
        if (deleteType.value === "Category") {
            await router.delete(
                route("categories.destroy", { id: deleteItemId.value }),
                {
                    preserveState: true,
                    onSuccess: (page) => {
                        const flash = page.props.flash;
                        if (flash.success) notyf.success(flash.success);
                        if (flash.error) notyf.error(flash.error);
                    },
                }
            );
        } else if (deleteType.value === "File") {
            await router.delete(
                route("file.delete", { id: deleteItemId.value }),
                {
                    preserveState: true,
                }
            );
            files.value = files.value.filter(
                (file) => file.id !== deleteItemId.value
            );
            notyf.success("File deleted successfully.");
        }
    } catch (error) {
        console.error(
            `Error deleting ${deleteType.value.toLowerCase()}:`,
            error
        );
        notyf.error(`Failed to delete the ${deleteType.value}.`);
    }
};

const showDetails = (id) => {
    router.visit(route("categories.show", { id }));
};

const fileOpen = (id) => {
    window.open(route("file.display", { id }), "_blank");
};

const fileDownload = (id) => {
    window.location.href = route("file.download", { id });
};

const getPicture = (pictureName) => {
    if (!pictureName) return null;
    return `${window.location.origin}/${pictureName}`;
};
</script>

<style scoped>
.main-category {
    width: 100%;
    font-weight: bold;
    font-size: 25px;
    color: #000;
    margin-bottom: 16px;
}

.v-card {
    margin-bottom: 16px;
}

.details-btn {
    border: 1px solid #000;
    color: #000;
    border-radius: 4px;
}

.details-btn:hover {
    background-color: #000;
    color: white;
}

.section-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    position: relative;
    margin: 20px 0;
}

.section-separator::before,
.section-separator::after {
    content: "";
    flex: 1;
    border-bottom: 2px solid #000;
}

.section-separator::before {
    margin-right: 10px;
}

.section-separator::after {
    margin-left: 10px;
}

.section-title {
    font-weight: bold;
    font-size: 25px;
    color: #000;
    font-weight: 500;
}

.open-folder-btn,
.download-btn,
.open-btn {
    border: 1px solid #000;
    color: #1976d2;
}

.open-folder-btn:hover,
.download-btn:hover,
.open-btn:hover {
    background-color: #000;
    color: white;
}

.main-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
</style>
