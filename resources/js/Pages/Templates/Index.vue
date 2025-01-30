<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Pencil, Trash2 } from 'lucide-vue-next'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog'
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'
const { toast } = useToast()
defineProps({
    templates: Array
})

const form = useForm({})

const deleteTemplate = (id) => {
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
</script>

<template>

    <Head title="Board Templates" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem class="hidden md:block">
                        <BreadcrumbPage>Templates</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>


        <div class="flex flex-col justify-between mb-4 space-y-6 lg:flex-row lg:items-center lg:space-y-2">
            <h2 class="text-3xl font-semibold tracking-tight">Templates</h2>
            <Button asChild>
                <Link :href="route('templates.create')">
                Create Template
                </Link>
            </Button>
        </div>
        <Card>
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
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="icon">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-[425px]">
                                        <DialogHeader>
                                            <DialogTitle>Delete Template?
                                            </DialogTitle>
                                            <DialogDescription>
                                                Once you delete this you won't be able to recover this template and
                                                you will
                                                need to make a new one
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <DialogClose>
                                                <Button variant="destructive" type="button"
                                                    @click="deleteTemplate(template.id)">
                                                    Delete
                                                </Button>
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
