{{-- resources/views/partials/_sidebar_script.blade.php --}}
<script>
function toggleGroup(h) {
    const b = h.nextElementSibling;
    const open = b.classList.contains('open');
    h.classList.toggle('open', !open);
    b.classList.toggle('open', !open);
}
document.addEventListener('DOMContentLoaded', function() {
    const active = document.querySelector('.nav-group-header.open');
    if (active) setTimeout(() => active.scrollIntoView({ behavior:'smooth', block:'nearest' }), 120);
});
</script>
