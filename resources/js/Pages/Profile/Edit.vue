<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbPage>Profile</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>


        <div class="px-4 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-6">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />

                <UpdatePasswordForm />

                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
