<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Coin;

Route::get('/', function () {
    // Fetch live coins and their latest analysis trend
    $coins = Coin::leftJoin('analysis_results', 'coins.id', '=', 'analysis_results.coin_id')
        ->select('coins.*', 'analysis_results.trend')
        ->orderBy('coins.volume', 'desc')
        ->get()
        ->map(function($coin) {
            return [
                'id' => $coin->id,
                'name' => $coin->name,
                'symbol' => $coin->symbol,
                'price' => '$' . number_format($coin->current_price, 2),
                'change' => ($coin->change_percentage > 0 ? '+' : '') . number_format($coin->change_percentage, 2) . '%',
                'volume' => '$' . number_format($coin->volume, 2),
                'trend' => $coin->trend ?? 'Neutral',
                'isUp' => $coin->change_percentage > 0
            ];
        });

    return Inertia::render('Welcome', [
        'cryptos' => $coins,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $coins = Coin::leftJoin('analysis_results', 'coins.id', '=', 'analysis_results.coin_id')
        ->select('coins.*', 'analysis_results.trend', 'analysis_results.rsi', 'analysis_results.macd', 'analysis_results.recommendation', 'analysis_results.confidence')
        ->get();
        
    return Inertia::render('Dashboard', [
        'cryptos' => $coins
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/coins/{symbol}', function ($symbol, \App\Services\CryptoApiService $apiService) {
    $coin = Coin::leftJoin('analysis_results', 'coins.id', '=', 'analysis_results.coin_id')
        ->where('symbol', strtoupper($symbol))
        ->select('coins.*', 'analysis_results.trend', 'analysis_results.rsi', 'analysis_results.macd', 'analysis_results.support', 'analysis_results.resistance', 'analysis_results.recommendation', 'analysis_results.confidence')
        ->firstOrFail();
        
    $filter = request()->query('filter', '1Bulan');
    
    // Map filter to Binance interval and limit
    $timeframes = [
        '1Hari' => ['interval' => '1h', 'limit' => 24],
        '1Minggu' => ['interval' => '4h', 'limit' => 42],
        '1Bulan' => ['interval' => '1d', 'limit' => 30],
        '1Tahun' => ['interval' => '1w', 'limit' => 52],
    ];
    $tf = $timeframes[$filter] ?? $timeframes['1Bulan'];

    $klines = $apiService->fetchKlines($coin->symbol . 'USDT', $tf['interval'], $tf['limit']);
    
    $chartData = [];
    foreach ($klines as $k) {
        $chartData[] = [
            'time' => (int)($k[0] / 1000), // UNIX timestamp in seconds
            'open' => (float)$k[1],
            'high' => (float)$k[2],
            'low' => (float)$k[3],
            'close' => (float)$k[4],
        ];
    }
    
    return Inertia::render('CoinDetail', [
        'coin' => $coin,
        'chartData' => $chartData,
        'currentFilter' => $filter
    ]);
})->middleware(['auth', 'verified'])->name('coins.show');

Route::get('/debug-chart/{symbol}', function ($symbol, \App\Services\CryptoApiService $apiService) {
    $coin = Coin::where('symbol', strtoupper($symbol))->firstOrFail();
    $klines = $apiService->fetchKlines($coin->symbol . 'USDT', '1d', 100);
    $chartData = [];
    foreach ($klines as $k) {
        $chartData[] = [
            'time' => gmdate('Y-m-d', (int)($k[0] / 1000)),
            'open' => (float)$k[1],
            'high' => (float)$k[2],
            'low' => (float)$k[3],
            'close' => (float)$k[4],
        ];
    }
    return response()->json($chartData);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Portfolio
    Route::get('/portfolio', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio', [\App\Http\Controllers\PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portfolio/{portfolio}', [\App\Http\Controllers\PortfolioController::class, 'destroy'])->name('portfolio.destroy');

    // Alerts
    Route::get('/alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts', [\App\Http\Controllers\AlertController::class, 'store'])->name('alerts.store');
    Route::delete('/alerts/{alert}', [\App\Http\Controllers\AlertController::class, 'destroy'])->name('alerts.destroy');
});

require __DIR__.'/auth.php';
