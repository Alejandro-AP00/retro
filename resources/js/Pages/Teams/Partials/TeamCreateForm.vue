<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Button } from '@/Components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import {
    Plus,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { DropdownMenuItem } from '@/Components/ui/dropdown-menu'
const { toast } = useToast()

const form = useForm({
    name: '',
})

const open = ref(false);
const submit = () => {
    form.post(route('teams.store'), {
        fresh: true,
        onSuccess: () => {
            toast({
                title: 'Success',
                description: 'Team created successfully.',
            });
            open.value = false;
        },
        onError: () => {
            toast({
                title: 'Error',
                description: 'Failed to create team.',
                variant: 'destructive'
            })
        }
    })
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as="button"
            class="w-full relative flex cursor-default select-none items-center rounded-sm gap-2 px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50  [&>svg]:size-4 [&>svg]:shrink-0">
            <div class="flex items-center justify-center border rounded-md size-6 bg-background">
                <Plus class="size-4" />
            </div>
            <div class="font-medium text-muted-foreground">
                Add team
            </div>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Create New Team</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Team Name</Label>
                    <Input id="name" v-model="form.name" :class="{ 'border-destructive': form.errors?.name }" />
                    <small v-if="form.errors?.name" class="text-destructive-text">{{ form.errors?.name
                        }}</small>
                </div>
            </form>
            <DialogFooter>
                <Button type="submit" @click="submit" :disabled="form.processing">
                    Create Team
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
