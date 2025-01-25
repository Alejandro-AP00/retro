<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/Components/ui/button'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>

        <Head title="Email Verification" />

        <div class="grid gap-6">
            <div class="grid gap-2 text-center">
                <h1 class="text-3xl font-bold">Verify Email</h1>
                <p class="text-balance text-muted-foreground">
                    Thanks for signing up! Before getting started, could you verify your
                    email address by clicking on the link we just emailed to you?
                </p>
            </div>

            <div v-if="verificationLinkSent" class="text-sm font-medium text-green-600">
                A new verification link has been sent to the email address you
                provided during registration.
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div class="flex items-center justify-between gap-4">
                    <Button type="submit" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                        Resend Verification Email
                    </Button>

                    <Button variant="outline" :href="route('logout')" method="post" as="link">
                        Log Out
                    </Button>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
