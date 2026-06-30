<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    cryptos: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Crypto Analyzer" />
    <div class="min-h-screen bg-gray-900 text-gray-100 font-sans selection:bg-indigo-500 selection:text-white">
        <!-- Navigation -->
        <nav class="border-b border-gray-800 bg-gray-900/50 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                            C
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400">CryptoAnalyzer</span>
                    </div>
                    <div v-if="canLogin" class="flex gap-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="text-sm font-medium text-gray-300 hover:text-white transition-colors"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm font-medium text-gray-300 hover:text-white transition-colors py-2 px-4"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white rounded-md transition-colors py-2 px-4 shadow-lg shadow-indigo-500/20"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-4">
                    Platform <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Analisis Crypto</span> Profesional
                </h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                    Monitoring harga real-time, analisis teknikal otomatis, prediksi tren cerdas, dan kelola portofolio Anda dalam satu ekosistem canggih.
                </p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link v-if="!$page.props.auth.user" :href="route('register')" class="px-8 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-105">
                        Mulai Trading Sekarang
                    </Link>
                    <Link v-else :href="route('dashboard')" class="px-8 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-105">
                        Buka Dashboard
                    </Link>
                </div>
            </div>

            <!-- Market Overview / Daftar Crypto -->
            <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden backdrop-blur-sm shadow-xl">
                <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Market Overview (Real-time Preview)
                    </h2>
                    <span class="text-xs font-medium px-2.5 py-1 bg-green-500/10 text-green-400 rounded-full border border-green-500/20">
                        Market is Active
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-800/80 text-gray-400">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider">Nama Coin</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider">Symbol</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-right">Harga</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-right">Perubahan 24 Jam</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-right">Volume</th>
                                <th class="px-6 py-4 font-semibold text-xs uppercase tracking-wider text-center">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <tr v-for="(coin, index) in cryptos" :key="coin.id" class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 text-gray-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium">{{ coin.name }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-700 px-2 py-1 rounded text-xs font-bold text-gray-300">{{ coin.symbol }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">{{ coin.price }}</td>
                                <td class="px-6 py-4 text-right font-bold" :class="coin.isUp ? 'text-green-400' : 'text-red-400'">
                                    {{ coin.change }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-400">{{ coin.volume }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-xs font-bold border',
                                        coin.isUp ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'
                                    ]">
                                        {{ coin.trend }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Features Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl hover:border-indigo-500/50 transition-colors">
                    <div class="w-12 h-12 bg-indigo-500/20 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Analisis Teknikal</h3>
                    <p class="text-gray-400 text-sm">Hitung otomatis RSI, MACD, MA, EMA, dan Support/Resistance dengan akurat.</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl hover:border-purple-500/50 transition-colors">
                    <div class="w-12 h-12 bg-purple-500/20 text-purple-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Sistem Prediksi Cerdas</h3>
                    <p class="text-gray-400 text-sm">Gunakan algoritma prediksi tren masa depan berdasarkan data historis mendalam.</p>
                </div>
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl hover:border-green-500/50 transition-colors">
                    <div class="w-12 h-12 bg-green-500/20 text-green-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Manajemen Portofolio</h3>
                    <p class="text-gray-400 text-sm">Pantau keuntungan dan kerugian (ROI) seluruh aset crypto Anda secara real-time.</p>
                </div>
            </div>
        </main>
    </div>
</template>
