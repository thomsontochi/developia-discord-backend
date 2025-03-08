<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    vendor: {
        type: Object,
        required: true
    }
});

const form = useForm({
    reason: '',
    status: ''
});

const checklistItems = {
    business_details: {
        label: 'Business Details',
        required: ['store_name', 'business_category', 'store_description']
    },
    contact_info: {
        label: 'Contact Information',
        required: ['email', 'phone', 'address']
    },
    documents: {
        label: 'Required Documents',
        required: ['verification_documents']
    }
};

const isChecklistComplete = (item) => {
    return item.required.every(field => props.vendor[field]);
};

const quickApprove = () => {
    form.post(route('admin.vendors.approve', props.vendor.id));
};

const openRejectModal = () => {
    form.status = 'rejected';
    // Implement modal logic
};
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <!-- Vendor Basic Info -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <img :src="vendor.store_logo || '/default-store.png'" 
                     class="h-12 w-12 rounded-full mr-4">
                <div>
                    <h3 class="font-medium text-gray-900">{{ vendor.store_name }}</h3>
                    <p class="text-sm text-gray-500">{{ vendor.full_name }}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                Pending Review
            </span>
        </div>

        <!-- Verification Checklist -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Verification Checklist</h4>
            <div class="space-y-2">
                <div v-for="(item, key) in checklistItems" 
                     :key="key"
                     class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ item.label }}</span>
                    <span :class="[
                        'px-2 py-1 rounded-full text-xs',
                        isChecklistComplete(item) 
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                    ]">
                        {{ isChecklistComplete(item) ? 'Complete' : 'Incomplete' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Documents Preview -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Submitted Documents</h4>
            <div class="grid grid-cols-2 gap-2">
                <div v-for="doc in vendor.verification_documents" 
                     :key="doc.id"
                     class="p-2 border rounded-md">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">{{ doc.name }}</span>
                        <a :href="doc.url" 
                           target="_blank"
                           class="text-indigo-600 hover:text-indigo-800 text-sm">
                            View
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="border-t border-gray-200 pt-4 flex justify-end space-x-2">
            <button @click="quickApprove"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Approve
            </button>
            <button @click="openRejectModal"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Reject
            </button>
        </div>
    </div>
</template>