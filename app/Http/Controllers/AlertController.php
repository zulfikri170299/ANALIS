<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alert;
use App\Models\Coin;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with('coin')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($alert) {
                return [
                    'id' => $alert->id,
                    'coin_name' => $alert->coin->name,
                    'coin_symbol' => $alert->coin->symbol,
                    'current_price' => $alert->coin->current_price,
                    'target_price' => $alert->target_price,
                    'condition' => $alert->condition,
                    'is_active' => $alert->is_active,
                    'created_at' => $alert->created_at->format('Y-m-d H:i')
                ];
            });

        $coins = Coin::select('id', 'name', 'symbol', 'current_price')->get();

        return Inertia::render('Alert', [
            'alerts' => $alerts,
            'availableCoins' => $coins
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coin_id' => 'required|exists:coins,id',
            'target_price' => 'required|numeric|min:0',
            'condition' => 'required|in:above,below',
        ]);

        Alert::create([
            'user_id' => Auth::id(),
            'coin_id' => $validated['coin_id'],
            'target_price' => $validated['target_price'],
            'condition' => $validated['condition'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Alert berhasil dibuat!');
    }

    public function destroy(Alert $alert)
    {
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->delete();

        return redirect()->back()->with('success', 'Alert berhasil dihapus.');
    }
}
