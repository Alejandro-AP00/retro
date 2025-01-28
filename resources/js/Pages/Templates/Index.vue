<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Pencil, Trash2 } from 'lucide-vue-next'

const { toast } = useToast()
defineProps({
    templates: Array
})

const form = useForm({})

const deleteTemplate = (id) => {
    if (confirm('Are you sure you want to delete this template?')) {
        form.delete(route('templates.destroy', id), {
            onSuccess: () => {
                toast({
                    title: "Template Deleted",
                    description: "Board template has been deleted successfully.",
                })
            },
            onError: () => {
                toast({
                    title: "Error",
                    description: "There was a problem deleting the template.",
                    variant: "destructive",
                })
            }
        })
    }
}
</script>

<template>

    <Head title="Board Templates" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Board Templates
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Templates</CardTitle>
                            <Button asChild>
                                <Link :href="route('templates.create')">
                                Create Template
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Description</TableHead>
                                    <TableHead>Columns</TableHead>
                                    <TableHead class="w-[100px]">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="template in templates" :key="template.id">
                                    <TableCell>{{ template.name }}</TableCell>
                                    <TableCell>{{ template.description }}</TableCell>
                                    <TableCell>{{ template.columns.length }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <Button variant="ghost" size="icon" asChild>
                                                <Link :href="route('templates.edit', template.id)">
                                                <Pencil class="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="destructive" size="icon"
                                                @click="deleteTemplate(template.id)">
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
