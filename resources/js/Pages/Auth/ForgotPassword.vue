<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>

        <Head title="Forgot Password" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Forgot Password</h1>
                <p class="text-balance text-muted-foreground">
                    Enter your email address and we'll send you a password reset link.
                </p>
            </div>

            <div v-if="status" class="text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                        placeholder="m@example.com" />
                    <span v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</span>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Email Password Reset Link
                </Button>
            </form>
        </div>
    </GuestLayout>
</template>
