<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Login</h1>
                <p class="text-balance text-muted-foreground">
                    Enter your email below to login to your account
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

                <div class="grid gap-2">
                    <div class="flex items-center">
                        <Label for="password">Password</Label>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="inline-block ml-auto text-sm underline">
                        Forgot your password?
                        </Link>
                    </div>
                    <Input id="password" type="password" v-model="form.password" required
                        autocomplete="current-password" />
                    <span v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</span>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Login
                </Button>
            </form>

            <div class="text-sm text-center">
                Don't have an account?
                <Link :href="route('register')" class="underline">
                Sign up
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
