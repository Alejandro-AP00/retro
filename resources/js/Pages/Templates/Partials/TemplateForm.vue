<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Plus, X, GripVertical } from 'lucide-vue-next'
import { VueDraggable } from 'vue-draggable-plus'

const props = defineProps({
    template: {
        type: Object,
        default: () => ({
            name: '',
            description: '',
            columns: [{ name: '' }]
        })
    },
    mode: {
        type: String,
        default: 'create',
        validator: (value) => ['create', 'edit'].includes(value)
    }
})

const { toast } = useToast()

const form = useForm({
    name: props.template.name,
    description: props.template.description,
    columns: props.template.columns
})

const addColumn = () => {
    form.columns.push({ name: '' })
}

const removeColumn = (index) => {
    form.columns.splice(index, 1)
}

const submit = () => {
    const action = props.mode === 'create'
        ? () => form.post(route('templates.store'))
        : () => form.put(route('templates.update', props.template.id))

    action({
        onSuccess: () => {
            toast({
                title: `Template ${props.mode === 'create' ? 'Created' : 'Updated'}`,
                description: `Board template has been ${props.mode === 'create' ? 'created' : 'updated'} successfully.`,
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: `There was a problem ${props.mode === 'create' ? 'creating' : 'updating'} the template.`,
                variant: "destructive",
            })
        }
    })
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ mode === 'create' ? 'New' : 'Edit' }} Template</CardTitle>
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

                    <VueDraggable v-model="form.columns" handle=".column-handle" class="space-y-2">
                        <div v-for="(column, index) in form.columns">
                            <div class="flex items-center gap-2">
                                <div class="p-2 cursor-move column-handle">
                                    <GripVertical class="size-4 text-muted-foreground" />
                                </div>
                                <Input v-model="column.name" placeholder="Column name" />
                                <Button type="button" variant="ghost" size="icon" @click="removeColumn(index)"
                                    :disabled="form.columns.length === 1">
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </VueDraggable>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ mode === 'create' ? 'Create' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
