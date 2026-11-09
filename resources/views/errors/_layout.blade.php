{{-- Shared look for error pages. Kept dependency-free so it renders even when something else is broken. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} · BUSina</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 px-4 font-sans antialiased">
    <div class="max-w-md text-center">
        <a href="{{ url('/') }}" class="inline-block"><x-logo size="lg" /></a>
        <p class="mt-10 text-6xl font-extrabold text-brand-600">{{ $code }}</p>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">{{ $heading }}</h1>
        <p class="mt-2 text-slate-600">{{ $text }}</p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="javascript:history.back()" class="btn-secondary">Go back</a>
            <a href="{{ url('/') }}" class="btn-primary">Home</a>
        </div>
    </div>
</body>
</html>
