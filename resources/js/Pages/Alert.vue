<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    alerts: Array,
    availableCoins: Array,
});

const isAddModalOpen = ref(false);

const form = useForm({
    coin_id: '',
    target_price: '',
    condition: 'above',
});

// Watch coin_id change to automatically set current price as placeholder/helper
const currentPrice = ref(null);
const handleCoinChange = () => {
    const coin = props.availableCoins.find(c => c.id === form.coin_id);
    if (coin) {
        currentPrice.value = coin.current_price;
    } else {
        currentPrice.value = null;
    }
};

const submitAlert = () => {
    form.post(route('alerts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isAddModalOpen.value = false;
            form.reset();
            currentPrice.value = null;
        },
    });
};

const deleteAlert = (id) => {
    if (confirm('Hapus peringatan ini?')) {
        router.delete(route('alerts.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Alert & Peringatan Otomatis" />

    <AuthenticatedLayout>
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Sistem Peringatan Harga</h1>
                <p class="text-gray-400">Terima notifikasi saat harga koin menyentuh target yang Anda tentukan.</p>
            </div>
            <button @click="isAddModalOpen = true" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-bold shadow-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Alert Baru
            </button>
        </div>

        <!-- Alert Table -->
        <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden shadow-xl mt-8">
            <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">Daftar Alert Saya</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-900/50 text-gray-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Aset</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Harga Live Saat Ini</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Kondisi</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Target Harga</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="alert in alerts" :key="alert.id" class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30 flex items-center justify-center font-bold text-xs shadow-inner">
                                        {{ alert.coin_symbol.substring(0,2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ alert.coin_name }}</div>
                                        <div class="text-xs text-gray-400">{{ alert.coin_symbol }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-300">
                                ${{ parseFloat(alert.current_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="alert.condition === 'above'" class="text-green-400 font-bold flex items-center justify-center gap-1">
                                    Naik Melewati <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                </span>
                                <span v-else class="text-red-400 font-bold flex items-center justify-center gap-1">
                                    Turun Melewati <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-white">
                                ${{ parseFloat(alert.target_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="alert.is_active" class="px-3 py-1 bg-green-500/10 border border-green-500/20 text-green-400 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-2 h-2 inline-block bg-green-500 rounded-full animate-pulse mr-1"></span> Aktif
                                </span>
                                <span v-else class="px-3 py-1 bg-gray-500/10 border border-gray-500/20 text-gray-400 rounded-full text-xs font-bold shadow-sm">
                                    Telah Terpicu
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="deleteAlert(alert.id)" class="text-gray-500 hover:text-red-400 transition-colors">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!alerts || alerts.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada alert aktif. Buat alert baru agar tidak ketinggalan momentum pasar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Alert Modal -->
        <div v-if="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="isAddModalOpen = false"></div>
            <div class="relative w-full max-w-md mx-auto my-6 z-50">
                <div class="border-0 rounded-2xl shadow-2xl relative flex flex-col w-full bg-gray-800 outline-none focus:outline-none border border-gray-700">
                    <div class="flex items-start justify-between p-5 border-b border-gray-700 rounded-t">
                        <h3 class="text-xl font-bold text-white">Buat Peringatan Baru</h3>
                        <button class="p-1 ml-auto border-0 text-gray-400 hover:text-white float-right text-3xl leading-none font-semibold outline-none focus:outline-none" @click="isAddModalOpen = false">
                            <span class="text-2xl block outline-none focus:outline-none">&times;</span>
                        </button>
                    </div>
                    <div class="relative p-6 flex-auto">
                        <form @submit.prevent="submitAlert" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Pilih Koin</label>
                                <select v-model="form.coin_id" @change="handleCoinChange" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option v-for="coin in availableCoins" :key="coin.id" :value="coin.id">
                                        {{ coin.name }} ({{ coin.symbol }})
                                    </option>
                                </select>
                                <div v-if="form.errors.coin_id" class="text-red-400 text-xs mt-1">{{ form.errors.coin_id }}</div>
                                <div v-if="currentPrice" class="text-gray-400 text-xs mt-1">Harga saat ini: <span class="font-bold text-white">${{ parseFloat(currentPrice).toLocaleString() }}</span></div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Kondisi</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label :class="['border rounded-lg p-3 flex items-center justify-center cursor-pointer transition-colors', form.condition === 'above' ? 'bg-purple-600/20 border-purple-500 text-purple-400 font-bold' : 'bg-gray-900 border-gray-700 text-gray-400 hover:bg-gray-700']">
                                        <input type="radio" v-model="form.condition" value="above" class="sr-only">
                                        Naik Melewati
                                    </label>
                                    <label :class="['border rounded-lg p-3 flex items-center justify-center cursor-pointer transition-colors', form.condition === 'below' ? 'bg-purple-600/20 border-purple-500 text-purple-400 font-bold' : 'bg-gray-900 border-gray-700 text-gray-400 hover:bg-gray-700']">
                                        <input type="radio" v-model="form.condition" value="below" class="sr-only">
                                        Turun Melewati
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Target Harga (USD)</label>
                                <input type="number" step="any" v-model="form.target_price" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Contoh: 106000">
                                <div v-if="form.errors.target_price" class="text-red-400 text-xs mt-1">{{ form.errors.target_price }}</div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-4 rounded-b mt-4 border-t border-gray-700">
                                <button type="button" class="text-gray-400 hover:text-white background-transparent font-bold px-4 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" @click="isAddModalOpen = false">
                                    Batal
                                </button>
                                <button type="submit" :disabled="form.processing" class="bg-purple-600 hover:bg-purple-500 text-white active:bg-purple-700 font-bold text-sm px-6 py-3 rounded-lg shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 disabled:opacity-50">
                                    Simpan Alert
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
