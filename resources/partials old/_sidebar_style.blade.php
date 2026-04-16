{{-- resources/views/partials/_sidebar_style.blade.php --}}
{{-- Include sekali saja per sidebar --}}
<style>
.nav-group { margin-bottom: 2px; }
.nav-group-header {
    display: flex; align-items: center; padding: 8px 14px; cursor: pointer;
    border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .7px; color: #94a3b8; user-select: none;
    transition: background .15s, color .15s; gap: 7px;
}
.nav-group-header:hover { background: rgba(99,102,241,.08); color: #6366f1; }
.nav-gi  { font-size: 14px; flex-shrink: 0; }
.nav-gl  { flex: 1; }
.nav-gc  { font-size: 10px; opacity: .6; transition: transform .25s cubic-bezier(.4,0,.2,1); }
.nav-group-header.open .nav-gc { transform: rotate(180deg); }
.nav-group-body { overflow: hidden; max-height: 0; opacity: 0; transition: max-height .3s cubic-bezier(.4,0,.2,1), opacity .2s; }
.nav-group-body.open { max-height: 800px; opacity: 1; }
.nav-item { margin: 1px 0; }
.nav-item a { display: flex; align-items: center; gap: 8px; padding: 7px 14px 7px 30px !important;
    font-size: 13px; color: #475569; border-radius: 7px; text-decoration: none;
    transition: background .12s, color .12s; }
.nav-item a:hover { background: rgba(99,102,241,.07); color: #6366f1; }
.nav-item a.active { background: rgba(99,102,241,.12); color: #6366f1; font-weight: 700; }
.nav-item a i { font-size: 14px; flex-shrink: 0; }
.nav-sep { padding: 5px 30px 3px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #cbd5e1; letter-spacing: .5px; }
</style>
