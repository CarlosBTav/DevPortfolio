@props([
    'variant' => 'viewport',
    'interactive' => true,
    'wavesOverlay' => true,
    /*
     * Optional theme overrides (defaults match original /cv look).
     * backgroundColor: any CSS color (e.g. #f9fafb, rgb(...))
     * dotColor: comma-separated R,G,B for canvas dots (e.g. 99, 102, 241)
     */
    'backgroundColor' => null,
    'dotColor' => null,
])

@php
    $stackClass = match ($variant) {
        'section' => 'ai-dots-bg-stack ai-dots-bg-stack--section',
        'hero' => 'ai-dots-bg-stack ai-dots-bg-stack--hero',
        default => 'ai-dots-bg-stack ai-dots-bg-stack--viewport',
    };

    $styleParts = [];
    if ($backgroundColor !== null && $backgroundColor !== '') {
        $bg = e($backgroundColor);
        $styleParts[] = '--ai-dots-canvas-bg: ' . $bg;
        $styleParts[] = '--ai-dots-mask-bg: ' . $bg;
    }
    if ($dotColor !== null && $dotColor !== '') {
        $styleParts[] = '--ai-dots-dot: ' . e($dotColor);
    }
    $inlineStyle = count($styleParts) ? implode('; ', $styleParts) : null;
@endphp

