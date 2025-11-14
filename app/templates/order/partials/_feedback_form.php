<form action="{{ url('/feedback/store') }}" method="POST" class="space-y-4">

  <input type="hidden" name="id_order_produksi" value="{{ $order['id_order_produksi'] }}">
  <input type="hidden" name="id_feedback" value="{{ $feedback['id_feedback'] ?? 0 }}">

  <div>
    <label class="label-df">Beri Rating <span class="text-red-500">*</span></label>

    {{-- ========================================================== --}}
    {{-- PERBAIKAN: Menggunakan 'direction: rtl' (lihat CSS)      --}}
    {{-- ========================================================== --}}
    {{-- div ini akan di-style 'direction: rtl' oleh CSS --}}
    <div class="star-rating">

      {{-- HTML tetap terbalik (5 ke 1) agar CSS ~ berfungsi --}}
      <input type="radio" id="star-5" name="rating" value="5" @if($feedback && $feedback['rating']==5) checked @endif>
      <label for="star-5" title="Bintang 5"><ion-icon name="star"></ion-icon></label>

      <input type="radio" id="star-4" name="rating" value="4" @if($feedback && $feedback['rating']==4) checked @endif>
      <label for="star-4" title="Bintang 4"><ion-icon name="star"></ion-icon></label>

      <input type="radio" id="star-3" name="rating" value="3" @if($feedback && $feedback['rating']==3) checked @endif>
      <label for="star-3" title="Bintang 3"><ion-icon name="star"></ion-icon></label>

      <input type="radio" id="star-2" name="rating" value="2" @if($feedback && $feedback['rating']==2) checked @endif>
      <label for="star-2" title="Bintang 2"><ion-icon name="star"></ion-icon></label>

      <input type="radio" id="star-1" name="rating" value="1" @if($feedback && $feedback['rating']==1) checked @endif required>
      <label for="star-1" title="Bintang 1"><ion-icon name="star"></ion-icon></label>
    </div>
  </div>

  <div>
    <label for="komentar" class="label-df">Tulis Review <span class="text-red-500">*</span></label>
    <textarea name="komentar" id="komentar" rows="5" class="input-df resize-none"
      placeholder="Bagaimana pengalaman Anda dengan layanan kami?"
      required>{{ $feedback['komentar'] ?? '' }}</textarea>
  </div>

  <div class="flex justify-end">
    <button type="submit" class="btn-df">
      {{ $feedback ? 'Update Feedback' : 'Kirim Feedback' }}
    </button>
  </div>
</form>