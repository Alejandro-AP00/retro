<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
import { Moon, Sun } from 'lucide-vue-next'

import {
    Avatar,
    AvatarFallback,
    AvatarImage,
} from '@/Components/ui/avatar'

import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
    DropdownMenuShortcut,
} from '@/Components/ui/dropdown-menu'

import { Separator } from '@/Components/ui/separator'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
    SidebarProvider,
    SidebarRail,
    SidebarTrigger,
} from '@/Components/ui/sidebar'

import {
    AudioWaveform,
    BadgeCheck,
    Bell,
    BookOpen,
    Bot,
    ChevronRight,
    ChevronsUpDown,
    Command,
    CreditCard,
    Folder,
    Forward,
    Frame,
    GalleryVerticalEnd,
    LogOut,
    Map,
    MoreHorizontal,
    PieChart,
    Plus,
    Settings2,
    Sparkles,
    SquareTerminal,
    Trash2,
    User,
} from 'lucide-vue-next'
import SidebarGroupLabel from '@/Components/ui/sidebar/SidebarGroupLabel.vue';

const showingNavigationDropdown = ref(false);
const $page = usePage()
// You can expand this with more navigation items as needed
const navItems = [
    {
        title: 'Dashboard',
        url: route('dashboard'),
        isActive: route().current('dashboard'),
    },
    {
        title: 'Settings',
        url: route('teams.edit', { team: $page.props.auth.user.current_team }),
        isActive: route().current('teams.edit'),
    },
    // Add more navigation items here
];

const teams = computed(() => {
    return $page.props.auth.user.teams;
})

const currentTeam = computed(() => {
    return $page.props.auth.user.current_team;
})

const switchTeam = async (team) => {
    await router.put(route('current-team.update'), {
        team_id: team.id
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Force reload to update permissions
            window.location.reload()
        }
    })
}
</script>

<template>
    <SidebarProvider>
        <Sidebar collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <SidebarMenuButton size="lg"
                                    class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                                    <div
                                        class="flex items-center justify-center rounded-lg aspect-square size-8 bg-sidebar-primary text-sidebar-primary-foreground">
                                        {{ currentTeam?.name.charAt(0) }}
                                    </div>
                                    <div class="grid flex-1 text-sm leading-tight text-left">
                                        <span class="font-semibold truncate">{{ currentTeam?.name }}</span>
                                    </div>
                                    <ChevronsUpDown class="ml-auto" />
                                </SidebarMenuButton>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                                align="start" side="bottom" :side-offset="4">
                                <DropdownMenuLabel class="text-xs text-muted-foreground">
                                    Teams
                                </DropdownMenuLabel>
                                <DropdownMenuItem v-for="team in teams" :key="team.id" class="gap-2 p-2"
                                    @click="switchTeam(team)">
                                    <div class="flex items-center justify-center border rounded-sm size-6">
                                        {{ team.name.charAt(0) }}
                                    </div>
                                    {{ team.name }}
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem class="gap-2 p-2">
                                    <div
                                        class="flex items-center justify-center border rounded-md size-6 bg-background">
                                        <Plus class="size-4" />
                                    </div>
                                    <div class="font-medium text-muted-foreground">
                                        Add team
                                    </div>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupLabel>Team</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in navItems" :key="item.title">
                            <SidebarMenuButton :class="{ 'bg-sidebar-accent': item.isActive }" as-child>
                                <Link :href="item.url">
                                <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <SidebarMenuButton size="lg"
                                    class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                                    <Avatar>
                                        <AvatarFallback>
                                            {{ $page.props.auth.user.name.charAt(0) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="grid flex-1 text-sm leading-tight text-left">
                                        <span class="font-semibold truncate">{{ $page.props.auth.user.name }}</span>
                                        <span class="text-xs truncate">{{ $page.props.auth.user.email }}</span>
                                    </div>
                                    <ChevronsUpDown class="ml-auto size-4" />
                                </SidebarMenuButton>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56" align="end">
                                <DropdownMenuLabel>My Account</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuGroup>
                                    <DropdownMenuItem as-child>
                                        <Link :href="route('profile.edit')">
                                        <User class="w-4 h-4 mr-2" />
                                        <span>Profile</span>
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link :href="route('logout')" method="post" as="button" class="w-full">
                                        <LogOut class="w-4 h-4 mr-2" />
                                        <span>Log Out</span>
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuGroup>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>

        <SidebarInset>
            <header class="flex items-center h-16 gap-2 shrink-0">
                <div class="flex items-center w-full gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="h-4 mr-2" />
                    <Breadcrumb v-if="$slots.header">
                        <BreadcrumbList>
                            <BreadcrumbItem>
                                <BreadcrumbPage>
                                    <slot name="header" />
                                </BreadcrumbPage>
                            </BreadcrumbItem>
                        </BreadcrumbList>
                    </Breadcrumb>
                    <ThemeSwitcher class="ml-auto" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 pt-0">
                <slot />
            </main>
        </SidebarInset>
    </SidebarProvider>
</template>
