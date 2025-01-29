<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Plus, ArrowRight } from 'lucide-vue-next'

defineProps({
    boards: Array
})
</script>

<template>

    <Head title="My Boards" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                My Boards
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Boards</CardTitle>
                            <Button asChild>
                                <Link :href="route('boards.create')">
                                <Plus class="mr-2 size-4" />
                                Create Board
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <Card v-for="board in boards" :key="board.id">
                                <CardHeader>
                                    <CardTitle>{{ board.name }}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p class="text-sm text-muted-foreground">
                                        {{ board.description }}
                                    </p>
                                    <p class="mt-2 text-sm text-muted-foreground">
                                        {{ board.columns.length }} columns
                                    </p>
                                </CardContent>
                                <div class="p-6 pt-0">
                                    <Button variant="secondary" asChild class="w-full">
                                        <Link :href="route('boards.show', board.id)">
                                        <ArrowRight class="mr-2 size-4" />
                                        Open Board
                                        </Link>
                                    </Button>
                                </div>
                            </Card>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
