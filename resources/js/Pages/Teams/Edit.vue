<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TeamMemberList from './Partials/TeamMemberList.vue'
import TeamInviteForm from './Partials/TeamInviteForm.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'

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
    form.put(route('teams.update', props.team.id))
}
</script>

<template>
    <AuthenticatedLayout title="Team Settings">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-neutral">
                Team Settings
            </h2>
        </template>

        <div class="py-12">
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
        </div>
    </AuthenticatedLayout>
</template>
