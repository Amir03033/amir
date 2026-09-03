<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Amir Jebbari</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-200 flex items-center justify-center px-4">
    <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm space-y-4 border border-slate-800 bg-slate-900/80 p-8 rounded-2xl">
        @csrf
        <h1 class="text-xl font-bold text-white">Inloggen</h1>

        @if ($errors->any())
            <div class="text-rose-400 text-sm">{{ $errors->first() }}</div>
        @endif

        <div>
            <label for="email" class="block text-xs text-slate-400 mb-1">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-sm">
        </div>

        <div>
            <label for="password" class="block text-xs text-slate-400 mb-1">Wachtwoord</label>
            <input id="password" type="password" name="password" required
                   class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-sm">
        </div>

        <label class="flex items-center gap-2 text-xs text-slate-400">
            <input type="checkbox" name="remember"> Onthoud mij
        </label>

        <button type="submit" class="w-full py-3 rounded-lg bg-cyan-500 text-white font-semibold">
            Inloggen
        </button>
    </form>
</body>
</html>