@once
    <style>
        .ai-dots-bg-stack {
            pointer-events: none;
            --ai-dots-canvas-bg: rgb(245, 245, 240);
            --ai-dots-mask-bg: rgb(245, 245, 240);
            /* R,G,B — used as rgb(var(--ai-dots-dot)) in script */
            --ai-dots-dot: 232, 118, 74;
            /* Blurred wave ribbons (SVG strokes, wide → narrow) */
            --ai-dots-wave-wide: #d8d8d2;
            --ai-dots-wave-mid: #77776f;
            --ai-dots-wave-core: #000000;
            --ai-dots-mask-blend: lighten;
            --ai-dots-glow-blend: multiply;
            --ai-dots-glow-gradient: radial-gradient(circle at 50% 50%, #9a9a92 0%, #d8d8d4 52%, transparent 84%);
        }
        .ai-dots-bg-stack--viewport {
            position: fixed;
            left: 0;
            top: 0;
            width: 100vw;
            max-width: 100vw;
            height: 100vh;
            height: 100lvh;
            box-sizing: border-box;
            z-index: 0;
            overflow: hidden;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }
        .ai-dots-bg-stack--section {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .ai-dots-bg-stack--hero {
            position: absolute;
            left: -30%;
            right: -30%;
            top: 0;
            bottom: 0;
            z-index: 1;
            overflow: hidden;
            background: transparent;
            --ai-dots-canvas-bg: transparent;
            --ai-dots-mask-bg: transparent;
            --ai-dots-dot: var(--hr-dots-dot, 99, 102, 241);
            --ai-dots-fade-w: var(--hr-dots-fade-w, 22%);
            --ai-dots-fade-h: var(--hr-dots-fade-h, 16%);
            -webkit-mask-image:
                linear-gradient(to bottom, transparent 0%, #000 var(--ai-dots-fade-h), #000 calc(100% - var(--ai-dots-fade-h)), transparent 100%),
                linear-gradient(to right, transparent 0%, #000 var(--ai-dots-fade-w), #000 calc(100% - var(--ai-dots-fade-w)), transparent 100%);
            mask-image:
                linear-gradient(to bottom, transparent 0%, #000 var(--ai-dots-fade-h), #000 calc(100% - var(--ai-dots-fade-h)), transparent 100%),
                linear-gradient(to right, transparent 0%, #000 var(--ai-dots-fade-w), #000 calc(100% - var(--ai-dots-fade-w)), transparent 100%);
            -webkit-mask-composite: source-in;
            mask-composite: intersect;
            mask-mode: alpha;
            -webkit-mask-size: 100% 100%;
            mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
        }
        .ai-dots-bg-stack .js-ai-dots-canvas {
            display: block;
            width: 100%;
            height: 100%;
            background-color: var(--ai-dots-canvas-bg);
        }
        .ai-dots-bg-stack--hero .js-ai-dots-canvas {
            background-color: transparent;
        }
        .ai-dots-bg-stack--viewport .js-ai-dots-canvas {
            position: absolute;
            inset: 0;
        }
        .ai-dots-bg-stack .ai-dots-mask-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-color: var(--ai-dots-mask-bg);
            mix-blend-mode: var(--ai-dots-mask-blend);
            z-index: 1;
            overflow: hidden;
        }
        .ai-dots-bg-stack .ai-dots-wave-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: rotate(-10deg);
            filter: blur(32px);
            z-index: 1;
        }
        .ai-dots-bg-stack--viewport .ai-dots-wave-container {
            width: 200vw;
            height: 100vh;
            height: 100lvh;
            margin-left: -100vw;
            margin-top: -50vh;
            margin-top: -50lvh;
        }
        .ai-dots-bg-stack--section .ai-dots-wave-container {
            width: 200%;
            height: 100%;
            margin-left: -100%;
            margin-top: -50%;
        }
        .ai-dots-bg-stack .ai-dots-wave-path {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            will-change: transform;
        }
        .ai-dots-bg-stack .ai-dots-wave-1 {
            animation: aiDotsMoveWave1 7.5s ease-in-out infinite alternate;
        }
        .ai-dots-bg-stack .ai-dots-wave-2 {
            animation: aiDotsMoveWave2 9.5s ease-in-out infinite alternate-reverse;
        }
        .ai-dots-bg-stack .ai-dots-wave-3 {
            animation: aiDotsMoveWave3 11s ease-in-out infinite alternate;
        }
        @keyframes aiDotsMoveWave1 {
            0%   { transform: translate(-30vw, -10vh) rotate(-5deg) scale(0.95, 0.72); }
            50%  { transform: translate(0vw, 15vh) rotate(3deg) scale(1.08, 1.2); }
            100% { transform: translate(30vw, -15vh) rotate(-2deg) scale(1.02, 0.86); }
        }
        @keyframes aiDotsMoveWave2 {
            0%   { transform: translate(30vw, 15vh) rotate(5deg) scale(1.08, 1.12); }
            50%  { transform: translate(0vw, -10vh) rotate(-2deg) scale(0.92, 0.84); }
            100% { transform: translate(-30vw, 10vh) rotate(3deg) scale(1.12, 1.28); }
        }
        @keyframes aiDotsMoveWave3 {
            0%   { transform: translate(-18vw, 18vh) rotate(7deg) scale(1.1, 0.78); }
            50%  { transform: translate(12vw, -6vh) rotate(-5deg) scale(0.88, 1.22); }
            100% { transform: translate(24vw, 14vh) rotate(4deg) scale(1.04, 0.96); }
        }
        .ai-dots-bg-stack .js-ai-dots-glow,
        .ai-dots-bg-stack .js-ai-dots-ripple-glow {
            position: absolute;
            width: 680px;
            height: 680px;
            background: var(--ai-dots-glow-gradient);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            mix-blend-mode: var(--ai-dots-glow-blend);
            filter: blur(40px);
            z-index: 2;
            opacity: 0;
            will-change: transform, opacity;
        }
        body:has(> .ai-dots-bg-stack--viewport) .page {
            position: relative;
            z-index: 10;
        }
        @media print {
            .ai-dots-bg-stack { display: none !important; }
        }
    </style>
@endonce

@if($interactive)
<div {{ $attributes->merge(['class' => $stackClass]) }} @if($inlineStyle) style="{{ $inlineStyle }}" @endif data-ai-dots-root="{{ $variant }}" @if($variant === 'hero') data-ai-dots-hit="stage" data-spacing="22" @endif aria-hidden="true">
    <canvas class="js-ai-dots-canvas"></canvas>
    @if($wavesOverlay)
    <div class="ai-dots-mask-layer">
        <div class="ai-dots-wave-container">
            <svg class="ai-dots-wave-path ai-dots-wave-1" viewBox="0 0 1000 400" preserveAspectRatio="none">
                <path d="M -200,200 C 100,50 300,350 600,200 C 900,50 1100,350 1400,200" fill="none" stroke="var(--ai-dots-wave-wide)" stroke-width="128" stroke-linecap="round"/>
                <path d="M -200,200 C 100,50 300,350 600,200 C 900,50 1100,350 1400,200" fill="none" stroke="var(--ai-dots-wave-mid)" stroke-width="86" stroke-linecap="round"/>
                <path d="M -200,200 C 100,50 300,350 600,200 C 900,50 1100,350 1400,200" fill="none" stroke="var(--ai-dots-wave-core)" stroke-width="46" stroke-linecap="round"/>
            </svg>
            <svg class="ai-dots-wave-path ai-dots-wave-2" viewBox="0 0 1000 400" preserveAspectRatio="none">
                <path d="M -200,200 C 200,350 400,50 700,200 C 1000,350 1200,50 1400,200" fill="none" stroke="var(--ai-dots-wave-wide)" stroke-width="102" stroke-linecap="round"/>
                <path d="M -200,200 C 200,350 400,50 700,200 C 1000,350 1200,50 1400,200" fill="none" stroke="var(--ai-dots-wave-mid)" stroke-width="68" stroke-linecap="round"/>
                <path d="M -200,200 C 200,350 400,50 700,200 C 1000,350 1200,50 1400,200" fill="none" stroke="var(--ai-dots-wave-core)" stroke-width="38" stroke-linecap="round"/>
            </svg>
            <svg class="ai-dots-wave-path ai-dots-wave-3" viewBox="0 0 1000 400" preserveAspectRatio="none">
                <path d="M -200,180 C 80,310 280,90 540,210 C 800,330 980,80 1400,180" fill="none" stroke="var(--ai-dots-wave-wide)" stroke-width="112" stroke-linecap="round"/>
                <path d="M -200,180 C 80,310 280,90 540,210 C 800,330 980,80 1400,180" fill="none" stroke="var(--ai-dots-wave-mid)" stroke-width="74" stroke-linecap="round"/>
                <path d="M -200,180 C 80,310 280,90 540,210 C 800,330 980,80 1400,180" fill="none" stroke="var(--ai-dots-wave-core)" stroke-width="40" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="js-ai-dots-glow ai-dots-glow"></div>
    </div>
    @endif
</div>

@pushOnce('scripts', 'portfolio-ai-dots-background')
@vite('resources/js/ai-dots-background.js')
@endPushOnce

@else
<!-- ai-dots-background: interactive=false (sin capas ni script) -->
@endif
