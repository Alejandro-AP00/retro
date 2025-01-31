<script setup>
import { Head, usePage, Link } from '@inertiajs/vue3'
import { useToast } from '@/Components/ui/toast/use-toast'
import { onMounted, onUnmounted, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Textarea } from '@/Components/ui/textarea'
import { ThumbsUp, Trash2, CheckCircle2, Edit, Lock, BookCheck, Ellipsis } from 'lucide-vue-next'
import { VueDraggable } from 'vue-draggable-plus'
import {
    Avatar,
    AvatarFallback,
    AvatarImage,
} from '@/Components/ui/avatar'

import axios from 'axios'
import { Badge } from '@/Components/ui/badge'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import {
    Breadcrumb,
    BreadcrumbEllipsis,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

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
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink>
                            {{ $page.props.auth.user.current_team.name }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbLink as-child>
                            <Link :href="route('templates.index')">
                            Boards
                            </Link>
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage class="flex items-center gap-2">
                            {{ board.name }}
                            <Button size="icon" variant="ghost">
                                <Edit class="size-4" />
                            </Button>
                        </BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>

        <div class="h-full">
            <div class="h-full mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid h-full grid-cols-1 gap-6 md:grid-cols-3">
                    <Card v-for="column in board.columns" :key="column.id" class="flex flex-col">
                        <CardHeader class="flex flex-row items-center p-4">
                            <CardTitle class="text-xl ">{{ column.name }}</CardTitle>
                            <Button class="ml-2" size="icon" variant="ghost">
                                <Edit class="size-4" />
                            </Button>
                            <Badge class="ml-auto" variant="secondary">{{ column.replies.length }}</Badge>
                        </CardHeader>
                        <CardContent class="flex-1 px-4">
                            <form @submit.prevent="submitReply(column.id)" class="mb-12">
                                <Textarea v-model="replyContents[column.id]" :disabled="board.locked_at"
                                    placeholder="Add a reply..." class="mb-2 resize-none" />
                                <Button type="submit"
                                    :disabled="board.locked_at || isSubmitting || !replyContents[column.id]"
                                    class="w-full">
                                    Add Reply
                                </Button>
                            </form>

                            <VueDraggable v-model="column.replies" group="replies"
                                @update="(e) => handleReplyMove(e, column.id)"
                                @add="(e) => handleReplyMove(e, column.id)" :disabled="board.locked_at"
                                class="w-full h-full space-y-4 min-h-32">
                                <Card v-for="reply in column.replies" :key="reply.id" variant="secondary"
                                    :class="{ 'bg-green-950/25': isReplyRead(reply) }">
                                    <CardContent class="p-0">
                                        <div class="flex items-center pl-3 border-b">
                                            <Avatar class="mr-2 size-4">
                                                <AvatarImage :src="$page.props.auth.user.avatar" alt="@radix-vue" />
                                                <AvatarFallback>
                                                    {{ $page.props.auth.user.name.charAt(0) }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <span class="text-sm text-muted-foreground">
                                                {{ reply.user.name }}
                                            </span>
                                            <span v-if="isReplyRead(reply)"
                                                class="px-2 ml-2 text-sm text-white bg-green-600 rounded dark:text-green-400 dark:bg-green-950">
                                                Read
                                            </span>
                                            <Button class="ml-auto" variant="ghost" size="icon"
                                                @click="markAsRead(reply)">
                                                <BookCheck />
                                            </Button>
                                            <DropdownMenu v-if="canDeleteReply(reply)">
                                                <DropdownMenuTrigger as-child>
                                                    <Button variant="ghost" size="icon">
                                                        <Ellipsis />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent>
                                                    <DropdownMenuItem @click="deleteReply(reply.id)">
                                                        <Trash2 class="size-4 text-destructive" />
                                                        Delete
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </div>
                                        <p class="px-3 mt-2">{{ reply.content }}</p>
                                        <div class="flex items-center justify-between pb-2 mt-4">
                                            <Button class="gap-0" variant="ghost" size="sm"
                                                @click="toggleVote(reply.id)">
                                                <ThumbsUp class="mr-2 size-4"
                                                    :class="{ 'fill-white': reply.votes.some(vote => vote.id == $page.props.auth.user.id) }" />
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
