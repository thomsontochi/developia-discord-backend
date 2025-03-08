<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    vendor: {
        type: Object,
        required: true
    }
});

const showDocumentModal = ref(false);
const selectedDocument = ref(null);

const verificationChecklist = {
    business_details: {
        label: 'Business Information',
        items: [
            { key: 'store_name', label: 'Store Name' },
            { key: 'business_category', label: 'Business Category' },
            { key: 'store_description', label: 'Store Description' },
            { key: 'business_hours', label: 'Business Hours' }
        ]
    },
    contact_info: {
        label: 'Contact Information',
        items: [
            { key: 'email', label: 'Email' },
            { key: 'phone', label: 'Phone Number' },
            { key: 'address', label: 'Business Address' }
        ]
    },
    documents: {
        label: 'Required Documents',
        items: [
            { key: 'business_license', label: 'Business License' },
            { key: 'tax_certificate', label: 'Tax Certificate' },
            { key: 'identity_proof', label: 'Identity Proof' }
        ]
    }
};

const verificationProgress = computed(() => {
    let completed = 0;
    let total = 0;
    
    Object.values(verificationChecklist).forEach(section => {
        section.items.forEach(item => {
            total++;
            if (props.vendor[item.key] || 
                (props.vendor.verification_documents && 
                 props.vendor.verification_documents[item.key])) {
                completed++;
            }
        });
    });
    
    return {
        percentage: Math.round((completed / total) * 100),
        completed,
        total
    };
});

const form = useForm({
    status: '',
    reason: '',
    checklist_complete: false
});
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header with Progress -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <img :src="vendor.store_logo || '/default-store.png'" 
                         class="h-12 w-12 rounded-full border-2 border-gray-200">
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">{{ vendor.store_name }}</h3>
                        <p class="text-sm text-gray-500">{{ vendor.full_name }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">
                        Verification Progress
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ verificationProgress.completed }}/{{ verificationProgress.total }} Complete
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                     :style="{ width: `${verificationProgress.percentage}%` }"
                     :class="{
                         'bg-red-600': verificationProgress.percentage < 50,
                         'bg-yellow-600': verificationProgress.percentage >= 50 && verificationProgress.percentage < 80,
                         'bg-green-600': verificationProgress.percentage >= 80
                     }">
                </div>
            </div>
        </div>

        <!-- Verification Checklist -->
        <div class="p-4">
            <div v-for="(section, sectionKey) in verificationChecklist" 
                 :key="sectionKey" 
                 class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ section.label }}</h4>
                <div class="space-y-2">
                    <div v-for="item in section.items" 
                         :key="item.key"
                         class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ item.label }}</span>
                        <span :class="[
                            'px-2 py-1 text-xs rounded-full',
                            vendor[item.key] || (vendor.verification_documents && vendor.verification_documents[item.key])
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'
                        ]">
                            {{ vendor[item.key] || (vendor.verification_documents && vendor.verification_documents[item.key]) ? 'Verified' : 'Missing' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Preview -->
        <div class="px-4 pb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Submitted Documents</h4>
            <div class="grid grid-cols-2 gap-2">
                <div v-for="doc in vendor.verification_documents" 
                     :key="doc.id"
                     class="border rounded p-2 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm truncate">{{ doc.name }}</span>
                    </div>
                    <button @click="selectedDocument = doc; showDocumentModal = true"
                            class="text-indigo-600 hover:text-indigo-900 text-sm">
                        View
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-4 py-3 bg-gray-50 text-right space-x-2">
            <button @click="$emit('approve', vendor.id)"
                    :disabled="verificationProgress.percentage < 100"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">
                Approve
            </button>
            <button @click="$emit('reject', vendor.id)"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Reject
            </button>
        </div>

        <!-- Document Preview Modal -->
        <div v-if="showDocumentModal" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg max-w-3xl w-full m-4">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium">{{ selectedDocument?.name }}</h3>
                    <button @click="showDocumentModal = false" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <img :src="selectedDocument?.url" class="max-w-full" />
                </div>
            </div>
        </div>
    </div>
</template>