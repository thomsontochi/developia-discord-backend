<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-sm text-gray-600">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </div>

            <div class="mt-4 flex items-center justify-between">
                <form @submit.prevent="submit">
                    <div>
                        <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Resend Verification Email
                        </button>
                    </div>
                </form>

                <form @submit.prevent="logout">
                    <button type="submit"
                        class="underline text-sm text-gray-600 hover:text-gray-900">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const logout = () => {
    form.post(route('logout'));
};
</script>