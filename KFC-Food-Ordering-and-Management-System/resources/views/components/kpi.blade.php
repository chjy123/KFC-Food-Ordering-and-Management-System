@props(['title','value','sub'=>null])
<div class="rounded-2xl border bg-white p-5 shadow-sm">
  <p class="text-sm text-gray-500">{{ $title }}</p>
  <p class="mt-1 text-2xl font-semibold">{{ $value }}</p>
  @if($sub)<p class="mt-3 text-xs text-gray-500">{{ $sub }}</p>@endif
</div>
