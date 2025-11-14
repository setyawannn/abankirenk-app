@php
  if (!function_exists('generate_star_rating')) {
    function generate_star_rating($rating) {
      $output = '<div class="flex items-center">';
        for ($i = 1; $i <= 5; $i++) {
          $output .='<ion-icon name="' . ($i <=$rating ? 'star' : 'star-outline' ) . '" class="' . ($i <=$rating ? 'text-yellow-400' : 'text-gray-300' ) . ' w-5 h-5"></ion-icon>' ;
          }
        $output .='<span class="ml-2 text-sm font-medium text-gray-600">(' . $rating . '/5)</span>' ;
        $output .='</div>' ;
      return $output;
    }
  }
@endphp

<div class="card-df rounded-t-none">
    <div class="p-6">
        
        <h4 class="text-lg font-medium mb-4">Feedback & Review Klien</h4>
        
        @if ($is_owner)
            
            @if (!$is_order_selesai)
                <div class="text-center p-8 bg-gray-50 rounded-md">
                    <ion-icon name="hourglass-outline" class="text-4xl text-gray-400"></ion-icon>
                    <p class="mt-2 text-gray-500">Anda dapat memberikan review setelah status order "Selesai".</p>
                </div>
            
            @elseif ($feedback && $is_locked)
                @include('order.partials._feedback_display', ['feedback' => $feedback])
                <div class="rounded-md bg-yellow-50 p-3 border border-yellow-200 mt-4">
                    <p class="text-sm font-medium text-yellow-800">Waktu Edit Habis</p>
                    <p class="text-sm text-yellow-700">Waktu edit review (24 jam) sudah habis.</p>
                </div>
                
            @else
                @include('order.partials._feedback_form', ['feedback' => $feedback, 'order' => $order])
            @endif

        @else
            
            @if (empty($feedback))
                <div class="text-center p-8">
                    <ion-icon name="chatbox-ellipses-outline" class="text-4xl text-gray-400"></ion-icon>
                    <p class="mt-2 text-gray-500">Klien belum memberikan feedback untuk order ini.</p>
                </div>
            @else
                @include('order.partials._feedback_display', ['feedback' => $feedback])
                
                <div class="rounded-md bg-gray-50 p-3 border border-gray-200 mt-4">
                    <p class="text-sm font-medium text-gray-700">Informasi Pengeditan</p>
                    <p class="text-sm text-gray-500">
                        @if ($is_locked)
                            <span class="font-semibold text-red-600">Terkunci.</span> Waktu edit klien (24 jam) sudah habis.
                        @else
                             <span class="font-semibold text-green-600">Bisa Diedit.</span> Klien masih dapat mengedit review ini.
                        @endif
                    </p>
                </div>
            @endif
            
        @endif

    </div>
</div>


<style>
    .prose p { margin-top: 0; margin-bottom: 1em; }
    
    .star-rating {
        display: flex;
        direction: rtl; 
        justify-content: flex-end; /* Mendorong bintang ke kiri */
    }

    /* 2. Sembunyikan radio button asli */
    .star-rating input[type="radio"] { display: none; }
    
    /* 3. Style bintang default (kosong) */
    .star-rating label {
        font-size: 2.5rem;
        color: #D1D5DB; /* text-gray-300 */
        cursor: pointer;
        transition: color 0.2s;
    }

    /* 4. Logika Hover (tidak berubah, tapi sekarang bekerja L-R) */
    /* Saat di-hover, warnai bintang itu DAN semua bintang "setelahnya" (secara HTML, yang berarti di kirinya) */
    .star-rating:hover label:hover ~ label,
    .star-rating:hover label:hover {
        color: #FBBF24; /* text-yellow-400 */
    }
    
    /* 5. Logika Checked (tidak berubah, tapi sekarang bekerja L-R) */
    /* Saat tidak di-hover, warnai bintang yang 'checked' DAN semua bintang di kirinya */
    .star-rating input[type="radio"]:checked ~ label {
        color: #FBBF24; /* text-yellow-400 */
    }
</style>