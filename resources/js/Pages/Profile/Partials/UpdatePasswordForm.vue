<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Update Password</CardTitle>
            <CardDescription>
                Ensure your account is using a long, random password to stay secure.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="updatePassword" class="grid gap-4">
                <div class="grid gap-2">
                    <label for="current_password" class="font-medium">Current Password</label>
                    <Input id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                        type="password" autocomplete="current-password" />
                    <span v-if="form.errors.current_password" class="text-sm text-red-600">
                        {{ form.errors.current_password }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <label for="password" class="font-medium">New Password</label>
                    <Input id="password" ref="passwordInput" v-model="form.password" type="password"
                        autocomplete="new-password" />
                    <span v-if="form.errors.password" class="text-sm text-red-600">
                        {{ form.errors.password }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <label for="password_confirmation" class="font-medium">Confirm Password</label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password"
                        autocomplete="new-password" />
                    <span v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </span>
                </div>
            </form>
        </CardContent>

        <CardFooter class="px-6 py-4 border-t">
            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing" @click="updatePassword">
                    Save
                </Button>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-muted-foreground">
                        Saved.
                    </p>
                </Transition>
            </div>
        </CardFooter>
    </Card>
</template>
