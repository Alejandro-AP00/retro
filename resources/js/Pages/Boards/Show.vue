<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { onMounted, onUnmounted, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Textarea } from '@/Components/ui/textarea'
import { ThumbsUp, Trash2 } from 'lucide-vue-next'
import axios from 'axios'

const props = defineProps({
    board: {
        type: Object,
        required: true
    },
})

const $page = usePage();

const { toast } = useToast()

// Create a map of column IDs to their reply content
const replyContents = ref({})
const isSubmitting = ref(false)

let echo = null

onMounted(() => {
    // Initialize empty content for each column
    props.board.columns.forEach(column => {
        replyContents.value[column.id] = ''
    })

    echo = window.Echo.join(`board.${props.board.id}`)
        .here((users) => {
            console.log('Present users:', users)
        })
        .joining((user) => {
            toast({
                title: "User Joined",
                description: `${user.name} joined the board`,
            })
        })
        .leaving((user) => {
            toast({
                title: "User Left",
                description: `${user.name} left the board`,
            })
        })
        .listen('ReplyCreated', (e) => {
            const column = props.board.columns.find(c => c.id === e.reply.column_id)
            if (column) {
                column.replies.unshift(e.reply)
            }
        })
        .listen('ReplyDeleted', (e) => {
            props.board.columns.forEach(column => {
                const index = column.replies.findIndex(r => r.id == e.reply)
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
        window.Echo.leave(`board.${props.board.id}`)
    }
})

const submitReply = async (columnId) => {
    if (isSubmitting.value || !replyContents.value[columnId]) return

    isSubmitting.value = true
    try {
        const response = await axios.post(route('replies.store'), {
            content: replyContents.value[columnId],
            column_id: columnId
        })

        // Add the new reply to the top of the column
        const column = props.board.columns.find(c => c.id === columnId)
        if (column) {
            column.replies.unshift(response.data.reply)
        }

        // Clear the input
        replyContents.value[columnId] = ''

        toast({
            title: "Reply Added",
            description: "Your reply has been added successfully.",
        })
    } catch (error) {
        toast({
            title: "Error",
            description: error.response?.data?.message || "Failed to add reply.",
            variant: "destructive",
        })
    } finally {
        isSubmitting.value = false
    }
}

const deleteReply = async (replyId) => {
    if (!confirm('Are you sure you want to delete this reply?')) return

    try {
        await axios.delete(route('replies.destroy', replyId))

        // Remove the reply locally
        props.board.columns.forEach(column => {
            const index = column.replies.findIndex(r => r.id === replyId)
            if (index !== -1) {
                column.replies.splice(index, 1)
            }
        })

        toast({
            title: "Reply Deleted",
            description: "Reply has been deleted successfully.",
        })
    } catch (error) {
        toast({
            title: "Error",
            description: "Failed to delete reply.",
            variant: "destructive",
        })
    }
}

const toggleVote = async (replyId) => {
    try {
        const response = await axios.post(route('replies.votes.store', replyId))

        // Update the votes locally
        props.board.columns.forEach(column => {
            const reply = column.replies.find(r => r.id === replyId)
            if (reply) {
                reply.votes = response.data.votes
            }
        })
    } catch (error) {
        toast({
            title: "Error",
            description: "Failed to update vote.",
            variant: "destructive",
        })
    }
}

const canDeleteReply = (reply) => {
    return $page.props.auth.user.id === reply.user_id ||
        $page.props.auth.user.id === props.board.owner_id
}
</script>

<template>

    <Head :title="board.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                {{ board.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <Card v-for="column in board.columns" :key="column.id">
                        <CardHeader>
                            <CardTitle>{{ column.name }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitReply(column.id)" class="mb-4">
                                <Textarea v-model="replyContents[column.id]" :disabled="board.locked_at"
                                    placeholder="Add a reply..." class="mb-2" />
                                <Button type="submit" class="w-full">
                                    Add Reply
                                </Button>
                            </form>

                            <div class="space-y-4">
                                <Card v-for="reply in column.replies" :key="reply.id" variant="secondary">
                                    <CardContent class="p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">
                                                {{ reply.user.name }}
                                            </span>
                                            <Button v-if="canDeleteReply(reply)" variant="ghost" size="icon"
                                                @click="deleteReply(reply.id)">
                                                <Trash2 class="size-4 text-destructive" />
                                            </Button>
                                        </div>
                                        <p class="mt-2">{{ reply.content }}</p>
                                        <div class="flex items-center mt-2">
                                            <Button variant="ghost" size="sm" @click="toggleVote(reply.id)"
                                                :class="{ 'text-primary': reply.votes.some(vote => vote.user_id === $page.props.auth.user.id) }">
                                                <ThumbsUp class="mr-2 size-4" />
                                                {{ reply.votes.length }}
                                            </Button>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
