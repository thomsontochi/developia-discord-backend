<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";


const props = defineProps({
    vendors: Object,
    stats: Object,
    filters: Object,
});

const selectedVendors = ref([]);
const selectAll = ref(false);

const searchForm = useForm({
    search: props.filters.search || "",
    status: props.filters.status || "",
    date_from: props.filters.date_from || "",
    date_to: props.filters.date_to || "",
    performance: props.filters.performance || "",
});

const statusClasses = {
    pending: "bg-yellow-100 text-yellow-800",
    approved: "bg-green-100 text-green-800",
    suspended: "bg-red-100 text-red-800",
    rejected: "bg-red-200 text-red-900",
};

const performSearch = () => {
    searchForm.get(
        route("admin.vendors.index", { page: vendors.current_page }),
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedVendors.value = props.vendors.data.map((vendor) => vendor.id);
    } else {
        selectedVendors.value = [];
    }
};

const bulkAction = (action) => {
    if (!selectedVendors.value.length) return;

    useForm({
        vendor_ids: selectedVendors.value,
        action: action,
    }).post(route("admin.vendors.bulk-action"), {
        preserveScroll: true,
        onSuccess: () => {
            selectedVendors.value = [];
            selectAll.value = false;
        },
    });
};

const updateVendorStatus = (vendorId, status) => {
    useForm({
        status: status,
    }).put(route("admin.vendors.update-status", vendorId), {
        preserveScroll: true,
    });
};

const deleteVendor = (vendorId) => {
    if (confirm("Are you sure you want to delete this vendor?")) {
        useForm().delete(route("admin.vendors.destroy", vendorId), {
            preserveScroll: true,
        });
    }
};

const pendingVendors = computed(() =>
    props.vendors.data.filter((v) => v.status === "pending")
);
</script>

