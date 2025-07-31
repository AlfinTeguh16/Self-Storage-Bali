<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Self Storage Bali</title>
    @vite('resources/css/app.css')
    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css"
    />
</head>
<body>
    @include('components.navbar')
    <section class="w-full h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('{{ asset('/img/header.jpg') }}');">
        <div class="flex flex-col w-full justify-center items-center text-center">
            <div class="w-2/3">
                <h1 class="text-3xl md:text-6xl font-bold text-white mb-4">SELAMAT DATANG DI SELF STORAGE BALI</h1>
                <p class="text-lg md:text-2xl text-white mb-8">Self Storage Bali adalah perusahaan yang didirikan pada tahun 2023 dan bergerak di bidang jasa penyimpanan barang.</p>
            </div>
        </div>
    </section>
</body>
</html>

