<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: props.invitation?.email || '',
    password: '',
    password_confirmation: '',
    invitation_token: new URLSearchParams(window.location.search).get('invitation'),
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Create an account</h1>
                <p class="text-balance text-muted-foreground">
                    Enter your details below to create your account
                </p>
            </div>

            <div v-if="invitation" class="mb-4 text-sm text-gray-600">
                You've been invited to join {{ invitation.team }}
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" v-model="form.name" required autofocus autocomplete="name" />
                    <span v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" required autocomplete="username"
                        placeholder="m@example.com" />
                    <span v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" v-model="form.password" required autocomplete="new-password" />
                    <span v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <Input id="password_confirmation" type="password" v-model="form.password_confirmation" required
                        autocomplete="new-password" />
                    <span v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </span>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Register
                </Button>
            </form>

            <div class="text-sm text-center">
                Already have an account?
                <Link :href="route('login')" class="underline">
                Sign in
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