<template>
    <Head title="Manage Vendors" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Vendor Management
                </h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 w-full sm:w-auto"
                    >
                        Export Data
                    </button>
                    <Link
                        :href="route('admin.vendors.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 w-full sm:w-auto text-center"
                    >
                        Add New Vendor
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
                >
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Vendors</h3>
                        <p class="text-3xl font-bold">{{ stats.total }}</p>
                        <p class="text-sm text-gray-500">
                            +{{ stats.newThisMonth }} this month
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Active Vendors</h3>
                        <p class="text-3xl font-bold text-green-600">
                            {{ stats.active }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ stats.metrics.average_rating }} avg rating
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Sales</h3>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ stats.metrics.total_sales }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Commission: {{ stats.metrics.commission_earned }}
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Pending Approval</h3>
                        <p class="text-3xl font-bold text-yellow-600">
                            {{ stats.pending }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Return rate: {{ stats.metrics.return_rate }}
                        </p>
                    </div>
                </div>
                

                <!-- Filters -->
                <div class="mb-6 p-4 bg-white rounded-lg shadow">
                    <form @submit.prevent="performSearch" class="space-y-4">
                        <!-- Search and Status -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search vendors..."
                                class="px-4 py-2 border rounded-md w-full"
                            />
                            <select
                                v-model="searchForm.status"
                                class="px-4 py-2 border rounded-md w-full md:w-auto"
                            >
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="suspended">Suspended</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Date Range and Performance -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <div
                                class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full md:w-auto"
                            >
                                <span
                                    class="text-sm text-gray-600 whitespace-nowrap"
                                    >Date Range:</span
                                >
                                <div
                                    class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto"
                                >
                                    <input
                                        type="date"
                                        v-model="searchForm.date_from"
                                        class="px-2 py-1 border rounded w-full sm:w-auto"
                                    />
                                    <span class="hidden sm:block">to</span>
                                    <input
                                        type="date"
                                        v-model="searchForm.date_to"
                                        class="px-2 py-1 border rounded w-full sm:w-auto"
                                    />
                                </div>
                            </div>
                            <div class="flex gap-2 w-full md:w-auto">
                                <select
                                    v-model="searchForm.performance"
                                    class="px-4 py-2 border rounded-md w-full md:w-auto"
                                >
                                    <option value="">All Performance</option>
                                    <option value="high">
                                        High Performing
                                    </option>
                                    <option value="medium">Average</option>
                                    <option value="low">
                                        Needs Improvement
                                    </option>
                                </select>
                                <button
                                    type="submit"
                                    :disabled="searchForm.processing"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 whitespace-nowrap"
                                >
                                    {{
                                        searchForm.processing
                                            ? "Searching..."
                                            : "Apply"
                                    }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <div
                    v-if="selectedVendors.length"
                    class="mb-4 p-4 bg-gray-100 rounded-lg flex flex-col sm:flex-row items-start sm:items-center gap-4"
                >
                    <span class="text-sm font-medium"
                        >{{ selectedVendors.length }} vendors selected</span
                    >
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="bulkAction('approve')"
                            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Approve All
                        </button>
                        <button
                            @click="bulkAction('suspend')"
                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                        >
                            Suspend All
                        </button>
                        <button
                            @click="bulkAction('delete')"
                            class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700"
                        >
                            Delete All
                        </button>
                    </div>
                </div>

                <!-- Vendors List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            v-model="selectAll"
                                            @change="toggleSelectAll"
                                            class="rounded border-gray-300"
                                        />
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Vendor
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Store
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Joined
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Metrics
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="vendor in vendors.data"
                                    :key="vendor.id"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            v-model="selectedVendors"
                                            :value="vendor.id"
                                            class="rounded border-gray-300"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10"
                                            >
                                                <img
                                                    :src="
                                                        vendor.store_logo ||
                                                        '/default-store.png'
                                                    "
                                                    class="h-10 w-10 rounded-full"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ vendor.full_name }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{ vendor.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ vendor.store_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ vendor.business_category }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                statusClasses[vendor.status],
                                            ]"
                                        >
                                            {{ vendor.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{ vendor.created_at }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="text-sm">
                                                Sales:
                                                {{ vendor.metrics.total_sales }}
                                            </div>
                                            <div class="text-sm">
                                                Rating:
                                                {{ vendor.metrics.rating }}/5
                                            </div>
                                            <div class="text-sm">
                                                Products:
                                                {{
                                                    vendor.metrics
                                                        .products_count
                                                }}
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'admin.vendors.show',
                                                    vendor.id
                                                )
                                            "
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            View
                                        </Link>
                                        <button
                                            class="text-red-600 hover:text-red-900"
                                            @click="deleteVendor(vendor.id)"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden">
                        <div
                            v-for="vendor in vendors.data"
                            :key="vendor.id"
                            class="border-b border-gray-200 p-4"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="selectedVendors"
                                        :value="vendor.id"
                                        class="rounded border-gray-300 mr-3"
                                    />
                                    <img
                                        :src="
                                            vendor.store_logo ||
                                            '/default-store.png'
                                        "
                                        class="h-12 w-12 rounded-full"
                                    />
                                    <div class="ml-3">
                                        <div class="font-medium">
                                            {{ vendor.store_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ vendor.full_name }}
                                        </div>
                                    </div>
                                </div>
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-semibold rounded-full',
                                        statusClasses[vendor.status],
                                    ]"
                                >
                                    {{ vendor.status }}
                                </span>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-y-2 text-sm mb-4">
                                <div>
                                    <span class="text-gray-500">Email:</span>
                                    <div class="text-gray-900">
                                        {{ vendor.email }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Category:</span>
                                    <div class="text-gray-900">
                                        {{ vendor.business_category }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Joined:</span>
                                    <div class="text-gray-900">
                                        {{ vendor.created_at }}
                                    </div>
                                </div>
                            </div>

                            <!-- Metrics -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Sales</div>
                                        <div class="font-medium">
                                            {{ vendor.metrics.total_sales }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Rating</div>
                                        <div class="font-medium">
                                            {{ vendor.metrics.rating }}/5
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">
                                            Products
                                        </div>
                                        <div class="font-medium">
                                            {{ vendor.metrics.products_count }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3">
                                <Link
                                    :href="
                                        route('admin.vendors.show', vendor.id)
                                    "
                                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                                >
                                    View Details
                                </Link>
                                <button
                                    class="text-red-600 hover:text-red-900 font-medium"
                                    @click="deleteVendor(vendor.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this after the table/mobile cards section -->
                <div
                    class="mt-4 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                >
                    <!-- Mobile pagination -->
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="vendors.prev_page_url"
                            :href="vendors.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="vendors.next_page_url"
                            :href="vendors.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>

                    <!-- Desktop pagination -->
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{
                                    vendors.from
                                }}</span>
                                to
                                <span class="font-medium">{{
                                    vendors.to
                                }}</span>
                                of
                                <span class="font-medium">{{
                                    vendors.total
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <Link
                                    v-for="(link, index) in vendors.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === vendors.links.length - 1
                                            ? 'rounded-r-md'
                                            : '',
                                    ]"
                                    v-html="link.label"
                                ></Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
