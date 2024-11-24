<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import DeleteModal from '@/Components/DeleteModal.vue';
import { ref } from 'vue';
import ProductForm from '@/Components/ProductForm.vue';

defineProps({
    products: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },
});

const showDeleteModal = ref(false);
const productToDelete = ref(null);

const openDeleteModal = (productId) => {
    productToDelete.value = productId;
    showDeleteModal.value = true;
};


</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
                <Link
                    :href="route('products.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add New Product
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="product in products.data" :key="product.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img
                                            :src="product.image"
                                            class="h-12 w-12 rounded-full"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ product.price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ product.stock }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link 
                                            :href="route('products.edit', { product: product.id })"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Edit
                                        </Link>
                                        <button 
                                            @click="openDeleteModal(product.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- pagniation -->
                        <div class="mt-6" v-if="products.links">
                            <div class="flex flex-wrap justify-center gap-1">
                                <Link
                                    v-for="(link, index) in products.links"
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
            </div>
        </div>

        <!-- Delete Modal -->
        <DeleteModal 
        :show="showDeleteModal"
        :product-id="productToDelete"
        @close="showDeleteModal = false"
    />
    </AuthenticatedLayout>
</template>