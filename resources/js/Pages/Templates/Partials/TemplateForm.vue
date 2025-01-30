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
import ColumnEditor from '@/Components/ColumnEditor.vue'

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
        <CardContent class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" />
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="form.description" />
                </div>

                <ColumnEditor v-model:columns="form.columns"></ColumnEditor>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ mode === 'create' ? 'Create' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
