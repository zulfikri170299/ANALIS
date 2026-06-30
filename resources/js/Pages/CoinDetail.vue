<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { createChart } from 'lightweight-charts';

const props = defineProps({
    coin: Object,
    chartData: Array,
    currentFilter: String
});

const chartContainer = ref(null);
const chartError = ref('');
let chart = null;
let candlestickSeries = null;

const changeFilter = (filter) => {
    router.get(route('coins.show', props.coin.symbol), { filter }, { preserveState: true, preserveScroll: true });
};

onMounted(() => {
    try {
        if (!chartContainer.value) return;

        chart = createChart(chartContainer.value, {
            layout: {
                background: { type: 'solid', color: '#1f2937' }, // gray-800
                textColor: '#9ca3af', // gray-400
            },
            grid: {
                vertLines: { color: '#374151' }, // gray-700
                horzLines: { color: '#374151' }, // gray-700
            },
            timeScale: {
                borderColor: '#374151',
                timeVisible: true,
            },
            width: chartContainer.value.clientWidth,
            height: 500,
        });

        candlestickSeries = chart.addCandlestickSeries({
            upColor: '#10b981', // green-500
            downColor: '#ef4444', // red-500
            borderVisible: false,
            wickUpColor: '#10b981',
            wickDownColor: '#ef4444',
        });

        candlestickSeries.setData(JSON.parse(JSON.stringify(props.chartData)));
        chart.timeScale().fitContent();

        // Responsive handling
        window.addEventListener('resize', handleResize);
    } catch (e) {
        chartError.value = e.message || e.toString();
        console.error(e);
    }
});

watch(() => props.chartData, (newData) => {
    if (candlestickSeries && newData) {
        try {
            candlestickSeries.setData(JSON.parse(JSON.stringify(newData)));
            chart.timeScale().fitContent();
        } catch (e) {
            console.error('Error updating chart:', e);
        }
    }
}, { deep: true });

const handleResize = () => {
    if (chartContainer.value && chart) {
        chart.applyOptions({ width: chartContainer.value.clientWidth });
    }
};

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    if (chart) {
        chart.remove();
    }
});
</script>

<template>
    <Head :title="`${coin.name} (${coin.symbol}) - Chart`" />

    <AuthenticatedLayout>
        <div class="mb-6 flex justify-between items-end">
            <div>
                <Link :href="route('dashboard')" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium flex items-center gap-1 mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dashboard
                </Link>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-lg text-white shadow-lg">
                        {{ coin.symbol.substring(0,2) }}
                    </div>
                    {{ coin.name }} <span class="text-xl text-gray-400 font-normal">{{ coin.symbol }}</span>
                </h1>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-white">${{ parseFloat(coin.current_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}</div>
                <div :class="['text-sm font-bold', coin.change_percentage > 0 ? 'text-green-400' : 'text-red-400']">
                    {{ coin.change_percentage > 0 ? '+' : '' }}{{ coin.change_percentage }}% (24h)
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart Section -->
            <div class="lg:col-span-2 bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-lg font-bold text-white">Grafik Harga (Candlestick)</h2>
                    <div class="flex bg-gray-900 rounded-lg p-1 border border-gray-700">
                        <button v-for="filter in ['1Hari', '1Minggu', '1Bulan', '1Tahun']" :key="filter"
                            @click="changeFilter(filter)"
                            :class="['px-4 py-1.5 rounded-md text-sm font-medium transition-colors',
                                     currentFilter === filter ? 'bg-indigo-600 text-white shadow' : 'text-gray-400 hover:text-white hover:bg-gray-800']">
                            {{ filter.replace('1', '1 ') }}
                        </button>
                    </div>
                </div>
                
                <div v-if="chartError" class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-lg mb-4 font-mono text-sm">
                    Error rendering chart: {{ chartError }}
                </div>
                <div v-if="!chartData || chartData.length === 0" class="bg-yellow-500/10 border border-yellow-500/50 text-yellow-400 p-4 rounded-lg mb-4 text-sm">
                    Tidak ada data grafik yang tersedia untuk koin ini.
                </div>
                <div ref="chartContainer" class="w-full h-[500px] rounded-xl overflow-hidden border border-gray-700 relative"></div>
            </div>

            <!-- Analysis Detail Section -->
            <div class="space-y-6">
                <!-- AI Analysis Card -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        Hasil Analisis AI
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 rounded-xl bg-gray-900/50">
                            <span class="text-gray-400">Rekomendasi</span>
                            <span :class="[
                                'px-4 py-1.5 rounded-lg text-sm font-extrabold shadow-lg',
                                coin.recommendation === 'BUY' ? 'bg-green-600 text-white shadow-green-900/50' : 
                                (coin.recommendation === 'SELL' ? 'bg-red-600 text-white shadow-red-900/50' : 'bg-gray-600 text-white')
                            ]">
                                {{ coin.recommendation || 'HOLD' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 rounded-xl bg-gray-900/50">
                            <span class="text-gray-400">Trend Saat Ini</span>
                            <span :class="[
                                'font-bold',
                                coin.trend === 'Bullish' ? 'text-green-400' : (coin.trend === 'Bearish' ? 'text-red-400' : 'text-gray-400')
                            ]">
                                {{ coin.trend || 'Neutral' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center p-3 rounded-xl bg-gray-900/50">
                            <span class="text-gray-400">Confidence Score</span>
                            <div class="flex items-center gap-2">
                                <span class="text-white font-bold">{{ coin.confidence || 0 }}%</span>
                                <div class="w-16 h-2 bg-gray-700 rounded-full">
                                    <div class="h-2 rounded-full bg-indigo-500" :style="`width: ${coin.confidence || 0}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Indicators -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-lg font-bold text-white mb-4">Indikator Teknikal</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">RSI (14)</span>
                            <span class="text-white font-medium">{{ coin.rsi || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">MACD</span>
                            <span class="text-white font-medium">{{ coin.macd || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Support (est)</span>
                            <span class="text-green-400 font-medium">${{ parseFloat(coin.support || 0).toLocaleString('en-US') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Resistance (est)</span>
                            <span class="text-red-400 font-medium">${{ parseFloat(coin.resistance || 0).toLocaleString('en-US') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
