<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Confirm Password" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Confirm Password</h1>
                <p class="text-balance text-muted-foreground">
                    This is a secure area of the application. Please confirm your
                    password before continuing.
                </p>
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" v-model="form.password" required
                        autocomplete="current-password" autofocus />
                    <span v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</span>
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Confirm
                </Button>
            </form>
        </div>
    </GuestLayout>
</template>
