<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ChartBarIcon,
    UsersIcon,
    ShoppingBagIcon,
    CurrencyDollarIcon,
    ShoppingCartIcon,
    FolderIcon,
    StarIcon,
    XMarkIcon,
    Bars3Icon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/solid";

const mobileMenuOpen = ref(false);

defineProps({
    stats: {
        type: Object,
        default: () => ({
            vendors: {
                total: 0,
                active: 0,
                inactive: 0,
                pending: 0,
                newThisMonth: 0,
            },
            customers: {
                total: 1234,
                active: 1100,
                inactive: 134,
                newThisMonth: 45,
            },
            products: {
                total: 2567,
                active: 2444,
                outOfStock: 123,
                categories: 12,
            },
            orders: {
                total: 0,
                today: 45,
                pending: 12,
                shipped: 30,
                completed: 0,
                cancelled: 3,
            },
            revenue: {
                today: 1234,
                thisMonth: 45678,
                total: 0,
                platformFees: 0,
            },
        }),
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="relative flex flex-col sm:flex-row justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-700 p-4 shadow-lg"
            >
                <!-- Header Content -->
                <div class="flex w-full justify-between items-center">
                    <h2
                        class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2 sm:gap-3"
                    >
                        <ChartBarIcon class="h-6 w-6 sm:h-8 sm:w-8" />
                        Vendly Admin Dashboard
                    </h2>

                    <!-- Mobile Menu Toggle -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="sm:hidden text-white focus:outline-none"
                    >
                        <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <XMarkIcon v-else class="h-6 w-6" />
                    </button>
                </div>

                <!-- Mobile Menu -->
                <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-show="mobileMenuOpen"
                        class="absolute top-full left-0 right-0 w-full bg-indigo-800 shadow-lg sm:hidden z-50"
                    >
                        <div class="p-4 space-y-3">
                            <Link
                                :href="route('categories.create')"
                                class="w-full px-4 py-2 bg-white/20 text-white rounded-md hover:bg-white/30 transition-all flex items-center justify-center gap-2"
                            >
                                <FolderIcon class="h-5 w-5" />
                                <span>Add Category</span>
                            </Link>
                            <!-- Add more mobile menu items here -->
                        </div>
                    </div>
                </transition>

                <!-- Desktop Menu -->
                <div class="hidden sm:flex items-center">
                    <Link
                        :href="route('categories.create')"
                        class="px-4 py-2 bg-white/20 text-white rounded-md hover:bg-white/30 transition-all flex items-center whitespace-nowrap"
                    >
                        <FolderIcon class="h-5 w-5 mr-2" />
                        Add Category
                    </Link>
                </div>
            </div>
        </template>

        <!-- WhatsApp Business Info -->
        <!-- <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">WhatsApp Business Connection</h3>
                                <p class="text-sm text-gray-500">Connect your WhatsApp Business account to start receiving orders</p>
                            </div>
                            <button class="ml-auto px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Connect WhatsApp
                            </button>
                        </div>
        </div> -->

        <div class="bg-gray-50 min-h-screen p-3 sm:p-6">
            <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">
                <!-- Key Metrics Grid -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6"
                >
                    <!-- Customers Card -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all p-4 sm:p-6 border-l-4 border-blue-500 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <UsersIcon class="h-6 w-6 text-blue-500" />
                                <h3
                                    class="text-base sm:text-lg font-semibold text-gray-800"
                                >
                                    Customers
                                </h3>
                            </div>
                        </div>
                        <span
                            class="text-xs sm:text-sm text-green-600 bg-green-50 px-2 py-1 rounded-full"
                        >
                            +{{ stats.customers.newThisMonth }} this month
                        </span>
                        <div class="space-y-2">
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Total</span>
                                <span class="font-bold text-gray-800">{{
                                    stats.customers.total
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Active</span>
                                <span class="text-green-600 font-bold">{{
                                    stats.customers.active
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Products Card -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all p-4 sm:p-6 border-l-4 border-green-500 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <ShoppingBagIcon
                                    class="h-6 w-6 text-green-500"
                                />
                                <h3
                                    class="text-base sm:text-lg font-semibold text-gray-800"
                                >
                                    Products
                                </h3>
                            </div>
                            <span
                                class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-full"
                            >
                                {{ stats.products.categories }} Categories
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Total Listed</span>
                                <span class="font-bold text-gray-800">{{
                                    stats.products.total
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Out of Stock</span>
                                <span class="text-red-600 font-bold">{{
                                    stats.products.outOfStock
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Card -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all p-4 sm:p-6 border-l-4 border-purple-500 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <ShoppingCartIcon
                                    class="h-6 w-6 text-purple-500"
                                />
                                <h3
                                    class="text-base sm:text-lg font-semibold text-gray-800"
                                >
                                    Orders
                                </h3>
                            </div>
                            <span
                                class="text-xs bg-purple-50 text-purple-600 px-2 py-1 rounded-full"
                            >
                                {{ stats.orders.total }} Total
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600"
                                    >Today's Orders</span
                                >
                                <span class="font-bold text-gray-800">{{
                                    stats.orders.today
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Pending</span>
                                <span class="text-yellow-600 font-bold">{{
                                    stats.orders.pending
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all p-4 sm:p-6 border-l-4 border-indigo-500 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <CurrencyDollarIcon
                                    class="h-6 w-6 text-indigo-500"
                                />
                                <h3
                                    class="text-base sm:text-lg font-semibold text-gray-800"
                                >
                                    Revenue
                                </h3>
                            </div>
                            <span
                                class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full"
                            >
                                Monthly Total
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">Today</span>
                                <span class="font-bold text-gray-800"
                                    >${{
                                        stats.revenue.today.toLocaleString()
                                    }}</span
                                >
                            </div>
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600">This Month</span>
                                <span class="text-green-600 font-bold"
                                    >${{
                                        stats.revenue.thisMonth.toLocaleString()
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Alerts Card -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all p-4 sm:p-6 border-l-4 border-red-500 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <ExclamationTriangleIcon
                                    class="h-6 w-6 text-red-500"
                                />
                                <h3
                                    class="text-base sm:text-lg font-semibold text-gray-800"
                                >
                                    Alerts
                                </h3>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600"
                                    >Pending Disputes</span
                                >
                                <span class="text-red-600 font-bold">5</span>
                            </div>
                            <div
                                class="flex justify-between text-xs sm:text-sm"
                            >
                                <span class="text-gray-600"
                                    >Vendor Applications</span
                                >
                                <span class="text-yellow-600 font-bold">3</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Management -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Vendor Applications -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-800 flex items-center gap-3"
                            >
                                <ClipboardListIcon
                                    class="h-6 w-6 text-orange-500"
                                />
                                Pending Applications
                            </h3>
                            <span
                                class="bg-orange-50 text-orange-600 px-2 py-1 rounded-full text-xs"
                                >3 New</span
                            >
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all"
                            >
                                <div>
                                    <p class="font-medium text-gray-800">
                                        Store Name Example
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Applied: 2 days ago
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        class="px-3 py-1 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition-all"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        class="px-3 py-1 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-all"
                                    >
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Disputes -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-800 flex items-center gap-3"
                            >
                                <RefreshIcon class="h-6 w-6 text-red-500" />
                                Recent Disputes
                            </h3>
                            <span
                                class="bg-red-50 text-red-600 px-2 py-1 rounded-full text-xs"
                                >5 Pending</span
                            >
                        </div>
                        <div class="space-y-4">
                            <div
                                class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all"
                            >
                                <div class="flex justify-between items-center">
                                    <p class="font-medium text-gray-800">
                                        Order #12345
                                    </p>
                                    <span
                                        class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full text-xs"
                                        >Pending Review</span
                                    >
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    Reported: 1 day ago
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top Performing Products -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-800 flex items-center gap-3"
                            >
                                <StarIcon class="h-6 w-6 text-yellow-500" />
                                Top Products
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all"
                            >
                                <div>
                                    <p class="font-medium text-gray-800">
                                        Product Name
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Category
                                    </p>
                                </div>
                                <span class="font-semibold text-green-600"
                                    >$1,234</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Top Vendors -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-800 flex items-center gap-3"
                            >
                                <UsersIcon class="h-6 w-6 text-purple-500" />
                                Top Vendors
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all"
                            >
                                <div>
                                    <p class="font-medium text-gray-800">
                                        Store Name
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        50 orders this month
                                    </p>
                                </div>
                                <span class="font-semibold text-green-600"
                                    >$5,678</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-800 flex items-center gap-3"
                            >
                                <ClipboardListIcon
                                    class="h-6 w-6 text-blue-500"
                                />
                                Quick Actions
                            </h3>
                        </div>
                        <div class="space-y-3">
                            <button
                                class="w-full p-3 text-left rounded-lg hover:bg-gray-50 flex items-center gap-3 group transition-all"
                            >
                                <span
                                    class="group-hover:text-blue-600 text-gray-800"
                                    >Review Applications</span
                                >
                                <span
                                    class="ml-auto bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs"
                                    >3</span
                                >
                            </button>
                            <button
                                class="w-full p-3 text-left rounded-lg hover:bg-gray-50 flex items-center gap-3 group transition-all"
                            >
                                <span
                                    class="group-hover:text-yellow-600 text-gray-800"
                                    >Pending Disputes</span
                                >
                                <span
                                    class="ml-auto bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs"
                                    >5</span
                                >
                            </button>
                            <button
                                class="w-full p-3 text-left rounded-lg hover:bg-gray-50 flex items-center gap-3 group transition-all"
                            >
                                <span
                                    class="group-hover:text-green-600 text-gray-800"
                                    >Process Payouts</span
                                >
                                <span
                                    class="ml-auto bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs"
                                    >12</span
                                >
                            </button>
                        </div>
                    </div>

                    <!-- <div class="p-6 bg-white rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Share Catalog</h3>
                                    <p class="text-sm text-gray-500">On WhatsApp</p>
                                </div>
                            </div>
                        </div> -->

                    <!-- <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            <span class="ml-3 text-gray-700">WhatsApp Notifications</span>
                                        </div>
                                    </a> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Additional responsive design tweaks */
@media (max-width: 640px) {
    .sm\:text-2xl {
        font-size: 1.25rem;
    }
}
</style>
