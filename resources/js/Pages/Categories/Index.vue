<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <!-- Hero Section -->
        <div
            class="relative bg-gradient-to-br from-gray-900 to-gray-800 text-white py-20"
        >
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="text-5xl font-bold tracking-tight">
                    Discover Our Categories
                </h1>
                <p class="mt-6 text-lg text-gray-300">
                    Dive into a curated selection of categories designed to
                    inspire and fulfill your needs.
                </p>
                <Link
                    :href="route('admin.categories.create')"
                    class="mt-8 inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition"
                >
                    Add a New Category
                </Link>
            </div>
        </div>

        <div class="py-8 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="category in categories.data"
                        :key="category.id"
                        class="bg-white rounded-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:shadow-xl p-4"
                    >
                    <img 
                        :src="category.image_path || category.image_url || '/default-category.jpg'"
                        @error="handleImageError"
                        alt="Category Image"
                        class="w-full h-[200px] object-cover rounded-t-lg"
                    />
                        <h3
                            class="text-lg font-serif font-bold text-gray-800 mt-2"
                        >
                            {{ category.name }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ category.description }}
                        </p>
                        <div class="mt-3 flex space-x-2">
                            <Link
                                :href="
                                    route('admin.categories.edit', {
                                        category: category.id,
                                    })
                                "
                                class="inline-block px-4 py-2 bg-blue-500 text-white rounded-lg transition-all duration-300 hover:bg-blue-600 text-sm"
                            >
                                Edit
                            </Link>
                            <button
                                @click="openDeleteModal(category.id)"
                                class="inline-block px-4 py-2 bg-red-500 text-white rounded-lg transition-all duration-300 hover:bg-red-600 text-sm"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="categories.links"
                    class="mt-6 flex justify-center space-x-2"
                >
                    <Link
                        v-for="(link, index) in categories.links"
                        :key="index"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-md text-xs font-medium transition-all duration-300"
                        :class="{
                            'bg-teal-600 text-white hover:bg-teal-700':
                                link.active,
                            'text-gray-700 bg-white border border-gray-300 hover:bg-gray-100':
                                !link.active,
                            'opacity-50 cursor-not-allowed': !link.url,
                        }"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <DeleteModal
            :show="showDeleteModal"
            :category-id="categoryToDelete"
            @close="showDeleteModal = false"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";
import DeleteModal from "@/Components/DeleteModal.vue";
import { ref } from "vue";

defineProps({
    categories: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },
});

const showDeleteModal = ref(false);
const categoryToDelete = ref(null);

const openDeleteModal = (categoryId) => {
    categoryToDelete.value = categoryId;
    showDeleteModal.value = true;
};


const handleImageError = (event) => {
    // If image fails to load, replace with a default image
    event.target.src = "https://picsum.photos/id/237/200/300";
};
</script>

<style scoped>
/* Additional styles for enhanced visual appeal */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, #4fd1c5, #63b3ed);
}
</style>
