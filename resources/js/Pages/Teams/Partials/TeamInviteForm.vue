<script setup>
import { useForm } from '@inertiajs/vue3'

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
        onSuccess: () => form.reset()
    })
}
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Invite Team Member</h3>

            <form @submit.prevent="sendInvite" class="mt-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <input v-model="form.email" type="email"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Role
                    </label>
                    <select v-model="form.role"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                            {{ role.name }}
                        </option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :disabled="form.processing">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
