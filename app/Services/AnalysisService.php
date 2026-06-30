<?php

namespace App\Services;

class AnalysisService
{
    /**
     * Calculate Simple Moving Average (SMA)
     */
    public function calculateMA(array $prices, $period = 14)
    {
        if (count($prices) < $period) return null;
        $slice = array_slice($prices, -$period);
        return array_sum($slice) / $period;
    }

    /**
     * Calculate Relative Strength Index (RSI)
     */
    public function calculateRSI(array $prices, $period = 14)
    {
        if (count($prices) <= $period) return null;

        $gains = 0;
        $losses = 0;

        for ($i = 1; $i <= $period; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            if ($change > 0) {
                $gains += $change;
            } else {
                $losses += abs($change);
            }
        }

        $avgGain = $gains / $period;
        $avgLoss = $losses / $period;

        if ($avgLoss == 0) {
            return 100; // All gains
        }

        $rs = $avgGain / $avgLoss;
        return 100 - (100 / (1 + $rs));
    }
    
    /**
     * Analyze Trend based on technical indicators
     */
    public function analyzeTrend(array $prices)
    {
        if (count($prices) < 14) {
            return null; // Not enough data
        }

        $ma14 = $this->calculateMA($prices, 14);
        $currentPrice = end($prices);
        $rsi = $this->calculateRSI($prices, 14);
        
        $trend = 'Neutral';
        $recommendation = 'HOLD';
        $confidence = 50;

        if ($currentPrice > $ma14 && $rsi > 50) {
            $trend = 'Bullish';
            $recommendation = 'BUY';
            $confidence = 75;
            if ($rsi > 70) {
                 $recommendation = 'HOLD'; // Overbought warning
                 $confidence = 60;
            }
        } elseif ($currentPrice < $ma14 && $rsi < 50) {
            $trend = 'Bearish';
            $recommendation = 'SELL';
            $confidence = 75;
            if ($rsi < 30) {
                $recommendation = 'BUY'; // Oversold opportunity
                $confidence = 60;
            }
        }

        return [
            'trend' => $trend,
            'rsi' => $rsi ? round($rsi, 2) : null,
            'macd' => $currentPrice > $ma14 ? 'Positive' : 'Negative', // Simplified interpretation
            'support' => min(array_slice($prices, -14)), // Simplistic localized support
            'resistance' => max(array_slice($prices, -14)), // Simplistic localized resistance
            'recommendation' => $recommendation,
            'confidence' => $confidence
        ];
    }
}
