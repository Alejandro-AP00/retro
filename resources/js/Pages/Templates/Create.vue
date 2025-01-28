<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Plus, X, GripVertical } from 'lucide-vue-next'
import { VueDraggable } from 'vue-draggable-plus'

const { toast } = useToast()

const form = useForm({
    name: '',
    description: '',
    columns: [{ name: '' }]
})

const addColumn = () => {
    form.columns.push({ name: '' })
}

const removeColumn = (index) => {
    form.columns.splice(index, 1)
}

const submit = () => {
    form.post(route('templates.store'), {
        onSuccess: () => {
            toast({
                title: "Template Created",
                description: "Board template has been created successfully.",
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: "There was a problem creating the template.",
                variant: "destructive",
            })
        }
    })
}
</script>

<template>

    <Head title="Create Board Template" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Create Board Template
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>New Template</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="form.description" />
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <Label>Columns</Label>
                                    <Button type="button" variant="outline" size="sm" @click="addColumn">
                                        <Plus class="mr-2 size-4" />
                                        Add Column
                                    </Button>
                                </div>

                                <VueDraggable v-model="form.columns" handle=".column-handle" item-key="name"
                                    class="space-y-2">
                                    <template #item="{ element, index }">
                                        <div class="flex items-center gap-2">
                                            <div class="p-2 cursor-move column-handle">
                                                <GripVertical class="size-4 text-muted-foreground" />
                                            </div>
                                            <Input v-model="element.name" placeholder="Column name" />
                                            <Button type="button" variant="ghost" size="icon"
                                                @click="removeColumn(index)" :disabled="form.columns.length === 1">
                                                <X class="size-4" />
                                            </Button>
                                        </div>
                                    </template>
                                </VueDraggable>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">
                                    Create Template
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
