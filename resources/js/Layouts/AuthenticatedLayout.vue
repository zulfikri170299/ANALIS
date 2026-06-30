<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 font-sans selection:bg-indigo-500 selection:text-white flex">
        
        <!-- Sidebar -->
        <aside :class="['bg-gray-800 border-r border-gray-700 transition-all duration-300 z-20 flex-shrink-0', sidebarOpen ? 'w-64' : 'w-20']">
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700 bg-gray-900/50">
                <div class="flex items-center gap-2 overflow-hidden" v-show="sidebarOpen">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg flex-shrink-0">
                        C
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400 truncate">CryptoAnalyzer</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white p-1 rounded-md focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <div class="py-4 overflow-y-auto h-[calc(100vh-4rem)]">
                <ul class="space-y-1 px-2">
                    <li>
                        <Link :href="route('dashboard')" :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-colors', $page.component === 'Dashboard' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white']">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span v-show="sidebarOpen">Market Overview</span>
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('alerts.index')" :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-colors', $page.component === 'Alert' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white']">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span v-show="sidebarOpen">Alert & Peringatan</span>
                        </Link>
                    </li>
                    <li>
                        <Link :href="route('portfolio.index')" :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-colors', $page.component === 'Portfolio' ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white']">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span v-show="sidebarOpen">Portofolio Saya</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <nav class="bg-gray-800/80 border-b border-gray-700 backdrop-blur-md sticky top-0 z-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Breadcrumb or page title could go here -->
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <!-- Role Badge -->
                            <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ $page.props.auth.user.role || 'Viewer' }}
                            </span>
                            
                            <!-- Profile Dropdown -->
                            <div class="relative">
                                <Link :href="route('profile.edit')" class="flex items-center gap-2 text-gray-300 hover:text-white focus:outline-none transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center font-bold">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                    <span class="text-sm font-medium">{{ $page.props.auth.user.name }}</span>
                                </Link>
                            </div>

                            <Link :href="route('logout')" method="post" as="button" class="text-gray-400 hover:text-red-400 transition-colors ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
