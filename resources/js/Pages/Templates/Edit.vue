<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TemplateForm from './Partials/TemplateForm.vue'

defineProps({
    template: Object
})
</script>

<template>

    <Head title="Edit Board Template" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Edit Board Template
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <TemplateForm mode="edit" :template="template" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
