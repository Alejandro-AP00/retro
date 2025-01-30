<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TeamMemberList from './Partials/TeamMemberList.vue'
import TeamInviteForm from './Partials/TeamInviteForm.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
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
    name: props.team.name
})

const updateTeam = () => {
    form.put(route('teams.update', props.team.id), {
        onSuccess: () => {
            toast({
                title: "Team Updated",
                description: "Team settings have been saved successfully.",
            })
        },
        onError: () => {
            toast({
                title: "Error",
                description: "There was a problem updating the team.",
                variant: "destructive",
            })
        }
    })
}
</script>

<template>
    <AuthenticatedLayout title="Team Settings">
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink>
                            {{ team.name }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Settings</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <CardTitle>Team Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="updateTeam" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Team Name</Label>
                            <Input id="name" v-model="form.name" type="text" />
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                Save
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <TeamMemberList :team="team" :availableRoles="availableRoles" class="mt-8" />
            <TeamInviteForm :team="team" :availableRoles="availableRoles" class="mt-8" />
        </div>
    </AuthenticatedLayout>
</template>
