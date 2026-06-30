<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Portfolio;
use App\Models\Coin;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with('coin')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function($portfolio) {
                $coin = $portfolio->coin;
                $currentValue = $portfolio->quantity * $coin->current_price;
                $invested = $portfolio->quantity * $portfolio->average_buy_price;
                $profit = $currentValue - $invested;
                $profitPercentage = $invested > 0 ? ($profit / $invested) * 100 : 0;
                
                return [
                    'id' => $portfolio->id,
                    'coin_name' => $coin->name,
                    'coin_symbol' => $coin->symbol,
                    'quantity' => $portfolio->quantity,
                    'average_buy_price' => $portfolio->average_buy_price,
                    'current_price' => $coin->current_price,
                    'total_value' => $currentValue,
                    'total_invested' => $invested,
                    'profit_loss' => $profit,
                    'profit_loss_percentage' => $profitPercentage,
                ];
            });

        $coins = Coin::select('id', 'name', 'symbol', 'current_price')->get();

        return Inertia::render('Portfolio', [
            'portfolios' => $portfolios,
            'availableCoins' => $coins
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coin_id' => 'required|exists:coins,id',
            'quantity' => 'required|numeric|min:0.00000001',
            'buy_price' => 'required|numeric|min:0',
        ]);

        $portfolio = Portfolio::where('user_id', Auth::id())
            ->where('coin_id', $validated['coin_id'])
            ->first();

        if ($portfolio) {
            // Calculate new average buy price
            $totalInvested = ($portfolio->quantity * $portfolio->average_buy_price) + ($validated['quantity'] * $validated['buy_price']);
            $newQuantity = $portfolio->quantity + $validated['quantity'];
            $newAveragePrice = $totalInvested / $newQuantity;

            $portfolio->update([
                'quantity' => $newQuantity,
                'average_buy_price' => $newAveragePrice
            ]);
        } else {
            Portfolio::create([
                'user_id' => Auth::id(),
                'coin_id' => $validated['coin_id'],
                'quantity' => $validated['quantity'],
                'average_buy_price' => $validated['buy_price']
            ]);
        }

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Aset berhasil dihapus dari portofolio.');
    }
}
