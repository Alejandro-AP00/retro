<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, onUnmounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    board: Object,
    auth: Object
})

const replyForm = useForm({
    content: '',
    column_id: null
})

let echo = null

onMounted(() => {
    echo = window.Echo.join(`board.${props.board.id}`)
        .here((users) => {
            console.log('Present users:', users)
        })
        .joining((user) => {
            console.log('User joined:', user)
        })
        .leaving((user) => {
            console.log('User left:', user)
        })
        .listen('ReplyCreated', (e) => {
            const column = props.board.columns.find(c => c.id === e.reply.column_id)
            if (column) {
                column.replies.push(e.reply)
            }
        })
        .listen('ReplyDeleted', (e) => {
            props.board.columns.forEach(column => {
                const index = column.replies.findIndex(r => r.id === e.replyId)
                if (index !== -1) {
                    column.replies.splice(index, 1)
                }
            })
        })
        .listen('ReplyVoteUpdated', (e) => {
            props.board.columns.forEach(column => {
                const reply = column.replies.find(r => r.id === e.replyId)
                if (reply) {
                    reply.votes = e.votes
                }
            })
        })
})

onUnmounted(() => {
    if (echo) {
        echo.leaveChannel()
    }
})

const submitReply = async (columnId) => {
    try {
        const response = await axios.post(route('replies.store'), {
            content: replyForm.content,
            column_id: columnId
        })

        const column = props.board.columns.find(c => c.id === columnId)
        if (column) {
            column.replies.push(response.data.reply)
        }

        replyForm.reset()
    } catch (error) {
        console.error('Error submitting reply:', error)
    }
}

const deleteReply = async (replyId) => {
    if (!confirm('Are you sure you want to delete this reply?')) return

    try {
        await axios.delete(route('replies.destroy', replyId))

        props.board.columns.forEach(column => {
            const index = column.replies.findIndex(r => r.id === replyId)
            if (index !== -1) {
                column.replies.splice(index, 1)
            }
        })
    } catch (error) {
        console.error('Error deleting reply:', error)
    }
}

const toggleVote = async (replyId) => {
    try {
        const response = await axios.post(route('replies.votes.store', replyId))

        props.board.columns.forEach(column => {
            const reply = column.replies.find(r => r.id === replyId)
            if (reply) {
                reply.votes = response.data.votes
            }
        })
    } catch (error) {
        console.error('Error toggling vote:', error)
    }
}

const canDeleteReply = (reply) => {
    return props.auth.user.id === reply.user_id ||
        props.auth.user.id === props.board.owner_id
}
</script>

<template>

    <Head :title="board.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ board.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div v-for="column in board.columns" :key="column.id"
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold">{{ column.name }}</h3>

                            <!-- Replies -->
                            <div class="space-y-4">
                                <div v-for="reply in column.replies" :key="reply.id" class="p-4 rounded bg-gray-50">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">
                                            {{ reply.user.name }}
                                        </span>
                                        <button v-if="canDeleteReply(reply)" @click="deleteReply(reply.id)"
                                            class="text-sm text-red-600">
                                            Delete
                                        </button>
                                    </div>
                                    <p class="mt-2">{{ reply.content }}</p>
                                    <div class="flex items-center mt-2">
                                        <button @click="toggleVote(reply.id)"
                                            :class="{ 'text-blue-600': reply.votes.some(vote => vote.user_id === auth.user.id) }"
                                            class="flex items-center space-x-1">
                                            <span>👍</span>
                                            <span>{{ reply.votes.length }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Form -->
                            <form @submit.prevent="submitReply(column.id)" class="mt-4">
                                <textarea v-model="replyForm.content" :disabled="board.locked_at"
                                    class="w-full border-gray-300 rounded-md shadow-sm" rows="3"
                                    placeholder="Add a reply..."></textarea>
                                <button :disabled="board.locked_at || replyForm.processing" type="submit"
                                    class="px-4 py-2 mt-2 text-white bg-blue-500 rounded disabled:opacity-50">
                                    Add Reply
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
