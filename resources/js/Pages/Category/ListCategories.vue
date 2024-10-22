<template>
    <v-container>
        <!-- Main Container -->
        <div class="main-container">
            <!-- Main Category Title -->
            <div class="main-category">Main Category</div>

            <AddCategory v-if="isAdmin == 1" />
        </div>

        <!-- Render categories -->
        <v-row>
            <v-col
                v-for="category in categories"
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
                    <v-card-title>{{ category.title }}</v-card-title>
                    <v-card-subtitle>{{
                        category.description
                    }}</v-card-subtitle>
                    <v-card-subtitle
                        >Location: {{ category.location }}</v-card-subtitle
                    >
                    <v-card-actions>
                        <!-- Details Button -->
                        <v-btn
                            class="details-btn"
                            text
                            @click="showDetails(category.id)"
                        >
                            Details
                        </v-btn>

                        <!-- Spacer to separate Details and Edit/Delete buttons -->
                        <v-spacer></v-spacer>

                        <UpdateCategory
                            v-if="isAdmin == 1"
                            :category="category"
                        />

                        <!-- Delete Button with Custom Confirm Dialog -->
                        <v-btn
                            v-if="isAdmin == 1"
                            class="delete-btn"
                            icon
                            @click="openDeleteDialog(category.id)"
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
                <v-card-title class="headline">Confirm Deletion</v-card-title>
                <v-card-text
                    >Are you sure you want to delete this Category?</v-card-text
                >
                <v-card-actions>
                    <v-btn color="red" text @click="confirmDelete"
                        >Delete</v-btn
                    >
                    <v-btn text @click="deleteDialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, inject } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AddCategory from "./models/AddCategory.vue";
import UpdateCategory from "./models/UpdateCategory.vue";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    isAdmin: {
        type: Number,
        required: true,
    },
});

const { flash } = usePage().props;
const notyf = inject("notyf");

// Manage delete dialog state
const deleteDialog = ref(false);
const deleteCategoryId = ref(null);

// Open delete confirmation dialog
const openDeleteDialog = (id) => {
    deleteCategoryId.value = id;
    deleteDialog.value = true;
};

// Confirm delete action
const confirmDelete = async () => {
    deleteDialog.value = false;

    try {
        await router.delete(
            route("categories.destroy", { id: deleteCategoryId.value }),
            {
                preserveState: true,
                onSuccess: (page) => {
                    const flash = page.props.flash;
                    if (flash.success) notyf.success(flash.success);
                    if (flash.error) notyf.error(flash.error);
                },
            }
        );
    } catch (error) {
        console.error("Error deleting category:", error);
        notyf.error("Failed to delete the Category.");
    }
};

const showDetails = (id) => {
    router.visit(route("categories.show", { id }));
};

const getPicture = (pictureName) => {
    return pictureName ? `${window.location.origin}/${pictureName}` : null;
};
</script>

<style scoped>
.main-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.main-category {
    font-weight: bold;
    font-size: 25px;
    color: #000;
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

.edit-btn {
    color: #000;
}

.edit-btn:hover {
    background-color: #1976d2;
    color: white;
}

.delete-btn {
    color: red;
}

.delete-btn:hover {
    background-color: red;
    color: white;
}
</style>
