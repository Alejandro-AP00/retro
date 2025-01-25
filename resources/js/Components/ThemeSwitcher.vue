<script setup lang="ts">
import { Moon, Sun } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { useDark, useToggle } from '@vueuse/core'

const isDark = useDark()
const toggleDark = useToggle(isDark)
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button class="ml-auto" variant="outline" size="icon">
                <Sun class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                <Moon
                    class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="toggleDark(false)">
                <Sun class="w-4 h-4 mr-2" />
                <span>Light</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="toggleDark(true)">
                <Moon class="w-4 h-4 mr-2" />
                <span>Dark</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
