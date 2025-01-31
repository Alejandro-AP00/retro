<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Plus, ArrowRight } from 'lucide-vue-next'
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

defineProps({
    boards: Array
})

const $page = usePage();

</script>

<template>

    <Head title="My Boards" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink>
                            {{ $page.props.auth.user.current_team.name }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Boards</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between mb-4 space-y-6 lg:flex-row lg:items-center lg:space-y-2">
                <h2 class="text-3xl font-semibold tracking-tight">Boards</h2>
                <Button asChild>
                    <Link :href="route('boards.create')">
                    <Plus class="mr-2 size-4" />
                    Create Board
                    </Link>
                </Button>
            </div>
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
                            {{ board.columns.flat().length }} Comments
                        </p>
                    </CardContent>
                    <div class="p-6 pt-0">
                        <Button variant="secondary" asChild class="w-full">
                            <Link :href="route('boards.show', board.id)">
                            Open Board
                            <ArrowRight class="mr-2 size-4" />
                            </Link>
                        </Button>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
