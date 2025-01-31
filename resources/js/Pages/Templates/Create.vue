<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
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

const { toast } = useToast()
const $page = usePage();

const form = useForm({
    name: '',
    description: '',
    columns: [{ name: '' }]
})
const submit = () => {
    form.post(route('templates.store'), {
        onSuccess: () => {
            toast({
                title: 'Template Created',
                description: `Board template has been created successfully.`,
            })
        },
        onError: (error) => {
            toast({
                title: "Error",
                description: `There was a problem creating the template.`,
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
                            <Link :href="route('templates.index')">
                            Templates
                            </Link>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Create Template</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between mb-4 space-y-6 lg:flex-row lg:items-center lg:space-y-2">
                <h2 class="text-3xl font-semibold tracking-tight">Create Template</h2>
                <div class="flex items-center">
                    <Button variant="default" type="submit" @click="submit" :disable="form.processing">
                        Create
                    </Button>
                </div>
            </div>
            <Card>
                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" :class="{ 'border-destructive': form.errors?.name }" />
                            <small v-if="form.errors?.name" class="text-destructive-text">{{ form.errors?.name
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description"
                                :class="{ 'border-destructive': form.errors?.description }" />
                            <small v-if="form.errors?.description" class="text-destructive-text">{{
                                form.errors?.description
                                }}</small>
                        </div>

                        <ColumnEditor v-model:columns="form.columns" :errors="form.errors"></ColumnEditor>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
