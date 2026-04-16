{{-- resources/views/partials/alog-detail.blade.php --}}
@if($log->old_values)
<div style="font-size:11px;background:#fee2e2;padding:6px 8px;border-radius:6px;margin-bottom:4px;">
    <strong style="color:#dc2626;">Sebelum:</strong><br>
    <code style="color:#dc2626;font-size:10px;white-space:pre-wrap;word-break:break-all;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</code>
</div>
@endif
@if($log->new_values)
<div style="font-size:11px;background:#dcfce7;padding:6px 8px;border-radius:6px;">
    <strong style="color:#16a34a;">Sesudah:</strong><br>
    <code style="color:#16a34a;font-size:10px;white-space:pre-wrap;word-break:break-all;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</code>
</div>
@endif
