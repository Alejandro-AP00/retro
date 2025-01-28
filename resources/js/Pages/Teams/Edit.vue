<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import TeamMemberList from './Partials/TeamMemberList.vue'
import TeamInviteForm from './Partials/TeamInviteForm.vue'

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
    <AppLayout title="Team Settings">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Team Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="updateTeam" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Team Name
                                </label>
                                <input v-model="form.name" type="text"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    :disabled="form.processing">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <TeamMemberList :team="team" :availableRoles="availableRoles" class="mt-8" />

                <TeamInviteForm :team="team" :availableRoles="availableRoles" class="mt-8" />
            </div>
        </div>
    </AppLayout>
</template>
