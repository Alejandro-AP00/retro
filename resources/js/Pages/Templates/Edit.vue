<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    template: Object
})

const form = useForm({
    name: props.template.name,
    description: props.template.description,
    columns: props.template.columns
})
</script>

<template>

    <Head title="Edit Board Template" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Board Template
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Form implementation -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
