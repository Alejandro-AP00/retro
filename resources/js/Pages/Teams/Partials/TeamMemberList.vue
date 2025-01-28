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

const updateRoleForm = useForm({
    role: ''
})

const updateRole = (userId) => {
    updateRoleForm.put(route('team-members.update', [props.team.id, userId]))
}

const removeMemberForm = useForm({})

const removeMember = (userId) => {
    if (confirm('Are you sure you want to remove this member?')) {
        removeMemberForm.delete(route('team-members.destroy', [props.team.id, userId]))
    }
}
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Team Members</h3>

            <div class="mt-6">
                <ul role="list" class="divide-y divide-gray-200">
                    <li v-for="user in team.users" :key="user.id" class="py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>

                            <div class="flex items-center space-x-4">
                                <select v-if="user.id !== team.owner_id" v-model="updateRoleForm.role"
                                    @change="updateRole(user.id)"
                                    class="block w-full px-3 py-2 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>

                                <button v-if="user.id !== team.owner_id" @click="removeMember(user.id)"
                                    class="text-sm text-red-600 hover:text-red-900">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
