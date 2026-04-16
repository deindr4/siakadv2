{{-- resources/views/partials/_dashboard_responsive.blade.php --}}
{{-- Include di semua dashboard: @include('partials._dashboard_responsive') --}}
<style>
/* ================================================
   DASHBOARD RESPONSIVE GRID UTILITIES
   ================================================ */

/* Grid 3 kolom → responsif */
.dash-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 16px;
}
/* Grid 4 kolom → responsif */
.dash-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
/* Grid 5 kolom → responsif */
.dash-grid-5 {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
/* Grid 2 kolom → responsif */
.dash-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
/* Grid 2/3 + 1/3 */
.dash-grid-2-1 {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

/* Stat card besar (gradient) */
.dash-stat-card {
    border-radius: 14px;
    padding: 18px 20px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    transition: opacity .2s, transform .15s;
}
.dash-stat-card:hover { opacity: .92; transform: translateY(-1px); color: #fff; }
.dash-stat-card i { font-size: 32px; opacity: .85; flex-shrink: 0; }
.dash-stat-card .dsc-label { font-size: 11px; opacity: .8; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
.dash-stat-card .dsc-val   { font-size: 28px; font-weight: 800; line-height: 1; }

/* Stat card kecil (border) */
.dash-mini-card {
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.dash-mini-card i { font-size: 22px; flex-shrink: 0; }
.dash-mini-card .dmc-label { font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
.dash-mini-card .dmc-val   { font-size: 22px; font-weight: 800; line-height: 1; }

/* Stat box tengah (kotak dengan warna bg) */
.dash-box-center {
    text-align: center;
    padding: 12px 8px;
    border-radius: 10px;
}
.dash-box-center .dbc-val   { font-size: 22px; font-weight: 800; line-height: 1; }
.dash-box-center .dbc-label { font-size: 10px; font-weight: 700; margin-top: 4px; }

/* Feed row (list item klikable) */
.dash-feed-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: background .12s;
}
.dash-feed-row:hover { background: #f8fafc; }
.dash-feed-row:last-child { border-bottom: none; }

/* Progress bar */
.dash-progress-wrap { background: #f1f5f9; border-radius: 20px; height: 10px; overflow: hidden; }
.dash-progress-bar  { height: 100%; border-radius: 20px; transition: width .5s; }

/* ================================================
   BREAKPOINTS
   ================================================ */

/* Tablet landscape (≤1200px) */
@media (max-width: 1200px) {
    .dash-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .dash-grid-5 { grid-template-columns: repeat(3, 1fr); }
    .dash-stat-card .dsc-val { font-size: 24px; }
}

/* Tablet portrait (≤900px) */
@media (max-width: 900px) {
    .dash-grid-3  { grid-template-columns: 1fr 1fr; }
    .dash-grid-2  { grid-template-columns: 1fr; }
    .dash-grid-2-1{ grid-template-columns: 1fr; }
    .dash-grid-4  { grid-template-columns: repeat(2, 1fr); }
    .dash-grid-5  { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile (≤600px) */
@media (max-width: 600px) {
    .dash-grid-3  { grid-template-columns: 1fr; }
    .dash-grid-4  { grid-template-columns: 1fr 1fr; }
    .dash-grid-5  { grid-template-columns: 1fr 1fr; }
    .dash-stat-card { padding: 14px 16px; }
    .dash-stat-card i { font-size: 26px; }
    .dash-stat-card .dsc-val { font-size: 22px; }
    .dash-mini-card .dmc-val { font-size: 18px; }
    .page-title h1 { font-size: 18px; }
}
</style>
