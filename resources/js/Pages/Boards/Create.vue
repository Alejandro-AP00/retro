<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { VueDraggable } from 'vue-draggable-plus'
import { Plus, X, GripVertical } from 'lucide-vue-next'
import { computed, watch } from 'vue'
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

const props = defineProps({
    templates: Array
})
const $page = usePage();

const { toast } = useToast()

const form = useForm({
    name: '',
    description: '',
    board_template_id: '',
    columns: []
})

const selectedTemplate = computed(() => {
    return props.templates.find(t => t.id == form.board_template_id)
})


const addColumn = () => {
    form.columns.push({ name: '' })
}

const removeColumn = (index) => {
    form.columns.splice(index, 1)
}

watch(() => form.board_template_id, (newId) => {
    const template = props.templates.find(t => t.id == newId)
    if (template) {
        form.columns = [...template.columns]
    }
})

const submit = () => {
    form.post(route('boards.store'), {
        onSuccess: () => {
            toast({
                title: "Board Created",
                description: "Board has been created successfully.",
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: "There was a problem creating the board.",
                variant: "destructive",
            })
        }
    })
}
</script>

<template>

    <Head title="Create Board" />

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
                        <BreadcrumbLink as-child>
                            <Link :href="route('boards.index')">
                            Boards
                            </Link>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Create Board</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>New Board</CardTitle>
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

                            <div class="space-y-2">
                                <Label for="template">Template</Label>
                                <Select v-model="form.board_template_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a template" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="template in templates" :key="template.id"
                                            :value="template.id.toString()">
                                            {{ template.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div v-if="selectedTemplate" class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <Label>Columns</Label>
                                    <Button type="button" variant="outline" size="sm" @click="addColumn">
                                        <Plus class="mr-2 size-4" />
                                        Add Column
                                    </Button>
                                </div>
                                <VueDraggable v-model="form.columns" handle=".column-handle" class="space-y-2">
                                    <div v-for="(column, index) in form.columns" :key="index">
                                        <div class="flex items-center gap-2">
                                            <div class="p-2 cursor-move column-handle">
                                                <GripVertical class="size-4 text-muted-foreground" />
                                            </div>
                                            <Input v-model="column.name" placeholder="Column name" />
                                            <Button type="button" variant="ghost" size="icon"
                                                @click="removeColumn(index)" :disabled="form.columns.length === 1">
                                                <X class="size-4" />
                                            </Button>
                                        </div>
                                    </div>
                                </VueDraggable>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">
                                    Create Board
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
