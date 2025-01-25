<script setup lang="ts">
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Profile Information</CardTitle>
            <CardDescription>
                Update your account's profile information and email address.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="form.patch(route('profile.update'))" class="grid gap-4">
                <div class="grid gap-2">
                    <label for="name" class="font-medium">Name</label>
                    <Input id="name" type="text" v-model="form.name" required autofocus autocomplete="name" />
                    <span v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</span>
                </div>

                <div class="grid gap-2">
                    <label for="email" class="font-medium">Email</label>
                    <Input id="email" type="email" v-model="form.email" required autocomplete="username" />
                    <span v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</span>
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null" class="text-sm text-muted-foreground">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="underline hover:text-foreground">
                    Click here to re-send the verification email.
                    </Link>

                    <p v-show="status === 'verification-link-sent'" class="mt-2 text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </p>
                </div>
            </form>
        </CardContent>

        <CardFooter class="px-6 py-4 border-t">
            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing" @click="form.patch(route('profile.update'))">
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
