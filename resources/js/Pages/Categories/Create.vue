<script setup>
import { Head, useForm , Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    image: null,
    image_url: '',
});

const imagePreview = ref(null);
const fileInput = ref(null);

// Input sanitization
const sanitizeInput = (input) => {
    return input
        .trim()
        .replace(/[<>]/g, '') // Remove < and > to prevent HTML injection
        .replace(/\s+/g, ' '); // Replace multiple spaces with single space
};

// Form validation
const validateForm = () => {
    const errors = {};

    // Name validation
    if (!form.name?.trim()) {
        errors.name = "Category name is required";
    } else if (form.name.length > 255) {
        errors.name = "Category name must be less than 255 characters";
    }

    // Description validation
    if (form.description && form.description.length > 1000) {
        errors.description = "Description must be less than 1000 characters";
    }

    // Image validation
    if (form.image && form.image_url) {
        errors.image = "Please choose either file upload or image URL, not both";
    }

    if (form.image) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!allowedTypes.includes(form.image.type)) {
            errors.image = "Please upload a valid image file (JPEG, PNG, or GIF)";
        }
        if (form.image.size > 2 * 1024 * 1024) { // 2MB limit
            errors.image = "Image size should be less than 2MB";
        }
    }

    if (form.image_url && !isValidUrl(form.image_url)) {
        errors.image_url = "Please enter a valid image URL";
    }

    return errors;
};

const isValidUrl = (url) => {
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
};

const preview = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type and size
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            toast.error("Please upload a valid image file (JPEG, PNG, or GIF)");
            fileInput.value.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            toast.error("Image size should be less than 2MB");
            fileInput.value.value = '';
            return;
        }

        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
        // Clear URL when file is selected
        form.image_url = '';
    }
};

const handleUrlInput = () => {
    if (form.image_url) {
        // Validate URL
        if (!isValidUrl(form.image_url)) {
            toast.error("Please enter a valid URL");
            return;
        }

        // Clear file input when URL is entered
        form.image = null;
        fileInput.value.value = '';
        imagePreview.value = form.image_url;
    }
};

const submit = () => {
    // Sanitize inputs
    form.name = sanitizeInput(form.name);
    if (form.description) {
        form.description = sanitizeInput(form.description);
    }

    // Validate form
    const errors = validateForm();
    if (Object.keys(errors).length > 0) {
        Object.entries(errors).forEach(([key, value]) => {
            toast.error(value);
        });
        return;
    }

    form.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Category created successfully');
            form.reset();
            imagePreview.value = null;
        },
        onError: (errors) => {
            Object.entries(errors).forEach(([key, value]) => {
                toast.error(`${key}: ${value}`);
            });
        },
    });
};
</script>

<template>
    <Head title="Create Category" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create category
                </h2>
                <Link
                    :href="route('admin.categories.index')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Go Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    v-model="form.description"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <InputLabel value="Category Image" />
                                <div class="mt-1 flex flex-col space-y-4">
                                    <!-- File Upload -->
                                    <div>
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            @change="preview"
                                            accept="image/*"
                                            class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100"
                                        />
                                        <InputError :message="form.errors.image" class="mt-2" />
                                    </div>

                                    <!-- OR divider -->
                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-gray-500">Or</span>
                                        </div>
                                    </div>

                                    <!-- Image URL -->
                                    <div>
                                        <TextInput
                                            type="url"
                                            placeholder="Enter image URL"
                                            class="mt-1 block w-full"
                                            v-model="form.image_url"
                                            @input="handleUrlInput"
                                        />
                                        <InputError :message="form.errors.image_url" class="mt-2" />
                                    </div>

                                    <!-- Image Preview -->
                                    <div v-if="imagePreview" class="mt-2">
                                        <img :src="imagePreview" alt="Preview" class="w-32 h-32 object-cover rounded-lg" />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end">
                                <PrimaryButton 
                                    class="ml-4" 
                                    :disabled="form.processing"
                                >
                                    Create Category
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>