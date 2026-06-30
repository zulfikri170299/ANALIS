<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    portfolios: Array,
    availableCoins: Array,
});

const isAddModalOpen = ref(false);

const form = useForm({
    coin_id: '',
    quantity: '',
    buy_price: '',
});

// Watch coin_id change to automatically set current price as default buy_price
const handleCoinChange = () => {
    const coin = props.availableCoins.find(c => c.id === form.coin_id);
    if (coin) {
        form.buy_price = coin.current_price;
    }
};

const submitTransaction = () => {
    form.post(route('portfolio.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isAddModalOpen.value = false;
            form.reset();
        },
    });
};

const deletePortfolio = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus aset ini dari portofolio?')) {
        router.delete(route('portfolio.destroy', id), {
            preserveScroll: true
        });
    }
};

const totalInvested = computed(() => {
    return props.portfolios.reduce((sum, item) => sum + parseFloat(item.total_invested), 0);
});

const totalValue = computed(() => {
    return props.portfolios.reduce((sum, item) => sum + parseFloat(item.total_value), 0);
});

const totalProfitLoss = computed(() => {
    return totalValue.value - totalInvested.value;
});

const totalProfitLossPercentage = computed(() => {
    if (totalInvested.value === 0) return 0;
    return (totalProfitLoss.value / totalInvested.value) * 100;
});
</script>

<template>
    <Head title="Manajemen Portofolio" />

    <AuthenticatedLayout>
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Portofolio Saya</h1>
                <p class="text-gray-400">Pantau nilai aset kripto Anda secara real-time.</p>
            </div>
            <button @click="isAddModalOpen = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold shadow-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Transaksi
            </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Nilai Saat Ini</div>
                <div class="text-3xl font-bold text-white">${{ totalValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Modal Diinvestasikan</div>
                <div class="text-3xl font-bold text-white">${{ totalInvested.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-lg">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Keuntungan/Kerugian</div>
                <div :class="['text-3xl font-bold', totalProfitLoss >= 0 ? 'text-green-400' : 'text-red-400']">
                    {{ totalProfitLoss >= 0 ? '+' : '' }}${{ totalProfitLoss.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                    <span class="text-lg font-medium">({{ totalProfitLoss >= 0 ? '+' : '' }}{{ totalProfitLossPercentage.toFixed(2) }}%)</span>
                </div>
            </div>
        </div>

        <!-- Portfolio Table -->
        <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white">Aset yang Dimiliki</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-900/50 text-gray-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Aset</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Saldo (Kuantitas)</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Harga Beli Rata-Rata</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Harga Saat Ini</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Total Nilai</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">P/L</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="item in portfolios" :key="item.id" class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-xs shadow-inner">
                                        {{ item.coin_symbol.substring(0,2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ item.coin_name }}</div>
                                        <div class="text-xs text-gray-400">{{ item.coin_symbol }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-white">
                                {{ parseFloat(item.quantity).toLocaleString('en-US', {maximumFractionDigits: 8}) }} {{ item.coin_symbol }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-300">
                                ${{ parseFloat(item.average_buy_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-300">
                                ${{ parseFloat(item.current_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-white">
                                ${{ parseFloat(item.total_value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold" :class="item.profit_loss >= 0 ? 'text-green-400' : 'text-red-400'">
                                {{ item.profit_loss >= 0 ? '+' : '' }}${{ parseFloat(item.profit_loss).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}<br/>
                                <span class="text-xs font-medium">{{ item.profit_loss >= 0 ? '+' : '' }}{{ parseFloat(item.profit_loss_percentage).toFixed(2) }}%</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="deletePortfolio(item.id)" class="text-red-400 hover:text-red-300 transition-colors">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!portfolios || portfolios.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Belum ada aset di portofolio Anda. Klik "Tambah Transaksi" untuk mencatat aset baru.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Transaction Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            <div class="relative w-full max-w-md mx-auto my-6 z-50">
                <!-- content -->
                <div class="border-0 rounded-2xl shadow-2xl relative flex flex-col w-full bg-gray-800 outline-none focus:outline-none border border-gray-700">
                    <!-- header -->
                    <div class="flex items-start justify-between p-5 border-b border-gray-700 rounded-t">
                        <h3 class="text-xl font-bold text-white">Tambah Transaksi Koin</h3>
                        <button class="p-1 ml-auto border-0 text-gray-400 hover:text-white float-right text-3xl leading-none font-semibold outline-none focus:outline-none" @click="isAddModalOpen = false">
                            <span class="text-2xl block outline-none focus:outline-none">&times;</span>
                        </button>
                    </div>
                    <!-- body -->
                    <div class="relative p-6 flex-auto">
                        <form @submit.prevent="submitTransaction" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Pilih Koin</label>
                                <select v-model="form.coin_id" @change="handleCoinChange" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option v-for="coin in availableCoins" :key="coin.id" :value="coin.id">
                                        {{ coin.name }} ({{ coin.symbol }}) - ${{ parseFloat(coin.current_price).toLocaleString() }}
                                    </option>
                                </select>
                                <div v-if="form.errors.coin_id" class="text-red-400 text-xs mt-1">{{ form.errors.coin_id }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Kuantitas (Saldo)</label>
                                <input type="number" step="any" v-model="form.quantity" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: 0.5">
                                <div v-if="form.errors.quantity" class="text-red-400 text-xs mt-1">{{ form.errors.quantity }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Harga Beli Rata-Rata (USD)</label>
                                <input type="number" step="any" v-model="form.buy_price" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: 105000">
                                <div v-if="form.errors.buy_price" class="text-red-400 text-xs mt-1">{{ form.errors.buy_price }}</div>
                            </div>
                            
                            <!-- footer -->
                            <div class="flex items-center justify-end pt-4 rounded-b mt-4 border-t border-gray-700">
                                <button type="button" class="text-gray-400 hover:text-white background-transparent font-bold px-4 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" @click="isAddModalOpen = false">
                                    Batal
                                </button>
                                <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-500 text-white active:bg-indigo-700 font-bold text-sm px-6 py-3 rounded-lg shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 disabled:opacity-50">
                                    Simpan Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
