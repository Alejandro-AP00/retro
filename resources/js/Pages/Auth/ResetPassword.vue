<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Reset Password" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Reset Password</h1>
                <p class="text-balance text-muted-foreground">
                    Enter your new password below
                </p>
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
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
                    Reset Password
                </Button>
            </form>
        </div>
    </GuestLayout>
</template>
