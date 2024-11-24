<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ProductForm from "@/Components/ProductForm.vue";

defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <Head title="Edit Product" />

   

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Product
                </h2>
                <Link
                    :href="route('products.index')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Go Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <ProductForm 
                            mode="edit"
                            :product="product"
                            :categories="categories"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>