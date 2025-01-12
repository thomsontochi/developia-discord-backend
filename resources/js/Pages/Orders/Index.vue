<script setup>
import { ref, watch } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    orders: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters?.search || "");
const status = ref(props.filters?.status || "");
const fromDate = ref("");
const toDate = ref("");
const isFilterOpen = ref(false);

// If date_range exists in filters, split it
if (props.filters?.date_range) {
    const [from, to] = props.filters.date_range.split(",");
    fromDate.value = from || "";
    toDate.value = to || "";
}

const fetchOrders = () => {
    router.get(
        route("orders.index"),
        {
            search: search.value,
            status: status.value,
            date_range:
                fromDate.value || toDate.value
                    ? `${fromDate.value},${toDate.value}`
                    : null,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// Use debounce for search
let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchOrders();
    }, 300);
});

// Immediate update for other filters
watch([status, fromDate, toDate], () => {
    fetchOrders();
});

const viewOrder = (orderId) => {
    router.get(route("orders.show", orderId));
};

const getStatusClass = (status) => {
    switch (status) {
        case "completed":
            return "bg-green-100 text-green-800";
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "cancelled":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};
</script>

<template>
    <Head title="Orders Management" />
    <!-- <pre>{{ stats.total_orders }}</pre> -->
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6"
                >
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Orders Management
                        </h1>
                        <p class="text-gray-500">
                            Track and manage All orders efficiently
                        </p>
                    </div>

                    <!-- Quick Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm font-medium text-gray-500">
                                Total Orders
                            </div>
                            <div class="text-2xl font-bold">
                                {{ stats.total_orders }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm font-medium text-gray-500">
                                Pending
                            </div>
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ stats.total_pending }}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm font-medium text-gray-500">
                                completed
                            </div>
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ stats.total_completed }}
                            </div>
                        </div>
                        <!-- cancelled -->
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="text-sm font-medium text-gray-500">
                              Total cancelled
                            </div>
                            <div class="text-2xl font-bold text-red-600">
                                {{ stats.total_cancelled }}
                            </div>
                        </div>

                        <div
                            class="hidden md:block bg-white rounded-lg shadow p-4"
                        >
                            <div class="text-sm font-medium text-gray-500">
                                Total Revenue
                            </div>
                            <div class="text-2xl font-bold text-green-600">
                                ${{ stats.total_revenue }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="bg-white rounded-lg shadow">
                    <!-- Filters Section -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Search Bar -->
                            <div class="flex-1 relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center"
                                >
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </span>
                                <input
                                    v-model="search"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Search orders..."
                                />
                            </div>

                            <!-- Filter Toggle Button -->
                            <button
                                @click="isFilterOpen = !isFilterOpen"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg
                                    class="h-4 w-4 mr-2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Filters
                            </button>
                        </div>

                        <!-- Expandable Filters -->
                        <div
                            v-if="isFilterOpen"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4"
                        >
                            <select
                                v-model="status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>

                            <input
                                type="date"
                                v-model="fromDate"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            />

                            <input
                                type="date"
                                v-model="toDate"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            />
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Order ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Customer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Vendor
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Date
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
                                    v-for="order in orders.data"
                                    :key="order.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-medium text-gray-900"
                                    >
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ order.customer.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ order.vendor.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                getStatusClass(order.status),
                                            ]"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${{ order.total_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{
                                            new Date(
                                                order.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <button
                                            @click="viewOrder(order.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mx-2"
                                        >
                                            View
                                        </button>
                                        <button
                                            v-if="order.dispute_flag"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Resolve Dispute
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                
                    <div v-if="orders.links" class="px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Pagination Links -->
                            <div class="flex flex-wrap items-center gap-1">
                                <template v-for="(link, key) in orders.links" :key="key">
                                    <div
                                        v-if="link.url === null"
                                        class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed"
                                        v-html="link.label"
                                    />
                                    <Link
                                        v-else
                                        :href="link.url"
                                        class="px-3 py-1 text-sm border rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :class="{
                                            'bg-indigo-50 text-indigo-600 border-indigo-500': link.active,
                                            'bg-white text-gray-700 border-gray-300': !link.active,
                                        }"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>

                            <!-- Results Counter -->
                            <div class="text-sm text-gray-700 whitespace-nowrap">
                                Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} results
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
