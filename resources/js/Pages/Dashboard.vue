<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    cryptos: Array
});
</script>

<template>
    <Head title="Dashboard Analyst" />

    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Market Dashboard</h1>
            <p class="text-gray-400">Analisis tren dan sinyal trading otomatis untuk aset cryptocurrency Anda.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Market Cap</div>
                <div class="text-2xl font-bold text-white">$2.34 Trillion</div>
                <div class="text-green-400 text-sm mt-2 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    +2.4%
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Volume 24h</div>
                <div class="text-2xl font-bold text-white">$84.5 Billion</div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Fear & Greed Index</div>
                <div class="text-2xl font-bold text-green-400">72 (Greed)</div>
                <div class="w-full bg-gray-700 rounded-full h-2 mt-3">
                    <div class="bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 h-2 rounded-full" style="width: 72%"></div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Active Signals</div>
                <div class="text-2xl font-bold text-white">{{ cryptos.filter(c => c.recommendation === 'BUY').length }} BUY</div>
                <div class="text-gray-400 text-sm mt-2 font-medium">Dari {{ cryptos.length }} Koin Terpantau</div>
            </div>
        </div>

        <!-- Crypto Analysis Table -->
        <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white">Analisis Teknikal Terkini</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-900/50 text-gray-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Aset</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Harga (USD)</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">24h %</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">RSI</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">MACD</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Trend</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Signal</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Confidence</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="coin in cryptos" :key="coin.id" class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <Link :href="route('coins.show', coin.symbol)" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-xs shadow-inner">
                                        {{ coin.symbol.substring(0,2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white group-hover:text-indigo-300">{{ coin.name }}</div>
                                        <div class="text-xs text-gray-400">{{ coin.symbol }}</div>
                                    </div>
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-white">
                                ${{ parseFloat(coin.current_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold" :class="coin.change_percentage > 0 ? 'text-green-400' : 'text-red-400'">
                                {{ coin.change_percentage > 0 ? '+' : '' }}{{ coin.change_percentage }}%
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span :class="[
                                    'px-2 py-1 rounded text-xs font-bold',
                                    coin.rsi > 70 ? 'text-red-400 bg-red-400/10' : (coin.rsi < 30 ? 'text-green-400 bg-green-400/10' : 'text-gray-300 bg-gray-700')
                                ]">
                                    {{ coin.rsi || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-300">
                                {{ coin.macd || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-bold border',
                                    coin.trend === 'Bullish' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 
                                    (coin.trend === 'Bearish' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-gray-500/10 text-gray-400 border-gray-500/20')
                                ]">
                                    {{ coin.trend || 'Neutral' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span :class="[
                                    'px-3 py-1 rounded-md text-xs font-extrabold uppercase shadow-sm',
                                    coin.recommendation === 'BUY' ? 'bg-green-600 text-white' : 
                                    (coin.recommendation === 'SELL' ? 'bg-red-600 text-white' : 'bg-gray-600 text-white')
                                ]">
                                    {{ coin.recommendation || 'HOLD' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-700 rounded-full h-2">
                                        <div :class="[
                                            'h-2 rounded-full',
                                            coin.confidence > 70 ? 'bg-green-500' : 'bg-yellow-500'
                                        ]" :style="`width: ${coin.confidence || 0}%`"></div>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ coin.confidence || 0 }}%</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!cryptos || cryptos.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data analisis. Silakan jalankan command `php artisan crypto:fetch`
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
