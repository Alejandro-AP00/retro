<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { useToast } from '@/Components/ui/toast'
import ColumnEditor from '@/Components/ColumnEditor.vue'


const props = defineProps({
    template: Object
})


const { toast } = useToast()

const form = useForm({
    name: props.template.name,
    description: props.template.description,
    columns: props.template.columns
})
const submit = () => {
    form.put(route('templates.update', props.template.id), {
        onSuccess: () => {
            toast({
                title: 'Template Created',
                description: `Board template has been updated successfully.`,
            })
        },
        onError: (error) => {
            toast({
                title: "Error",
                description: `There was a problem updating the template.`,
                variant: "destructive",
            })
        }
    })
}
</script>

<template>

    <Head title="Edit Board Template" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem class="hidden md:block">
                        <BreadcrumbLink as-child>
                            <Link :href="route('templates.index')">
                            Templates
                            </Link>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>{{ template.name }}</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>

        <div class="flex flex-col justify-between mb-4 space-y-6 lg:flex-row lg:items-center lg:space-y-2">
            <h2 class="text-3xl font-semibold tracking-tight">Edit {{ template.name }}</h2>
            <div class="flex items-center">
                <Button variant="default" type="submit" @click="submit" :disable="form.processing">
                    Save
                </Button>
            </div>
        </div>

        <Card>
            <CardContent class="p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" :class="{ 'border-destructive': form.errors?.name }" />
                        <small v-if="form.errors?.name" class="text-destructive-text">{{ form.errors?.name }}</small>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description"
                            :class="{ 'border-destructive': form.errors?.description }" />
                        <small v-if="form.errors?.description" class="text-destructive-text">{{ form.errors?.description
                            }}</small>
                    </div>

                    <ColumnEditor v-model:columns="form.columns" :errors="form.errors"></ColumnEditor>
                </form>
            </CardContent>
        </Card>

    </AuthenticatedLayout>
</template>
