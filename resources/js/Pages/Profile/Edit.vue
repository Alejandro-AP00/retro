<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="leading-tight text-neutral">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="px-4 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="grid gap-6">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />

                    <UpdatePasswordForm />

                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
