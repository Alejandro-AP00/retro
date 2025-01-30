<script setup>
import { Head, usePage, Link } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { onMounted, onUnmounted, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Textarea } from '@/Components/ui/textarea'
import { ThumbsUp, Trash2, CheckCircle2, Edit, Lock } from 'lucide-vue-next'
import { VueDraggable } from 'vue-draggable-plus'
import axios from 'axios'

const props = defineProps({
    board: {
        type: Object,
        required: true
    },
})

const $page = usePage()
const { toast } = useToast()
const replyContents = ref({})
const isSubmitting = ref(false)

let echo = null

onMounted(() => {
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
        .listen('ReplyMoved', (e) => {
            // Remove from old column
            const oldColumn = props.board.columns.find(c => c.id === e.oldColumnId)
            if (oldColumn) {
                const index = oldColumn.replies.findIndex(r => r.id === e.reply.id)
                if (index !== -1) {
                    oldColumn.replies.splice(index, 1)
                }
            }

            // Add to new column at correct position
            const newColumn = props.board.columns.find(c => c.id === e.newColumnId)
            if (newColumn) {
                newColumn.replies.splice(e.newOrder, 0, e.reply)
            }
        })
})

onUnmounted(() => {
    if (echo) {
        window.Echo.leave(`board.${props.board.id}`)
    }
})

const handleReplyMove = async (event, columnId) => {
    await updateReplyPosition(event.data.id, columnId, event.newIndex)
    try {
        await axios.post(route('replies.position.update', { reply: event.data.id }), {
            columnId,
            order: event.newIndex
        })
    } catch (error) {
        console.log(error);

        toast({
            title: "Error",
            description: "Failed to update reply position.",
            variant: "destructive",
        })
    }
}

const markAsRead = async (reply) => {
    try {
        const response = await axios.post(route('replies.read.store', reply.id))

        props.board.columns.forEach(column => {
            const updatedReply = column.replies.find(r => r.id === reply.id)
            if (updatedReply) {
                updatedReply.is_read = response.data.is_read
            }
        })
    } catch (error) {
        console.log(error);

        toast({
            title: "Error",
            description: "Failed to mark reply as read.",
            variant: "destructive",
        })
    }
}

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

const isReplyRead = (reply) => {
    return reply.is_read
}
</script>

<template>

    <Head :title="board.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ board.name }}
                    <span v-if="board.locked_at" class="ml-2 text-muted-foreground">
                        <Lock class="inline-block size-4" />
                    </span>
                </h2>
                <div class="flex items-center gap-2">
                    <Button variant="outline">
                        <Edit class="mr-2 size-4" />
                        Edit Board
                    </Button>
                </div>
            </div>
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
                                <Button type="submit"
                                    :disabled="board.locked_at || isSubmitting || !replyContents[column.id]"
                                    class="w-full">
                                    Add Reply
                                </Button>
                            </form>

                            <VueDraggable v-model="column.replies" group="replies"
                                @update="(e) => handleReplyMove(e, column.id)"
                                @add="(e) => handleReplyMove(e, column.id)" :disabled="board.locked_at"
                                class="w-full space-y-4 min-h-32">
                                <Card v-for="reply in column.replies" :key="reply.id" variant="secondary"
                                    :class="{ 'border-primary': !isReplyRead(reply) }">
                                    <CardContent class="p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">
                                                {{ reply.user.name }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <Button variant="ghost" size="icon" @click="markAsRead(reply)">
                                                    <CheckCircle2 class="size-4" />
                                                </Button>
                                                <Button v-if="canDeleteReply(reply)" variant="ghost" size="icon"
                                                    @click="deleteReply(reply.id)">
                                                    <Trash2 class="size-4 text-destructive" />
                                                </Button>
                                            </div>
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
                            </VueDraggable>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
