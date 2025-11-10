{{-- Ini adalah file partial --}}
<div class="flex justify-between items-center mb-4">
  <h4 class="text-lg font-medium">Data Desain</h4>
  @if(auth()['role'] == 'desainer')
  <button class="btn-df btn-sm">
    <ion-icon name="cloud-upload-outline"></ion-icon>
    Upload Hasil Desain
  </button>
  @endif
</div>
<div class="space-y-4">
  <p class="text-gray-500 text-center p-4">Belum ada file desain yang di-upload.</p>
</div>