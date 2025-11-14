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

    <div class="space-y-6">
    <div>
      <label class="txt-title-df">Rating</label>
      <div class="txt-desc-df">
        {!! generate_star_rating($feedback['rating']) !!}
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="txt-title-df">Dikirim Oleh Klien</label>
        <p class="txt-desc-df">{{ $feedback['nama_klien'] ?? 'N/A' }}</p>
      </div>
      <div>
        <label class="txt-title-df">Terakhir Diperbarui</label>
        <p class="txt-desc-df">{{ $feedback['formatted_updated_at'] }}</p>
      </div>
    </div>
    <div>
      <label class="txt-title-df">Review Klien</label>
      <div class="prose prose-sm max-w-none text-gray-700 mt-1 p-3 border border-gray-200 rounded-md bg-gray-50 min-h-[100px]">
        <p>{!! nl2br($feedback['komentar']) !!}</p>
      </div>
    </div>
</div>