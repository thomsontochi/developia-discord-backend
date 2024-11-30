<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import DeleteModal from '@/Components/DeleteModal.vue';
import { ref } from 'vue';

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




const getCategoryImage = (categoryName) => {
    console.log('Category Name Received:', categoryName);
    
    // Convert category name to lowercase and remove special characters
    const name = categoryName.toLowerCase().replace(/[^a-z0-9]/g, ''); // This will convert 'Pets & Animals' to 'petsanimals'
    console.log('Processed Category Name:', name);
    
    // Define image mappings - use the processed name format
    const imageMap = {
        petsanimals: '/storage/images/categories/pets.jpg',
        groceries: '/storage/images/categories/groceries.jpeg',
        beauty: '/storage/images/categories/beauty.jpg',
    };

    console.log('Image Map:', imageMap);
    console.log('Found Image Path:', imageMap[name]);

    const result = imageMap[name] || 'https://picsum.photos/id/237/200/300';
    console.log('Returning:', result);
    
    return result;
};


const handleImageError = (event) => {
    // If image fails to load, replace with a default image
    event.target.src = 'https://picsum.photos/id/237/200/300';
};

</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
                <Link
                    :href="route('categories.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add New Category
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                   
                    <div v-for="category in categories.data" :key="category.id"
                    class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 relative group">
                    <!-- Image Section -->
                    <div class="aspect-square overflow-hidden rounded-t-lg">
                        <img 
                            :src="category.image_url || category.image_path || 'https://picsum.photos/id/237/200/200'"
                            :alt="category.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                    </div>

                    <!-- Content Section -->
                    <div class="p-3">
                        <h3 class="font-medium text-gray-800 truncate">
                            {{ category.name }}
                        </h3>
                        
                        <!-- Action Buttons - Hover Menu -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center space-x-2">
                            <Link 
                                :href="route('categories.edit', { category: category.id })"
                                class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <button 
                                @click="openDeleteModal(category.id)"
                                class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                    
                </div>

                <!-- Pagination -->
                <div class="mt-6" v-if="categories.links">
                    <div class="flex flex-wrap justify-center gap-1">
                        <Link
                            v-for="(link, index) in categories.links"
                            :key="index"
                            :href="link.url"
                            v-html="link.label"
                            class="px-4 py-2 border rounded-md"
                            :class="{
                                'bg-indigo-600 text-white': link.active,
                                'text-gray-700 hover:bg-gray-100': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url
                            }"
                        />
                    </div>
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