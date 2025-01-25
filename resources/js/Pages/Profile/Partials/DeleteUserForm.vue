<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/Components/ui/dialog"
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Delete Account</CardTitle>
            <CardDescription>
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Before deleting your account, please download any data or information that you wish to retain.
            </CardDescription>
        </CardHeader>

        <CardFooter class="px-6 py-4 border-t">
            <Dialog :open="confirmingUserDeletion" @update:open="closeModal">
                <DialogTrigger asChild>
                    <Button variant="destructive" @click="confirmUserDeletion">
                        Delete Account
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                        <DialogDescription>
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Please enter your password to confirm you would like to permanently delete your account.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Input id="password" ref="passwordInput" v-model="form.password" type="password"
                                placeholder="Password" @keyup.enter="deleteUser" />
                            <span v-if="form.errors.password" class="text-sm text-red-600">
                                {{ form.errors.password }}
                            </span>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="closeModal">
                            Cancel
                        </Button>
                        <Button variant="destructive" :disabled="form.processing" @click="deleteUser">
                            Delete Account
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </CardFooter>
    </Card>
</template>
