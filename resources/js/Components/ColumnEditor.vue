<script setup>
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { VueDraggable } from 'vue-draggable-plus'
import { Plus, X, GripVertical } from 'lucide-vue-next'

const props = defineProps({
    errors: {}
})
const columns = defineModel('columns')

const addColumn = () => {
    columns.value.push({ name: '' })
}

const removeColumn = (index) => {
    columns.value.splice(index, 1)
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <Label>Columns</Label>
            <Button type="button" variant="outline" size="sm" @click="addColumn">
                <Plus class="mr-2 size-4" />
                Add Column
            </Button>
        </div>
        <VueDraggable v-model="columns" handle=".column-handle" class="space-y-2">
            <div v-for="(column, index) in columns">
                <div class="flex items-center gap-2">
                    <div class="p-2 cursor-move column-handle">
                        <GripVertical class="size-4 text-muted-foreground" />
                    </div>
                    <Input v-model="column.name" :class="{ 'border-destructive': errors[`columns.${index}.name`] }"
                        placeholder="Column name" />
                    <Button type="button" variant="ghost" size="icon" @click="removeColumn(index)"
                        :disabled="columns.length === 1">
                        <X class="size-4" />
                    </Button>
                </div>
                <small v-if="errors[`columns.${index}.name`]" class="ml-10 text-destructive-text">{{
                    errors[`columns.${index}.name`] }}</small>
            </div>
        </VueDraggable>
    </div>
</template>
