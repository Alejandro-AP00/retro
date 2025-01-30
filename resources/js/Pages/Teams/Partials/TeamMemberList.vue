<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Button } from '@/Components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import Badge from '@/Components/ui/badge/Badge.vue'
import { formatTitle } from 'usemods'

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

const { toast } = useToast()

const updateRoleForm = useForm({
    role: ''
})

const $page = usePage()

const updateRole = (userId) => {
    updateRoleForm.put(route('team-members.update', [props.team.id, userId]), {
        onSuccess: () => {
            toast({
                title: "Role Updated",
                description: "Team member's role has been updated successfully.",
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: "Failed to update member's role.",
                variant: "destructive",
            })
        }
    })
}

const removeMemberForm = useForm({})

const removeMember = (userId) => {
    if (confirm('Are you sure you want to remove this member?')) {
        removeMemberForm.delete(route('team-members.destroy', [props.team.id, userId]), {
            onSuccess: () => {
                toast({
                    title: "Member Removed",
                    description: "Team member has been removed successfully.",
                })
            },
            onError: () => {
                toast({
                    title: "Error",
                    description: "Failed to remove team member.",
                    variant: "destructive",
                })
            }
        })
    }
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Team Members</CardTitle>
        </CardHeader>
        <CardContent>
            <ul role="list" class="divide-y divide-border">
                <li v-for="user in team.users" :key="user.id" class="py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ user.name }} <Badge class="ml-2">{{
                                formatTitle(user.roles[0].name) }}</Badge>
                            </p>
                            <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                        </div>

                        <div class="flex items-center space-x-4">
                            <Select v-if="user.id !== $page.props.auth.user.id" v-model="updateRoleForm.role"
                                @update:modelValue="updateRole(user.id)">
                                <SelectTrigger>
                                    <SelectValue :placeholder="formatTitle(user.role)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="role in availableRoles" :key="role.id" :value="role.name">
                                        {{ formatTitle(role.name) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Button v-if="user.id !== $page.props.auth.user.id" variant="destructive"
                                @click="removeMember(user.id)">
                                Remove
                            </Button>
                        </div>
                    </div>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
