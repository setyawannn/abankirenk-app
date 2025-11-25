@extends('layouts.app')

@section('title')
Jasa Pembuatan Yearbook Terbaik
@endsection

@section('content')
<section class="relative bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ url('images/hero-bg.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="md:w-2/3">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                Abadikan Kenangan <br> <span class="text-red-600">Masa Sekolah</span> Anda
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl leading-relaxed">
                Kami membantu sekolah dan siswa membuat buku tahunan (yearbook) yang kreatif, berkualitas, dan tak
                terlupakan. Mulai dari konsep hingga cetak.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#portfolio"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2">
                    Lihat Portfolio <ion-icon name="arrow-down-outline"></ion-icon>
                </a>
                <a href="#contact"
                    class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-3 px-8 rounded-full transition duration-300">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Kenapa Memilih AbankIrenk?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Kami memberikan layanan terbaik untuk memastikan buku tahunan
                Anda menjadi kenangan yang sempurna.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div
                class="p-8 bg-gray-50 rounded-2xl hover:shadow-xl transition duration-300 text-center group border border-gray-100">
                <div
                    class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-red-600 group-hover:text-white transition duration-300">
                    <ion-icon name="color-palette-outline" class="text-3xl"></ion-icon>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Desain Kreatif</h3>
                <p class="text-gray-600">Tim desainer kami siap mewujudkan konsep unik dan kekinian sesuai keinginan
                    angkatan Anda.</p>
            </div>
            <div
                class="p-8 bg-gray-50 rounded-2xl hover:shadow-xl transition duration-300 text-center group border border-gray-100">
                <div
                    class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-red-600 group-hover:text-white transition duration-300">
                    <ion-icon name="print-outline" class="text-3xl"></ion-icon>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Kualitas Cetak Premium</h3>
                <p class="text-gray-600">Menggunakan mesin cetak terbaru dan bahan kertas berkualitas tinggi untuk hasil
                    yang tajam dan awet.</p>
            </div>
            <div
                class="p-8 bg-gray-50 rounded-2xl hover:shadow-xl transition duration-300 text-center group border border-gray-100">
                <div
                    class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-red-600 group-hover:text-white transition duration-300">
                    <ion-icon name="chatbubbles-outline" class="text-3xl"></ion-icon>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Pelayanan Ramah</h3>
                <p class="text-gray-600">Kami siap berdiskusi dan membantu Anda dari tahap perencanaan hingga buku
                    diterima.</p>
            </div>
        </div>
    </div>
</section>

<section id="portfolio" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Portfolio Desain</h2>
                <p class="text-gray-600">Beberapa contoh tema yang telah kami kerjakan.</p>
            </div>
            <a href="#" class="text-red-600 font-semibold hover:text-red-700 flex items-center gap-1 group">
                Lihat Semua <ion-icon name="arrow-forward-outline"
                    class="group-hover:translate-x-1 transition"></ion-icon>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(empty($templates))
            <div class="col-span-3 text-center py-12 text-gray-500">
                <p>Belum ada portfolio yang ditampilkan.</p>
            </div>
            @else
            @foreach($templates as $template)
            <div
                class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition duration-300 flex flex-col h-full">
                <div class="h-48 bg-gray-200 relative group overflow-hidden">
                    <img src="https://placehold.co/600x400/red/white?text={{ urlencode($template['judul']) }}"
                        alt="{{ $template['judul'] }}"
                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                        <a href="#"
                            class="text-white font-bold border border-white px-6 py-2 rounded-full hover:bg-white hover:text-black transition">Detail</a>
                    </div>
                </div>
                <div class="p-6 flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $template['judul'] }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-3">{{ $template['deskripsi'] }}</p>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Apa Kata Mereka?</h2>
            <p class="text-gray-600">Pengalaman klien yang telah bekerjasama dengan kami.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(empty($feedbacks))
            <div class="col-span-3 text-center py-12 text-gray-500">
                <p>Belum ada ulasan.</p>
            </div>
            @else
            @foreach($feedbacks as $feedback)
            <div
                class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:border-red-200 transition duration-300 relative">
                <div class="absolute -top-4 left-8 text-6xl text-red-100 font-serif">"</div>
                <div class="flex items-center gap-1 text-yellow-400 mb-4 relative z-10">
                    @for($i = 0; $i < $feedback['rating']; $i++) <ion-icon name="star"></ion-icon>
                        @endfor
                </div>
                <p class="text-gray-700 mb-6 italic relative z-10">"{{ $feedback['komentar'] }}"</p>
                <div class="flex items-center gap-4 relative z-10">
                    <div
                        class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-lg">
                        {{ substr($feedback['nama_klien'], 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $feedback['nama_klien'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $feedback['nama_sekolah'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>

<section id="contact" class="py-20 bg-red-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
        </svg>
    </div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">Siap Membuat Yearbook Impian?</h2>
        <p class="text-red-100 text-lg md:text-xl mb-10 max-w-2xl mx-auto">Jangan ragu untuk berkonsultasi dengan kami.
            Dapatkan penawaran terbaik untuk sekolah Anda sekarang juga.</p>
        <a href="https://wa.me/6289529028582" target="_blank"
            class="bg-white text-red-600 font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition duration-300 shadow-xl inline-flex items-center gap-2 transform hover:-translate-y-1">
            <ion-icon name="logo-whatsapp" class="text-xl"></ion-icon> Hubungi Kami via WhatsApp
        </a>
    </div>
</section>
@endsection