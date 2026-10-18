<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Tamu</title>
    @vite('resources/css/app.css')
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang</h1>
        <p class="mt-4 text-gray-600">Halaman informasi ini dikhususkan untuk pengunjung yang belum memiliki sesi aktif.</p>

        <div class="mt-6 flex gap-4">
            <a href="{{ route('login') }}" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 transition">Masuk ke POS</a>
        </div>
    </div>
</body>
</html>