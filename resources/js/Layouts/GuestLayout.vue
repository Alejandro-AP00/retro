<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="w-full lg:grid lg:min-h-[600px] lg:grid-cols-2 xl:min-h-[800px] h-screen">
        <div class="flex items-center justify-center py-12">
            <div class="mx-auto w-[350px]">
                <div class="mb-6">
                    <Link class="flex justify-center" href="/">
                    <ApplicationLogo class="w-20 h-20 text-gray-500 fill-current" />
                    </Link>
                </div>
                <slot />
            </div>
        </div>
        <div class="hidden bg-muted lg:block">
            <img src="/placeholder.svg" alt="Image" width="1920" height="1080"
                class="h-full w-full object-cover dark:brightness-[0.2] dark:grayscale">
        </div>
    </div>
</template>
