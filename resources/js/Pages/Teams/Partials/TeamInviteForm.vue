<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { formatTitle } from 'usemods'

const { toast } = useToast()

const props = defineProps({
    team: {
        type: Object,
        required: true
    },
    availableRoles: {
        type: Array,
        required: true
    }
})

const form = useForm({
    email: '',
    role: props.availableRoles[0]?.name || ''
})

const sendInvite = () => {
    form.post(route('team-invitations.store', props.team.id), {
        onSuccess: () => {
            form.reset()
            toast({
                title: "Invitation Sent",
                description: `An invitation has been sent to ${form.email}`,
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: "Failed to send the invitation.",
                variant: "destructive",
            })
        }
    })
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Invite Team Member</CardTitle>
        </CardHeader>
        <CardContent>
            <form @submit.prevent="sendInvite" class="space-y-6">
                <div class="space-y-2">
                    <Label for="email">Email Address</Label>
                    <Input id="email" v-model="form.email" type="email" />
                </div>

                <div class="space-y-2">
                    <Label for="role">Role</Label>
                    <Select v-model="form.role">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="role in availableRoles" :key="role.id" :value="role.name">
                                {{ formatTitle(role.name) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        Send Invitation
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
