@extends('frontend.master')
@section('meta')

@endsection
@section('content')

{{-- Anasayfa için yerel utility'ler — temanın .text-success'i pastel yeşile (#def2d7)
     override edildiği için yeni .brand-* class'ları ekledim. Mevcut class'lar
     (.text-success, .bg-light vs) dokunulmadı; sadece anasayfa içerik bölümleri
     bu yeni class'ları kullanıyor. --}}
<style>
.brand-text       { color: #ea580c !important; }
.brand-text-dark  { color: #c2410c !important; }
.brand-bg-soft    { background-color: #f0f9f4 !important; }
.brand-divider    { color: #ea580c; letter-spacing: .12em; }
.brand-stat       { color: #ea580c; font-weight: 700; }
.brand-step-num   { color: #ea580c; font-weight: 700; }
.brand-cta {
    background-color: #ea580c !important;
    border-color: #ea580c !important;
    color: #fff !important;
}
.brand-cta:hover, .brand-cta:focus {
    background-color: #c2410c !important;
    border-color: #c2410c !important;
    color: #fff !important;
}
.brand-icon { color: #ea580c; }

/* ============ Section başlık standartı — altalta sade ============ */
.section-head {
    margin-bottom: 36px;
    max-width: 720px;
}
.section-h2 {
    font-weight: 800;
    font-size: clamp(1.35rem, 2.4vw, 1.9rem);
    line-height: 1.18;
    letter-spacing: -.022em;
    color: #0a0a0a;
    margin: 0 0 10px;
    text-transform: none;
}
.section-lead {
    color: #525252;
    margin: 0;
    font-size: .94rem;
    line-height: 1.65;
    max-width: 580px;
}
.section-lead-tight {
    color: #525252;
    font-size: .92rem;
    line-height: 1.6;
}

/* ============ Nasıl Çalışır — yatay editorial akış ============ */
.flow-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border-top: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
}
.flow-step {
    padding: 28px 24px;
    border-right: 1px solid #f1f1f1;
    position: relative;
    transition: background .2s ease;
}
.flow-step:last-child { border-right: 0; }
.flow-step:hover { background: #fafafa; }
.flow-step-num {
    color: #fdba74;
    font-weight: 800;
    font-size: 2rem;
    line-height: 1;
    letter-spacing: -.04em;
    margin-bottom: 16px;
    font-feature-settings: "tnum";
}
.flow-step-title {
    color: #0a0a0a;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 8px;
    text-transform: none;
    letter-spacing: -.01em;
}
.flow-step p {
    color: #525252;
    font-size: .85rem;
    line-height: 1.6;
    margin: 0;
}
.flow-step:hover .flow-step-num { color: #ea580c; }

@media (max-width: 991px) {
    .flow-steps { grid-template-columns: 1fr 1fr; }
    .flow-step:nth-child(2) { border-right: 0; }
    .flow-step:nth-child(odd) { border-right: 1px solid #f1f1f1; }
    .flow-step:nth-child(1), .flow-step:nth-child(2) { border-bottom: 1px solid #f1f1f1; }
}
@media (max-width: 575px) {
    .flow-steps { grid-template-columns: 1fr; }
    .flow-step { border-right: 0 !important; border-bottom: 1px solid #f1f1f1 !important; }
    .flow-step:last-child { border-bottom: 0 !important; }
}

/* ============ Bayi Avantajları — kapsamlı kartlar ============ */
.advantages-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.advantage-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 16px;
    padding: 28px 28px 26px;
    display: grid;
    grid-template-columns: auto 1fr;
    column-gap: 20px;
    row-gap: 6px;
    align-items: start;
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}
.advantage-card:hover {
    border-color: #fdba74;
    box-shadow: 0 12px 28px rgba(234,88,12,.10);
    transform: translateY(-2px);
}
.advantage-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    grid-row: span 2;
    box-shadow: 0 6px 16px rgba(234,88,12,.22);
}
.advantage-icon-2 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 6px 16px rgba(245,158,11,.22); }
.advantage-icon-3 { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); box-shadow: 0 6px 16px rgba(236,72,153,.22); }
.advantage-icon-4 { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); box-shadow: 0 6px 16px rgba(20,184,166,.22); }

.advantage-title {
    color: #0a0a0a;
    font-weight: 700;
    font-size: 1.02rem;
    margin: 2px 0 4px;
    letter-spacing: -.005em;
    text-transform: none;
}
.advantage-card p {
    color: #525252;
    font-size: .87rem;
    line-height: 1.65;
    margin: 0;
}
@media (max-width: 768px) {
    .advantages-grid { grid-template-columns: 1fr; }
    .advantage-card { padding: 22px; }
}

/* ============ SSS aside ============ */
.faq-aside {
    position: sticky;
    top: 100px;
    padding-right: 8px;
}
.faq-aside .section-h2 { margin-bottom: 14px; }
.faq-aside-link {
    color: #ea580c;
    border-bottom: 1px solid rgba(234,88,12,.30);
    text-decoration: none;
    font-weight: 600;
    transition: color .15s, border-color .15s;
}
.faq-aside-link:hover { color: #c2410c; border-color: #c2410c; }
@media (max-width: 991px) {
    .faq-aside { position: static; padding-right: 0; }
}

/* Accordion item — daha sade ve hizalı */
.accordion-item {
    background: #fff !important;
    border: 1px solid #e5e5e5 !important;
    border-radius: 12px !important;
    margin-bottom: 10px;
    overflow: hidden;
}
.accordion-button {
    background: #fff !important;
    color: #0a0a0a !important;
    font-weight: 600 !important;
    padding: 16px 20px !important;
    font-size: .95rem !important;
    box-shadow: none !important;
}
.accordion-button:not(.collapsed) {
    background: #fff7ed !important;
    color: #9a3412 !important;
}
.accordion-button:focus {
    box-shadow: 0 0 0 3px rgba(234,88,12,.14) !important;
    border-color: transparent !important;
}
.accordion-body {
    color: #525252 !important;
    line-height: 1.7;
    padding: 4px 20px 20px !important;
    background: #fff !important;
}

/* ============ Final CTA bant ============ */
.cta-band {
    background: #0a0a0a;
    background-image: linear-gradient(135deg, #0a0a0a 0%, #431407 60%, #0a0a0a 100%);
    border-radius: 20px;
    padding: 44px 48px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.cta-band::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 360px; height: 360px;
    background: radial-gradient(circle, rgba(234,88,12,.30), transparent 65%);
    pointer-events: none;
}
.cta-band-text { position: relative; z-index: 1; max-width: 560px; }
.cta-band-title {
    color: #fff;
    font-weight: 800;
    font-size: clamp(1.2rem, 2.1vw, 1.6rem);
    line-height: 1.18;
    letter-spacing: -.022em;
    margin-bottom: 10px;
}
.cta-band-text p {
    color: rgba(255,255,255,.78);
    margin: 0;
    font-size: .92rem;
    line-height: 1.6;
}
.cta-band-text a {
    color: #fdba74;
    text-decoration: none;
    border-bottom: 1px solid rgba(253,186,116,.30);
    font-weight: 600;
}
.cta-band-text a:hover { color: #fff; border-color: #fff; }
.cta-band-action { position: relative; z-index: 1; }

@media (max-width: 768px) {
    .cta-band { padding: 32px 28px; }
}

/* ============ B2B Hero — editorial dark card ============ */
.b2b-hero {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: #0a0a0a;
    min-height: 480px;
    isolation: isolate;
}
.b2b-hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}
.b2b-hero-bg img {
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.45;
    filter: grayscale(20%) contrast(1.05);
}
.b2b-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 1;
    background:
        radial-gradient(circle at 75% 20%, rgba(234,88,12,.45), transparent 55%),
        linear-gradient(135deg, rgba(10,10,10,.92) 0%, rgba(30,27,75,.85) 50%, rgba(10,10,10,.85) 100%);
}
.b2b-hero-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 56px;
    padding: 64px 56px;
    align-items: center;
}
.b2b-hero-content { color: #fff; max-width: 560px; }
.b2b-hero-eyebrow {
    display: inline-block;
    color: #fdba74;
    text-transform: uppercase;
    letter-spacing: .22em;
    font-size: .72rem;
    font-weight: 600;
    margin-bottom: 18px;
    padding: 4px 12px;
    border: 1px solid rgba(253,186,116,.30);
    border-radius: 999px;
    background: rgba(234,88,12,.10);
    backdrop-filter: blur(8px);
}
.b2b-hero-title {
    color: #fff;
    font-weight: 800;
    font-size: clamp(1.7rem, 3.6vw, 2.7rem);
    line-height: 1.08;
    letter-spacing: -.025em;
    margin: 0 0 22px;
    font-family: inherit !important;
}
.b2b-hero-title-accent {
    background: linear-gradient(135deg, #fdba74 0%, #fb923c 60%, #fed7aa 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
.b2b-hero-lead {
    color: rgba(255,255,255,.85);
    font-size: 1.02rem;
    line-height: 1.65;
    margin-bottom: 28px;
    max-width: 540px;
}
.b2b-hero-actions {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.b2b-hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    color: #0a0a0a;
    padding: 13px 24px;
    border-radius: 999px;
    font-weight: 700;
    font-size: .88rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    transition: transform .2s ease, box-shadow .2s ease, background .15s ease;
    box-shadow: 0 12px 30px rgba(0,0,0,.20);
    text-decoration: none;
}
.b2b-hero-cta:hover {
    background: #fff7ed;
    color: #9a3412;
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(0,0,0,.30);
}
.b2b-hero-cta i { font-size: .8rem; transition: transform .2s ease; }
.b2b-hero-cta:hover i { transform: translateX(3px); }
.b2b-hero-link {
    color: #fdba74;
    font-weight: 600;
    font-size: .88rem;
    text-decoration: none;
    transition: color .15s ease;
}
.b2b-hero-link:hover { color: #fff; }

/* Sağdaki feature kartları — 4 detaylı, floating cloud animasyonlu */
.b2b-hero-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    perspective: 1000px;
}

@keyframes cloud-float-1 {
    0%, 100% { transform: translate(0, 0) rotate(-1.6deg); }
    50%      { transform: translate(-6px, -10px) rotate(0.4deg); }
}
@keyframes cloud-float-2 {
    0%, 100% { transform: translate(0, 0) rotate(2deg); }
    50%      { transform: translate(8px, -8px) rotate(-0.6deg); }
}
@keyframes cloud-float-3 {
    0%, 100% { transform: translate(0, 0) rotate(-2.2deg); }
    50%      { transform: translate(-10px, 12px) rotate(0.2deg); }
}
@keyframes cloud-float-4 {
    0%, 100% { transform: translate(0, 0) rotate(1.5deg); }
    50%      { transform: translate(10px, 8px) rotate(-0.8deg); }
}

.b2b-hero-tag {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.14);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    color: #fff;
    padding: 16px 16px 14px;
    border-radius: 16px;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
    will-change: transform;
    position: relative;
}
.b2b-hero-tag:hover {
    background: rgba(255,255,255,.12);
    border-color: rgba(253,186,116,.45);
    box-shadow: 0 12px 32px rgba(0,0,0,.30), 0 0 0 1px rgba(253,186,116,.30);
    animation-play-state: paused;
}
.b2b-hero-tag--1 { animation: cloud-float-1 8.5s ease-in-out 0s infinite; margin-top: 0;     margin-left: -6px; }
.b2b-hero-tag--2 { animation: cloud-float-2 9.2s ease-in-out 0.6s infinite; margin-top: 14px; margin-right: -4px; }
.b2b-hero-tag--3 { animation: cloud-float-3 7.8s ease-in-out 1.4s infinite; margin-top: -8px;  margin-left: 4px; }
.b2b-hero-tag--4 { animation: cloud-float-4 10s   ease-in-out 0.3s infinite; margin-top: 6px;   margin-right: -8px; }

.b2b-hero-tag-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(253,186,116,.18);
    color: #fdba74;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-bottom: 10px;
    border: 1px solid rgba(253,186,116,.22);
}
.b2b-hero-tag-title {
    color: #fff;
    font-weight: 700;
    font-size: .85rem;
    margin: 0 0 4px;
    letter-spacing: -.005em;
    text-transform: none;
}
.b2b-hero-tag-desc {
    color: rgba(255,255,255,.72);
    font-size: .75rem;
    line-height: 1.55;
    margin: 0;
}

@media (prefers-reduced-motion: reduce) {
    .b2b-hero-tag--1, .b2b-hero-tag--2, .b2b-hero-tag--3, .b2b-hero-tag--4 {
        animation: none;
        transform: none;
    }
}

/* Sağ üst floating mini kart (overlap) */
.b2b-hero-floating {
    position: absolute;
    z-index: 3;
    right: 32px;
    bottom: 32px;
    background: #fff;
    border-radius: 14px;
    padding: 8px;
    box-shadow: 0 24px 60px rgba(0,0,0,.40);
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 280px;
    transform: rotate(-2deg);
    transition: transform .3s ease;
}
.b2b-hero-floating:hover { transform: rotate(0) translateY(-4px); }
.b2b-hero-floating img {
    width: 60px; height: 60px;
    object-fit: cover;
    border-radius: 10px;
}
.b2b-hero-floating-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-right: 10px;
}
.b2b-hero-floating-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #ea580c;
    box-shadow: 0 0 0 4px rgba(234,88,12,.18);
    flex-shrink: 0;
}
.b2b-hero-floating-info strong {
    display: block;
    color: #0a0a0a;
    font-size: .82rem;
    font-weight: 700;
    line-height: 1.2;
}
.b2b-hero-floating-info em {
    display: block;
    color: #737373;
    font-style: normal;
    font-size: .7rem;
    margin-top: 2px;
}

@media (max-width: 991px) {
    .b2b-hero-grid {
        grid-template-columns: 1fr;
        padding: 44px 28px;
        gap: 36px;
    }
    .b2b-hero-floating { display: none; }
}
@media (max-width: 575px) {
    .b2b-hero-meta { grid-template-columns: 1fr; }
    .b2b-hero-grid { padding: 36px 24px; }
}

/* B2B intro asimetrik kolaj */
.b2b-collage {
    position: relative;
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 14px;
    height: 460px;
}
.b2b-collage-main {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(15,23,42,.10);
    transition: transform .3s ease;
}
.b2b-collage-main:hover { transform: translateY(-4px); }
.b2b-collage-main img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.b2b-collage-tag {
    position: absolute;
    top: 16px; left: 16px;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 600;
    color: #171717;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: .03em;
    box-shadow: 0 4px 12px rgba(15,23,42,.08);
}
.b2b-collage-tag-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #ea580c;
    box-shadow: 0 0 0 3px rgba(234,88,12,.20);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 3px rgba(234,88,12,.20); }
    50% { box-shadow: 0 0 0 6px rgba(234,88,12,.10); }
}
.b2b-collage-side {
    display: grid;
    grid-template-rows: 1fr 1fr 0.85fr;
    gap: 14px;
}
.b2b-collage-thumb {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(15,23,42,.08);
    transition: transform .3s ease;
    background: #f5f5f5;
}
.b2b-collage-thumb:hover { transform: translateY(-3px); }
.b2b-collage-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.b2b-collage-mini-stat {
    background: #0a0a0a;
    background-image: linear-gradient(135deg, #7c2d12 0%, #431407 50%, #0a0a0a 100%);
    color: #fff;
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(15,23,42,.18);
}
.b2b-collage-stat-number {
    font-size: 1.65rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.02em;
    background: linear-gradient(135deg, #fdba74, #fb923c);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 4px;
}
.b2b-collage-stat-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .12em;
    line-height: 1.3;
    color: rgba(255,255,255,.7);
    font-weight: 500;
}
@media (max-width: 768px) {
    .b2b-collage { height: 340px; }
}

/* Numune Albümler — fotograf galeri (mosaic) */
.showcase-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    grid-auto-rows: 140px;
    gap: 12px;
}
.showcase-grid .show-tile {
    border-radius: 12px;
    overflow: hidden;
    background: #f5f5f5;
    position: relative;
    box-shadow: 0 4px 12px rgba(15,23,42,.06);
    transition: transform .3s ease, box-shadow .3s ease;
}
.showcase-grid .show-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15,23,42,.10);
}
.showcase-grid .show-tile img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.showcase-grid .show-tile:hover img { transform: scale(1.06); }
.showcase-grid .show-tile-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(10,10,10,.65));
    display: flex;
    align-items: flex-end;
    padding: 14px;
    opacity: 0;
    transition: opacity .25s ease;
}
.showcase-grid .show-tile:hover .show-tile-overlay { opacity: 1; }
.showcase-grid .show-tile-title {
    color: #fff;
    font-weight: 600;
    font-size: .85rem;
    letter-spacing: -.005em;
}
.showcase-tile-1 { grid-column: span 3; grid-row: span 2; }
.showcase-tile-2 { grid-column: span 3; grid-row: span 2; }
.showcase-tile-3 { grid-column: span 2; grid-row: span 2; }
.showcase-tile-4 { grid-column: span 2; grid-row: span 2; }
.showcase-tile-5 { grid-column: span 2; grid-row: span 2; }

@media (max-width: 991px) {
    .showcase-grid { grid-template-columns: repeat(4, 1fr); grid-auto-rows: 120px; }
    .showcase-tile-1, .showcase-tile-2 { grid-column: span 4; }
    .showcase-tile-3, .showcase-tile-4, .showcase-tile-5 { grid-column: span 2; grid-row: span 2; }
}
@media (max-width: 575px) {
    .showcase-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 140px; }
    .showcase-tile-1, .showcase-tile-2, .showcase-tile-3, .showcase-tile-4, .showcase-tile-5 {
        grid-column: span 2; grid-row: span 1;
    }
}

/* Featured highlight — tek vurgulu görsel kartı */
.feature-highlight {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    min-height: 360px;
    background: #0a0a0a;
}
.feature-highlight img {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: .55;
}
.feature-highlight-content {
    position: relative;
    z-index: 2;
    padding: 48px;
    color: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.feature-highlight-content h3 {
    color: #fff;
    font-weight: 800;
    font-size: clamp(1.4rem, 2.4vw, 1.85rem);
    letter-spacing: -.025em;
    margin-bottom: 14px;
}
.feature-highlight-content p {
    color: rgba(255,255,255,.85);
    max-width: 460px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .feature-highlight-content { padding: 32px; }
    .feature-highlight-content h3 { font-size: 1.5rem; }
}
</style>

<main>
    {{-- Eski swiper slider kaldırıldı — modern arayüzlerde basit kaçıyordu.
         Yerine doğrudan B2B editorial hero ile başlıyoruz. --}}

    {{-- ======== B2B Hero — editorial dark hero (yatay) ======== --}}
    <section class="container pt-4 pt-lg-5">
      <div class="b2b-hero">
        <div class="b2b-hero-bg">
          <img src="/images/1758117104_ORFb8xojb0.JPG" alt="" loading="lazy">
        </div>
        <div class="b2b-hero-grid">
          <div class="b2b-hero-content">
            <h2 class="b2b-hero-title">
              Fotoğrafçıların güvendiği<br>
              <span class="b2b-hero-title-accent">albüm &amp; baskı çözümü</span>
            </h2>
            <p class="b2b-hero-lead">
              {{ $siteSettings->company_title ?? $siteSettings->title ?? 'KenAlbüm' }} olarak profesyonel fotoğrafçılar, stüdyolar ve baskı atölyeleri için yüksek kaliteli üretim yapıyoruz. Siz müşterilerinizle ilgilenirken biz üretimi tamamlayıp size veya nihai müşterinize ulaştırırız.
            </p>
            <div class="b2b-hero-actions">
              @auth
                <a href="#" class="b2b-hero-cta" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal">
                  <span>Hemen Sipariş Ver</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('profile.index') }}" class="b2b-hero-link">Bayi Panelim →</a>
              @else
                <a href="{{ route('login') }}" class="b2b-hero-cta">
                  <span>Bayi Girişi</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('register') }}" class="b2b-hero-link">Bayi Başvurusu →</a>
              @endauth
            </div>
          </div>

          <div class="b2b-hero-meta">
            <div class="b2b-hero-tag b2b-hero-tag--1">
              <div class="b2b-hero-tag-icon"><i class="fas fa-percentage"></i></div>
              <h6 class="b2b-hero-tag-title">Bayiye Özel Fiyat</h6>
              <p class="b2b-hero-tag-desc">Sipariş hacminize göre kademeli indirim. Listede gördüğünüz, ödediğiniz net fiyat.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--2">
              <div class="b2b-hero-tag-icon"><i class="fas fa-bolt"></i></div>
              <h6 class="b2b-hero-tag-title">Acil Üretim</h6>
              <p class="b2b-hero-tag-desc">Yaklaşan tarihiniz mi var? Önceliklendirip planlanan günde teslim ediyoruz.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--3">
              <div class="b2b-hero-tag-icon"><i class="fas fa-pen-fancy"></i></div>
              <h6 class="b2b-hero-tag-title">Tasarım & Dizgi</h6>
              <p class="b2b-hero-tag-desc">Tasarımı kendiniz yapın ya da bize bırakın. Dizgi, rötüş hizmeti de var.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--4">
              <div class="b2b-hero-tag-icon"><i class="fas fa-shipping-fast"></i></div>
              <h6 class="b2b-hero-tag-title">Türkiye Geneli Kargo</h6>
              <p class="b2b-hero-tag-desc">Anlaşmalı kargolarla hızlı teslimat. Dilerseniz nihai müşterinize gönderilir.</p>
            </div>
          </div>
        </div>

        {{-- Floating mini-album görsel (sağ üst köşe) --}}
        <div class="b2b-hero-floating">
          <img src="/images/1758033483_b3GibIqYHN.jpg" alt="Lily Albüm" loading="lazy">
          <div class="b2b-hero-floating-info">
            <span class="b2b-hero-floating-dot"></span>
            <div>
              <strong>Premium Seri</strong>
              <em>10+ yıl tecrübe</em>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- 'Bayi Avantajları' detaylı section'ı kaldırıldı, içerik hero'nun
         sağ tag'lerine taşındı (floating cloud animasyonlu). --}}
    {{-- 'Numune Albümlerimizden' galeri kaldırıldı (gereksiz). --}}

    {{-- ======== Nasıl Çalışır — hero ile feature highlight arasında ======== --}}
    <section class="container py-5">
      <div class="section-head">
        <h2 class="section-h2">Sipariş vermek<br>yalnızca 4 adım</h2>
        <p class="section-lead">
          Header'daki <strong>Sipariş Ver</strong> menüsüyle başlayın, yapılandırın, dosyalarınızı yükleyin — gerisini bize bırakın.
        </p>
      </div>

      <div class="flow-steps">
        <div class="flow-step">
          <div class="flow-step-num">01</div>
          <h6 class="flow-step-title">Ürünü seçin</h6>
          <p>Sipariş Ver menüsünden veya kategorilerden ürünü seçin. Aynı siparişten yeniden vermek için <em>Geçmişim</em> sekmesini kullanın.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">02</div>
          <h6 class="flow-step-title">Özelleştirin</h6>
          <p>Ebat, kumaş, renk, paket ve diğer detayları adım adım seçin. Toplam fiyatınız anlık güncellenir.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">03</div>
          <h6 class="flow-step-title">Dosyaları yükleyin</h6>
          <p>Kapak ve iç sayfa tasarımlarınızı tek bir ZIP olarak yükleyin. Hatalı dosyalar tarafımızdan kontrol edilir.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">04</div>
          <h6 class="flow-step-title">Üretim &amp; kargo</h6>
          <p>Onay sonrası üretim başlar. Durum takibini panelden anlık görür, kargo takibinizi yaparsınız.</p>
        </div>
      </div>
    </section>

    {{-- ======== Feature highlight — Nasıl Çalışır ile Kategoriler arasında, CTA'sız ======== --}}
    <section class="container py-3">
      <div class="feature-highlight">
        <img src="/images/1758117104_ORFb8xojb0.JPG" alt="Premium baskı" loading="lazy">
        <div class="feature-highlight-content">
          <small class="text-uppercase fw-bold d-block mb-2" style="color:#fdba74; letter-spacing:.18em; font-size:.72rem;">— Yeni nesil baskı</small>
          <h3>Müşterilerinizi etkileyecek<br>premium kalite</h3>
          <p>Düğün, nişan, doğum ve özel anlar için profesyonel kalitede albüm üretimi. Yıllarca koruma sağlayan dayanıklı malzemeler, modern bağlama teknikleri ve renk derinliği yüksek baskı.</p>
        </div>
      </div>
    </section>

    {{-- 'Kategoriler intro' ve homepageCategories carousel'ları kaldırıldı — anasayfada artık ürün listelemiyoruz. --}}

    {{-- ======== Sıkça Sorulan Sorular ======== --}}
    <section class="container py-5">
      <div class="row g-5 align-items-start">
        <div class="col-lg-4">
          <div class="faq-aside">
            <h2 class="section-h2">Aklınızda<br>soru var mı?</h2>
            <p class="section-lead-tight">Bayi sürecimizle ilgili sıkça sorulan sorulara verdiğimiz cevaplar. Başka bir konu olursa <a href="#" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" class="faq-aside-link">bize ulaşın</a>.</p>
          </div>
        </div>
        <div class="col-lg-8">
            <div class="accordion" id="homeFaqAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB1">
                    Bayi başvurusunu nasıl yapabilirim?
                  </button>
                </h2>
                <div id="faqB1" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Üst menüden <strong>Bayi Başvurusu</strong> bağlantısıyla kayıt formunu doldurup gönderebilirsiniz. Ekibimiz başvurunuzu inceleyip onay verdikten sonra panelinize giriş yapabilir, bayi fiyatlarıyla sipariş vermeye başlayabilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB2">
                    Üretim ve teslimat süresi ne kadar?
                  </button>
                </h2>
                <div id="faqB2" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Standart üretim süremiz <strong>2-5 iş günü</strong> arasındadır. Acil üretim seçeneği ile bu süre 1 iş gününe iner. Kargolama anlaşmalı firmalar üzerinden yapılır; gönderim tarihi panelinizden takip edilebilir.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB3">
                    Hangi dosya formatlarını kabul ediyorsunuz?
                  </button>
                </h2>
                <div id="faqB3" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Kapak ve iç sayfaları için <strong>PSD, AI, PDF, TIFF, JPG</strong> formatlarını kabul ediyoruz. Tüm dosyaları tek bir <strong>ZIP/RAR</strong> arşivi olarak siparişin son adımında yükleyebilirsiniz. Tasarım şablonlarımızı ürün detay sayfasından indirebilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB4">
                    Sık tekrarlayan siparişlerimi hızlandırabilir miyim?
                  </button>
                </h2>
                <div id="faqB4" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Evet. Header'daki <strong>Sipariş Ver</strong> menüsünde <em>Geçmişim</em> sekmesinden önceki siparişlerinize ulaşabilir, tek tıkla aynısını sepete ekleyebilir veya küçük değişiklikler için "Özelleştir" diyerek wizard'ı önceki seçimlerle önceden doldurabilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB5">
                    Ödeme ve fatura nasıl işliyor?
                  </button>
                </h2>
                <div id="faqB5" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Bayi panelinizdeki <strong>bakiye</strong> üzerinden ödeme yapılır. Bakiyenizi havale/EFT ile yükleyebilir, her sipariş tutarı bakiyenizden otomatik düşülür. Tüm siparişler için panelinizden e-fatura/dekont indirilebilir.
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </section>

    {{-- ======== Final CTA — koyu kompakt bant ======== --}}
    <section class="container py-5">
      <div class="cta-band">
        <div class="cta-band-text">
          <h3 class="cta-band-title">Bayi olun,<br>baskıyı bize bırakın.</h3>
          <p>
            Stüdyonuza özel bayi indirimleri ve öncelikli üretim avantajları için başvurun.
            @if($siteSettings->phone)
              Sorularınız için <a href="tel:{{ preg_replace('/[^0-9+]/','',$siteSettings->phone) }}">{{ $siteSettings->phone }}</a>
            @endif
          </p>
        </div>
        <div class="cta-band-action">
          @auth
            <a href="#" class="b2b-hero-cta" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal">
              <span>Sipariş Ver</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          @else
            <a href="{{ route('register') }}" class="b2b-hero-cta">
              <span>Bayi Başvurusu</span>
              <i class="fas fa-arrow-right"></i>
            </a>
          @endauth
        </div>
      </div>
    </section>

  </main>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Ana slider için Swiper initialize
    if ($('.slideshow.type4').length) {
        new Swiper('.slideshow.type4 .swiper-container', {
            autoplay: {
                delay: 5000
            },
            navigation: {
                nextEl: '.slideshow__next',
                prevEl: '.slideshow__prev'
            },
            pagination: false,
            slidesPerView: 1,
            effect: 'fade',
            loop: true
        });
    }

    // Kategori ürün swiperları için
    $('.products-carousel .swiper-container').each(function(index, element) {
        new Swiper(element, {
            slidesPerView: 1,
            spaceBetween: 30,
            breakpoints: {
                576: {
                    slidesPerView: 2,
                    spaceBetween: 30
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
    });
});
</script>
@endsection
