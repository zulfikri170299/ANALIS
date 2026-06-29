<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ANALIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; color: #F8FAFC; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute w-[600px] h-[600px] bg-indigo-600/30 rounded-full blur-[80px] -top-[200px] -left-[200px]"></div>
    <div class="absolute w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-[80px] -bottom-[100px] -right-[100px]"></div>

    <div class="relative z-10 w-full max-w-md p-8 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-indigo-500 to-cyan-400 bg-clip-text text-transparent mb-2">ANALIS</h1>
            <p class="text-slate-400">Silakan masuk ke akun Anda</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors text-white placeholder-slate-500" placeholder="nama@email.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors text-white placeholder-slate-500" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-semibold shadow-[0_4px_15px_rgba(79,70,229,0.4)] transition-all hover:-translate-y-0.5">
                Masuk ke Sistem
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-slate-400 hover:text-indigo-400 transition-colors">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
