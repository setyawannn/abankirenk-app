{{-- Ini adalah file partial --}}
<h4 class="text-lg font-medium mb-4">Dokumen MoU</h4>
@if (empty($file_mou))
<p class="text-gray-500 text-center p-4">File MoU untuk order ini belum di-upload.</p>
@else
<iframe
  src="{{ url($file_mou) }}"
  class="w-full h-[600px] rounded-md border border-gray-300"
  frameborder="0">
</iframe>
@endif