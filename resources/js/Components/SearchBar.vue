<!-- resources/js/Components/SearchBar.vue -->
<template>
    <div class="search-bar">
        <v-text-field
            clearable
            type="text"
            name="textSearch"
            v-model="textSearch"
            required
            density="compact"
            rounded
            filled
            variant="solo"
            label="Search"
            append-inner-icon="mdi-magnify"
            single-line
            hide-details
            style="outline: none"
        ></v-text-field>
    </div>
</template>

<script>
export default {
    name: "SearchBar",
    data() {
        return {
            textSearch: "",
        };
    },
};
</script>

<style scoped>
@media (max-width: 600px) {
    .search-bar {
        width: 30% !important;
    }
}
@media (min-width: 601px) {
    .search-bar {
        width: 40% !important;
    }
}
</style>
