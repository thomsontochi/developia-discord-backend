<!-- ProductForm.vue -->
<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
    product: {
        type: Object,
        required: false,
        default: () => ({
            name: "",
            price: "",
            stock: "",
            image: "",
            is_active: "",
            sizes: [],
            colors: [],
            category_id: "",
            slug: "",
            status: "",
            description: "",
        }),
    },
    categories: {
        type: Array,
        required: true,
        default: () => [],
    },
    mode: {
        type: String,
        required: true,
        validator: (value) => ["create", "edit"].includes(value),
    },
});

const form = useForm({
    name: props.product?.name || "",
    description: props.product?.description || "",
    price: props.product?.price || "",
    stock: props.product?.stock || "",
    image: props.product?.image || "",
    is_active: props.product?.is_active ?? true,
    sizes: props.product?.sizes || [],
    colors: props.product?.colors || [],
    category_id: props.product?.category_id || "",
    // slug: props.product?.slug || "",
    status: props.product?.status || "active",
});

// Add available options for sizes and colors
const availableSizes = ["XS", "S", "M", "L", "XL", "XXL"];
const availableColors = ["Red", "Blue", "Green", "Black", "White", "Yellow"];
const availableStatuses = ["active", "draft", "archived"];

// Initialize form with product data if in edit mode
onMounted(() => {
    if (props.mode === "edit" && props.product) {
        form.name = props.product.name;
        form.description = props.product.description;
        form.price = props.product.price;
        form.stock = props.product.stock;
        form.image = props.product.image;
        form.is_active = props.product.is_active ?? true;
        form.sizes = Array.isArray(props.product.sizes)
            ? props.product.sizes
            : [];
        form.colors = Array.isArray(props.product.colors)
            ? props.product.colors
            : [];
        form.category_id = props.product.category_id;
        form.slug = props.product.slug;
        form.status = props.product.status || "active";
    }
});

const imageError = ref(false);

const handleImageError = () => {
    imageError.value = true;
};

const validateForm = () => {
    const errors = {};

    if (!form.name?.trim()) {
        errors.name = "Product name is required";
    }

    if (!form.description?.trim()) {
        errors.description = "Product description is required";
    }

    if (!form.price || isNaN(form.price) || form.price <= 0) {
        errors.price = "Please enter a valid price";
    }

    if (!form.stock || isNaN(form.stock) || form.stock < 0) {
        errors.stock = "Please enter a valid stock amount";
    }

    if (!form.is_active) {
        errors.is_active = "Please select an active status";
    }

    if (!form.category_id) {
        errors.category_id = "Please select a category";
    }

    // if (!form.slug?.trim()) {
    //     errors.slug = "Product slug is required";
    // }

    if (!form.status?.trim()) {
        errors.status = "Product status is required";
    }

    if (!form.sizes?.length) {
        errors.sizes = "Please select at least one size";
    }

    if (!form.colors?.length) {
        errors.colors = "Please select at least one color";
    }

    return errors;
};

const submit = () => {
    const errors = validateForm();
    if (Object.keys(errors).length > 0) {
        Object.entries(errors).forEach(([key, value]) => {
            toast.error(value);
        });
        return;
    }

    const submitConfig = {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                `Product ${
                    props.mode === "create" ? "created" : "updated"
                } successfully`,
                {
                    timeout: 2000,
                    position: "top-right",
                }
            );
        },
        onError: (errors) => {
            Object.entries(errors).forEach(([key, value]) => {
                toast.error(`${key}: ${value}`, {
                    timeout: 3000,
                    position: "top-right",
                });
            });
            form.setError(errors);
        },
    };

    if (props.mode === "create") {
        form.post(route("products.store"), submitConfig);
    } else {
        form.put(route("products.update", props.product.id), submitConfig);
    }
};

const toggleSize = (size) => {
    if (!Array.isArray(form.sizes)) {
        form.sizes = [];
    }
    const index = form.sizes.indexOf(size);
    if (index === -1) {
        form.sizes.push(size);
    } else {
        form.sizes.splice(index, 1);
    }
};

