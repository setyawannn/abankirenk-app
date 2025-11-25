<footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-2xl font-bold text-red-600 mb-4">AbankIrenk</h3>
        <p class="text-gray-400 max-w-sm">
          Partner terbaik sekolah dalam mengabadikan kenangan masa sekolah melalui buku tahunan yang kreatif dan
          berkualitas.
        </p>
      </div>
      <div>
        <h4 class="text-lg font-semibold mb-4 text-white">Tautan Cepat</h4>
        <ul class="space-y-2">
          <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-red-500 transition">Home</a></li>
          <li><a href="{{ url('products') }}" class="text-gray-400 hover:text-red-500 transition">Produk</a></li>
          <li><a href="{{ url('about') }}" class="text-gray-400 hover:text-red-500 transition">Tentang Kami</a></li>
          <li><a href="{{ url('contact') }}" class="text-gray-400 hover:text-red-500 transition">Hubungi Kami</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-lg font-semibold mb-4 text-white">Kontak</h4>
        <ul class="space-y-2 text-gray-400">
          <li class="flex items-center gap-2">
            <ion-icon name="location-outline"></ion-icon> Jl. Kreatif No. 123, Indonesia
          </li>
          <li class="flex items-center gap-2">
            <ion-icon name="call-outline"></ion-icon> +62 812 3456 7890
          </li>
          <li class="flex items-center gap-2">
            <ion-icon name="mail-outline"></ion-icon> hello@abankirenk.com
          </li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
      <p>&copy; {{ date('Y') }} Ciboox Indonesia. All rights reserved.</p>
    </div>
  </div>
</footer>