<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import PendingVendorCard from "@/Components/vendors/PendingVendorCard.vue";

const props = defineProps({
    vendor: Object,
});

const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    approved: 'bg-green-100 text-green-800 border border-green-200',
    suspended: 'bg-red-100 text-red-800 border border-red-200',
    rejected: 'bg-red-200 text-red-900 border border-red-300'
};

const statusIcons = {
    pending: '⏳',
    approved: '✅',
    suspended: '⚠️',
    rejected: '❌'
};

const activeTab = ref('details');

const updateStatus = (status, reason = '') => {
    useForm({
        status,
        reason
    }).post(route(`admin.vendors.${status}`, props.vendor.id), {
        preserveScroll: true
    });
};

const approveVendor = () => {
    useForm().post(route("admin.vendors.approve", vendor.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message or handle response
        },
    });
};

const rejectVendor = (reason) => {
    useForm({
        reason: reason
    }).post(route("admin.vendors.reject", vendor.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Vendor: ${vendor.store_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <span class="mr-2">{{ vendor.store_name }}</span>
                    <span :class="['px-3 py-1 rounded-full text-sm font-medium shadow-sm', statusClasses[vendor.status]]">
                        {{ statusIcons[vendor.status] }} {{ vendor.status.charAt(0).toUpperCase() + vendor.status.slice(1) }}
                    </span>
                </h2>
                <Link :href="route('admin.vendors.index')"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-150 shadow-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                 <!-- Show verification card for pending vendors -->
                <div v-if="vendor.status === 'pending'" class="mb-6">
                    <PendingVendorCard 
                        :vendor="vendor"
                        @approve="approveVendor"
                        @reject="showRejectModal = true"
                    />
                </div>
                    
                <!-- Hero Section -->
                <div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div class="h-16 w-16 md:h-20 md:w-20 bg-white rounded-full flex items-center justify-center overflow-hidden shadow-md">
                                <img :src="vendor.store_logo || '/default-store.png'" class="h-full w-full object-cover">
                            </div>
                            <div class="ml-4 text-white">
                                <h1 class="text-xl md:text-2xl font-bold">{{ vendor.store_name }}</h1>
                                <p class="text-indigo-100">{{ vendor.business_category }}</p>
                                <p class="text-indigo-100 text-sm">Joined: {{ vendor.created_at }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button v-if="vendor.status === 'pending'"
                                @click="updateStatus('approve')"
                                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition shadow-sm focus:ring-2 focus:ring-green-300 focus:ring-offset-2 focus:outline-none">
                                Approve
                            </button>
                            <button v-if="['pending', 'approved'].includes(vendor.status)"
                                @click="updateStatus('suspend')"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition shadow-sm focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 focus:outline-none">
                                Suspend
                            </button>
                            <button v-if="vendor.status === 'suspended'"
                                @click="updateStatus('approve')"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition shadow-sm focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2 focus:outline-none">
                                Reinstate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                        <p class="text-sm text-gray-500 mb-1">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-800">{{ vendor.metrics.total_sales }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                        <p class="text-sm text-gray-500 mb-1">Products</p>
                        <p class="text-2xl font-bold text-gray-800">{{ vendor.metrics.products_count }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-500 mb-1">Rating</p>
                        <p class="text-2xl font-bold text-gray-800">{{ vendor.metrics.rating }}/5</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
                        <p class="text-sm text-gray-500 mb-1">Fulfillment Rate</p>
                        <p class="text-2xl font-bold text-gray-800">{{ vendor.metrics.fulfillment_rate }}</p>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="bg-white rounded-lg shadow-md mb-8">
                    <nav class="flex overflow-x-auto">
                        <button
                            v-for="tab in ['details', 'documents', 'products', 'activity']"
                            :key="tab"
                            @click="activeTab = tab"
                            :class="[
                                'px-6 py-4 text-center font-medium text-sm whitespace-nowrap flex-1 transition-colors duration-200',
                                activeTab === tab
                                    ? 'bg-indigo-50 text-indigo-700 border-b-2 border-indigo-500'
                                    : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50'
                            ]"
                        >
                            {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div v-show="activeTab === 'details'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-100 rounded-bl-full opacity-50"></div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8V7H5v6h10z" clip-rule="evenodd" />
                                </svg>
                                Business Information
                            </h3>
                            <div class="space-y-4 relative z-10">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500 mb-1">Store Description</p>
                                    <p class="text-gray-700">{{ vendor.store_description }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500 mb-1">Business Hours</p>
                                    <p class="text-gray-700">{{ vendor.business_hours }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="bg-white p-6 rounded-lg shadow-md relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-green-100 rounded-bl-full opacity-50"></div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                Contact Information
                            </h3>
                            <div class="space-y-4 relative z-10">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500 mb-1">Owner</p>
                                        <p class="font-medium text-gray-700">{{ vendor.full_name }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500 mb-1">Email</p>
                                        <p class="text-gray-700">{{ vendor.email }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500 mb-1">Phone</p>
                                        <p class="text-gray-700">{{ vendor.phone }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-500 mb-1">Address</p>
                                        <p class="text-gray-700">{{ vendor.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div v-show="activeTab === 'documents'" class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        Verification Documents
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="doc in vendor.verification_documents" :key="doc.id"
                             class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200 bg-white">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ doc.name }}</h4>
                                    <p class="text-sm text-gray-500">Uploaded: {{ doc.uploaded_at }}</p>
                                </div>
                                <a :href="doc.url" target="_blank"
                                   class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 transition flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Tab -->
                <div v-show="activeTab === 'products'" class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Products ({{ vendor.products?.length || 0 }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="product in vendor.products" :key="product.id"
                             class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200 bg-white flex flex-col">
                            <div class="p-4 flex-1">
                                <h4 class="font-medium text-gray-800 mb-2">{{ product.name }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Price:</span>
                                        <span class="font-medium">${{ product.price }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Stock:</span>
                                        <span class="font-medium">{{ product.stock }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Sales:</span>
                                        <span class="font-medium">{{ product.sales_count }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-3 text-right">
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Details →</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Log Tab -->
                <div v-show="activeTab === 'activity'" class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Recent Activity
                    </h3>
                    <div class="relative">
                        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        <div class="space-y-6">
                            <div v-for="(activity, index) in vendor.recent_activities" :key="index"
                                 class="relative pl-8">
                                <div class="absolute left-0 top-1.5 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center z-10 transform -translate-x-1/2 shadow-sm">
                                    <span class="text-indigo-600 text-xs">{{ index + 1 }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <p class="font-medium text-gray-800">{{ activity.action }}</p>
                                    <p class="text-sm text-gray-500">{{ activity.date }}</p>
                                    <p v-if="activity.details" class="text-sm text-gray-600 mt-2 p-2 bg-gray-50 rounded">
                                        {{ activity.details }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>