const toggleColor = (color) => {
    if (!Array.isArray(form.colors)) {
        form.colors = [];
    }
    const index = form.colors.indexOf(color);
    if (index === -1) {
        form.colors.push(color);
    } else {
        form.colors.splice(index, 1);
    }
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <!-- Basic Information -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Basic Information
                    </h3>
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Product Name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Description</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            />
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Image URL</label
                            >
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input
                                    v-model="form.image"
                                    type="url"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': form.errors.image,
                                    }"
                                    placeholder="https://example.com/image.jpg"
                                />
                            </div>
                            <p
                                v-if="form.errors.image"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.image }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Enter a valid image URL. Supported formats: JPG,
                                PNG, WebP
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Category</label
                            >
                            <select
                                v-model="form.category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{
                                    'border-red-500': form.errors.category_id,
                                }"
                            >
                                <option value="">Select a category</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.category_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.category_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pricing and Inventory -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Pricing & Inventory
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Price -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Price</label
                            >
                            <div class="relative mt-1">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"
                                    >$</span
                                >
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': form.errors.price,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.price"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.price }}
                            </p>
                        </div>

                        <!-- Stock -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Stock</label
                            >
                            <input
                                v-model="form.stock"
                                type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :class="{ 'border-red-500': form.errors.stock }"
                            />
                            <p
                                v-if="form.errors.stock"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.stock }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Variants -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Product Variants
                    </h3>

                    <!-- Sizes -->
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Available Sizes</label
                        >
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="size in availableSizes"
                                :key="size"
                                type="button"
                                class="px-4 py-2 rounded-md text-sm"
                                :class="
                                    Array.isArray(form.sizes) &&
                                    form.sizes.includes(size)
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                "
                                @click="toggleSize(size)"
                            >
                                {{ size }}
                            </button>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Available Colors</label
                        >
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="color in availableColors"
                                :key="color"
                                type="button"
                                class="px-4 py-2 rounded-md text-sm flex items-center gap-2"
                                :class="
                                    Array.isArray(form.colors) &&
                                    form.colors.includes(color)
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                "
                                @click="toggleColor(color)"
                            >
                                <span
                                    class="w-3 h-3 rounded-full"
                                    :style="{
                                        backgroundColor: color.toLowerCase(),
                                    }"
                                ></span>
                                {{ color }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status and Visibility -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Status & Visibility
                    </h3>
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Product Status</label
                            >
                            <select
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option
                                    v-for="status in availableStatuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{
                                        status.charAt(0).toUpperCase() +
                                        status.slice(1)
                                    }}
                                </option>
                            </select>
                        </div>

                        <!-- Is Active Toggle -->
                        <div class="flex items-center">
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                :class="
                                    form.is_active
                                        ? 'bg-indigo-600'
                                        : 'bg-gray-200'
                                "
                            >
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="
                                        form.is_active
                                            ? 'translate-x-5'
                                            : 'translate-x-0'
                                    "
                                />
                            </button>
                            <span class="ml-3 text-sm">
                                {{ form.is_active ? "Active" : "Inactive" }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :disabled="form.processing"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        {{
                            form.processing
                                ? "Processing..."
                                : mode === "create"
                                ? "Create Product"
                                : "Update Product"
                        }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Image Preview Section -->

        <div class="space-y-6">
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Product Preview
                    </h3>
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 shadow-lg"
                    >
                        <div class="relative group">
                            <!-- Main Preview Container -->
                            <div
                                class="aspect-square rounded-lg overflow-hidden bg-white shadow-inner border-2 border-gray-100"
                            >
                                <img
                                    v-if="form.image && !imageError"
                                    :src="form.image"
                                    @error="handleImageError"
                                    class="w-full h-full object-contain transform transition-transform duration-300 group-hover:scale-105"
                                    alt="Product preview"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-6"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-16 w-16 mb-4 text-gray-300"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span class="text-sm font-medium">
                                        {{
                                            imageError
                                                ? "Failed to load image"
                                                : "No image preview available"
                                        }}
                                    </span>
                                    <span class="text-xs mt-2 text-gray-400">
                                        {{
                                            imageError
                                                ? "Please check the URL and try again"
                                                : "Enter an image URL to preview"
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Image Details Card -->
                            <div
                                class="mt-4 bg-white rounded-lg p-4 shadow-md border border-gray-100"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-sm font-medium text-gray-700"
                                        >Image Status</span
                                    >
                                    <span
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="
                                            form.image && !imageError
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800'
                                        "
                                    >
                                        {{
                                            form.image && !imageError
                                                ? "Active"
                                                : "Pending"
                                        }}
                                    </span>
                                </div>

                                <!-- URL Display -->
                                <div class="mt-3 relative">
                                    <div class="text-xs text-gray-500 mb-1">
                                        Image URL
                                    </div>
                                    <div
                                        class="flex items-center bg-gray-50 rounded-md p-2 break-all"
                                    >
                                        <span
                                            class="text-xs text-gray-600 truncate flex-1"
                                        >
                                            {{
                                                form.image || "No URL provided"
                                            }}
                                        </span>
                                        <span
                                            v-if="form.image"
                                            class="ml-2 text-xs text-indigo-600 cursor-pointer hover:text-indigo-800"
                                            @click="
                                                () =>
                                                    navigator.clipboard.writeText(
                                                        form.image
                                                    )
                                            "
                                        >
                                            Copy
                                        </span>
                                    </div>
                                </div>

                                <!-- Image Tips -->
                                <div class="mt-4 text-xs text-gray-500">
                                    <p class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        Recommended image size: 800x800 pixels
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
