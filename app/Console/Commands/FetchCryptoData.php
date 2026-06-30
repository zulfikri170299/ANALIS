<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Coin;
use App\Models\PriceHistory;
use App\Models\AnalysisResult;
use App\Services\CryptoApiService;
use App\Services\AnalysisService;

class FetchCryptoData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'crypto:fetch';

    /**
     * The console command description.
     */
    protected $description = 'Fetch real-time crypto data, record history, and run technical analysis';

    /**
     * Execute the console command.
     */
    public function handle(CryptoApiService $apiService, AnalysisService $analysisService)
    {
        $symbols = [
            'BTCUSDT', 'ETHUSDT', 'SOLUSDT', 'ADAUSDT', 'XRPUSDT', 
            'DOTUSDT', 'DOGEUSDT', 'AVAXUSDT', 'LINKUSDT', 'MATICUSDT',
            'LTCUSDT', 'BCHUSDT', 'ATOMUSDT', 'XLMUSDT', 'BNBUSDT',
            'TRXUSDT', 'UNIUSDT', 'NEARUSDT', 'ICPUSDT', 'APTUSDT',
            'FILUSDT', 'VETUSDT', 'ARBUSDT', 'RNDRUSDT', 'GRTUSDT'
        ];
        $this->info('Fetching market data from Binance...');
        
        $marketData = $apiService->fetchMarketData($symbols);

        foreach ($marketData as $data) {
            $symbol = str_replace('USDT', '', $data['symbol']);
            $currentPrice = $data['lastPrice'];
            $volume = $data['volume'];
            $changePercent = $data['priceChangePercent'];
            
            // Note: Market cap is not natively provided by Binance 24hr ticker in dollars without circulating supply.
            // We'll estimate it or leave it null for now. 
            $coin = Coin::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'name' => $this->getFriendlyName($symbol),
                    'current_price' => $currentPrice,
                    'volume' => $volume,
                    'change_percentage' => $changePercent,
                ]
            );

            // Record price history
            PriceHistory::create([
                'coin_id' => $coin->id,
                'price' => $currentPrice,
                'volume' => $volume,
                'timestamp' => now(),
            ]);

            // Process Alerts
            $this->processAlerts($coin);

            $this->info("Updated {$coin->symbol}: $" . number_format($coin->current_price, 2));

            // Fetch Klines for Technical Analysis (Daily candles, last 50 days)
            $klines = $apiService->fetchKlines($data['symbol'], '1d', 50);
            
            if (count($klines) >= 14) {
                // Extract closing prices (index 4 in Binance Kline array)
                $closingPrices = array_map(function($kline) {
                    return (float) $kline[4];
                }, $klines);

                // Add current price to the end of the historical array to be accurate to the minute
                $closingPrices[] = (float) $currentPrice;

                // Perform Analysis
                $analysis = $analysisService->analyzeTrend($closingPrices);

                if ($analysis) {
                    AnalysisResult::updateOrCreate(
                        ['coin_id' => $coin->id],
                        [
                            'trend' => $analysis['trend'],
                            'rsi' => $analysis['rsi'],
                            'macd' => $analysis['macd'],
                            'support' => $analysis['support'],
                            'resistance' => $analysis['resistance'],
                            'recommendation' => $analysis['recommendation'],
                            'confidence' => $analysis['confidence'],
                        ]
                    );
                    $this->info("  -> Analysis: {$analysis['trend']} (RSI: {$analysis['rsi']}, Rec: {$analysis['recommendation']})");
                }
            }
        }

        $this->info('Crypto data fetch and analysis completed successfully.');
    }

    private function getFriendlyName($symbol)
    {
        $names = [
            'BTC' => 'Bitcoin', 'ETH' => 'Ethereum', 'SOL' => 'Solana', 'ADA' => 'Cardano', 'XRP' => 'Ripple',
            'DOT' => 'Polkadot', 'DOGE' => 'Dogecoin', 'AVAX' => 'Avalanche', 'LINK' => 'Chainlink', 'MATIC' => 'Polygon',
            'LTC' => 'Litecoin', 'BCH' => 'Bitcoin Cash', 'ATOM' => 'Cosmos', 'XLM' => 'Stellar', 'BNB' => 'BNB',
            'TRX' => 'TRON', 'UNI' => 'Uniswap', 'NEAR' => 'NEAR Protocol', 'ICP' => 'Internet Computer', 'APT' => 'Aptos',
            'FIL' => 'Filecoin', 'VET' => 'VeChain', 'ARB' => 'Arbitrum', 'RNDR' => 'Render', 'GRT' => 'The Graph'
        ];

        return $names[$symbol] ?? $symbol;
    }

    /**
     * Check if any alerts meet the condition and trigger them
     */
    private function processAlerts($coin)
    {
        $activeAlerts = \App\Models\Alert::where('coin_id', $coin->id)
            ->where('is_active', true)
            ->get();

        foreach ($activeAlerts as $alert) {
            $triggered = false;
            
            if ($alert->condition === 'above' && $coin->current_price >= $alert->target_price) {
                $triggered = true;
            } elseif ($alert->condition === 'below' && $coin->current_price <= $alert->target_price) {
                $triggered = true;
            }

            if ($triggered) {
                // Mark alert as triggered (inactive)
                $alert->update(['is_active' => false]);
                $this->info("  -> Alert Triggered! {$coin->symbol} went {$alert->condition} {$alert->target_price}");
                // Here we could dispatch an email or push notification job
            }
        }
    }
}
