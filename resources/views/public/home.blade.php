@extends('layouts.public')

@section('title', 'Carlos Codex | Desarrollo web y aplicaciones')
@section('meta_description', 'Desarrollo webs, aplicaciones y soluciones digitales a medida. Descubre mis proyectos y cuéntame qué producto necesitas construir.')

@php
    $selectedService = request()->query('service');
    $serviceLeadMessages = [
        'web-development' => 'Quiero una propuesta para desarrollo web. Mi objetivo es...',
        'app-development' => 'Quiero una propuesta para desarrollo de app. Mi objetivo es...',
        'custom-solutions' => 'Quiero hablar sobre una solución a medida para...',
    ];
    $prefilledContactMessage = old('content', $serviceLeadMessages[$selectedService] ?? '');
@endphp

@section('content')

    <!--
    |------------------------------------------------------------------|
    |  ################       HERO REDESIGN        ################    |
    |  Dark card design · WebGL shader · AI dots · Portrait           |
    |------------------------------------------------------------------|
    -->
    <style>
        /* ═══════════════════════════════════════════════════════════════
           THEME TOKENS (home) — edit only these two blocks to retune color
           ① LIGHT  default (no .dark on <html>)  ② DARK  html.dark      */
        html {
          color-scheme: light;
          --hr-shader-intensity: 0.52;
          /* WebGL: 1 = pastel luminous stripe (light); 0 = multiply-dark band (dark theme) */
          --hr-shader-pastel: 1;
          /* WebGL: min brightness from diagonal vignette (higher = less “shadow”) */
          --hr-shader-vign-floor: 0.97;
          /* Unused: WebGL hero uses transparent framebuffer (page bg shows through) */
          --hr-shader-bg: 255, 255, 255;
          --hr-shader-fluid-mix: 0.58;
          --hr-dots-dot: 99, 102, 241;
          --hr-portrait-shadow: 0 22px 48px rgba(15, 23, 42, 0.14);
          --hr-bg-html: #e8edf3;
          --hr-bg-base: #f1f5f9;
          --hr-bg-card: #ffffff;
          --hr-bg-card-alt: #f8fafc;
          --hr-bg-services-inset: #f8fafc;
          --hr-bg-services-grid: #eef2f7;
          --hr-border: rgba(15, 23, 42, 0.09);
          --hr-border-strong: rgba(15, 23, 42, 0.14);
          --hr-border-grid: rgba(15, 23, 42, 0.1);
          --hr-text: #0f172a;
          --hr-text-muted: #475569;
          --hr-text-faint: #64748b;
          --hr-heading: #0f172a;
          --hr-accent: #4f46e5;
          --hr-accent-2: #6366f1;
          --hr-accent-soft: #7c83f7;
          --hr-available: #15803d;
          --hr-dots-fade-w: 22%;
          --hr-dots-fade-h: 16%;
          --hr-idea-card-bg: rgba(255, 255, 255, 0.92);
          --hr-vig-mid: rgba(241, 245, 249, 0.5);
          --hr-vig-edge: rgba(241, 245, 249, 0.9);
          --hr-btn-ghost: rgba(15, 23, 42, 0.04);
          --hr-btn-ghost-hover: rgba(15, 23, 42, 0.08);
          --hr-idea-arrow-hover: rgba(15, 23, 42, 0.06);
          --hr-pill-hover-bg: rgba(15, 23, 42, 0.04);
          --hr-pill-hover-border: rgba(15, 23, 42, 0.18);
          --hr-pill-arrow-bg: rgba(15, 23, 42, 0.08);
          --hr-grid-inset-line: rgba(15, 23, 42, 0.06);
          /* Hero tech rail: AWS wordmark (“AWS” glyphs), orange smile unchanged */
          --tech-aws-letters-fill: #000000;
        }
        html.dark {
          color-scheme: dark;
          --hr-shader-intensity: 0.36;
          --hr-shader-pastel: 0;
          --hr-shader-vign-floor: 0.85;
          /* Near-black lifted with accent (indigo undertone) */
          --hr-shader-bg: 10, 11, 24;
          --hr-shader-fluid-mix: 0.55;
          --hr-dots-dot: 165, 180, 252;
          --hr-portrait-shadow: 0 24px 48px rgba(0, 0, 0, 0.72);
          --hr-bg-html: #050507;
          --hr-bg-base: #07070a;
          --hr-bg-card: #0e0f14;
          --hr-bg-card-alt: #11131a;
          --hr-bg-services-inset: #151821;
          --hr-bg-services-grid: #1e212c;
          --hr-border: rgba(255, 255, 255, 0.08);
          --hr-border-strong: rgba(255, 255, 255, 0.14);
          --hr-border-grid: rgba(255, 255, 255, 0.12);
          --hr-text: #e8ecf2;
          --hr-text-muted: #9aa0ad;
          --hr-text-faint: #6b7180;
          --hr-heading: #f5f6fa;
          --hr-accent: #5a61dd;
          --hr-accent-2: #818cf8;
          --hr-accent-soft: #7f90f6;
          --hr-available: #22c55e;
          --hr-dots-fade-w: 22%;
          --hr-dots-fade-h: 16%;
          --hr-idea-card-bg: rgba(20, 22, 30, 0.82);
          --hr-vig-mid: rgba(5, 5, 7, 0.55);
          --hr-vig-edge: rgba(5, 5, 7, 0.85);
          --hr-btn-ghost: rgba(255, 255, 255, 0.03);
          --hr-btn-ghost-hover: rgba(255, 255, 255, 0.06);
          --hr-idea-arrow-hover: rgba(255, 255, 255, 0.06);
          --hr-pill-hover-bg: rgba(255, 255, 255, 0.04);
          --hr-pill-hover-border: rgba(255, 255, 255, 0.22);
          --hr-pill-arrow-bg: rgba(255, 255, 255, 0.08);
          --hr-grid-inset-line: rgba(255, 255, 255, 0.04);
          --tech-aws-letters-fill: #ffffff;
        }

        body { background-color: var(--hr-bg-base) !important; }
        html { background-color: var(--hr-bg-html) !important; }

        /* ── Centered content wrapper ──────────────────────────────── */
        .hr-page {
          max-width: 1280px;
          margin: 0 auto;
          padding: 0 18px 60px;
          position: relative;
          z-index: 2;
        }

        /* ── Stats: sibling below hero; pulled up so portrait meets / sits behind strip ─ */
        .hr-stats-wrapper {
          max-width: 1280px;
          margin: -40px auto 0;
          padding: 0 18px;
          position: relative;
          z-index: 6;
        }

        /* ═══════════════════════════════════════════════════════════
           SERVICES SECTION — dark card design
           ═══════════════════════════════════════════════════════════ */
        .hr-services-section {
          padding: 80px 0 64px;
        }
        .hr-section-head {
          margin-bottom: 48px;
        }
        .hr-section-label {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          font-family: 'JetBrains Mono', monospace;
          font-size: 11px;
          font-weight: 600;
          letter-spacing: 0.14em;
          text-transform: uppercase;
          color: var(--hr-accent-2);
          margin-bottom: 20px;
        }
        .hr-section-label .hr-status-dot {
          background: var(--hr-accent);
          box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.18);
          animation: hr-pulse-indigo 2.4s ease-in-out infinite;
        }
        @keyframes hr-pulse-indigo {
          0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5); }
          50%       { box-shadow: 0 0 0 8px rgba(99, 102, 241, 0); }
        }
        .hr-section-title {
          font-family: 'Geist', system-ui, sans-serif;
          font-weight: 700;
          font-size: clamp(30px, 3.2vw, 46px);
          line-height: 1.1;
          letter-spacing: -0.02em;
          color: var(--hr-heading);
          margin: 0 0 14px;
        }
        .hr-section-sub {
          color: var(--hr-text-muted);
          font-size: 16px;
          line-height: 1.65;
          max-width: 560px;
          margin: 0;
        }
        .hr-services-grid {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 14px;
          margin-bottom: 20px;
        }
        .hr-service-card {
          background: var(--hr-bg-card);
          border: 1px solid var(--hr-border);
          border-radius: 20px;
          padding: 32px;
          display: flex;
          flex-direction: column;
          gap: 14px;
          transition: border-color 0.22s, background 0.22s, transform 0.22s;
          cursor: default;
        }
        .hr-service-card:hover {
          border-color: rgba(99, 102, 241, 0.35);
          background: var(--hr-bg-card-alt);
          transform: translateY(-2px);
        }
        .hr-service-icon {
          width: 46px;
          height: 46px;
          border-radius: 13px;
          background: rgba(99, 102, 241, 0.12);
          color: var(--hr-accent-2);
          display: inline-flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        }
        .hr-service-icon svg { width: 20px; height: 20px; }
        .hr-service-card h3 {
          font-family: 'Geist', system-ui, sans-serif;
          font-size: 17px;
          font-weight: 600;
          color: var(--hr-text);
          margin: 0;
          line-height: 1.3;
        }
        .hr-service-card p {
          font-size: 14px;
          line-height: 1.7;
          color: var(--hr-text-muted);
          margin: 0;
          flex-grow: 1;
        }
        .hr-service-link {
          display: inline-flex;
          align-items: center;
          gap: 6px;
          font-size: 13px;
          font-weight: 500;
          color: var(--hr-accent-2);
          text-decoration: none;
          margin-top: 4px;
          transition: gap 0.18s ease, color 0.18s;
        }
        .hr-service-link:hover { gap: 10px; color: var(--hr-accent-soft); }
        .hr-service-link svg { width: 14px; height: 14px; }

        /* ── Services CTA banner ─────────────────────────────────── */
        .hr-cta-banner {
          position: relative;
          border: 1px solid var(--hr-border-strong);
          background: linear-gradient(135deg,
            rgba(99, 102, 241, 0.09) 0%,
            rgba(14, 15, 20, 0) 55%
          );
          border-radius: 20px;
          padding: 36px 44px;
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 32px;
          overflow: hidden;
        }
        .hr-cta-banner::before {
          content: "";
          position: absolute;
          top: -60px;
          right: -60px;
          width: 220px;
          height: 220px;
          background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 60%);
          pointer-events: none;
        }
        .hr-cta-banner-label {
          display: inline-flex;
          align-items: center;
          gap: 6px;
          font-size: 12px;
          font-weight: 600;
          color: var(--hr-accent-2);
          background: rgba(99,102,241,0.1);
          border: 1px solid rgba(99,102,241,0.22);
          padding: 4px 12px;
          border-radius: 999px;
          margin-bottom: 12px;
        }
        .hr-cta-banner h3 {
          font-family: 'Geist', system-ui, sans-serif;
          font-size: 22px;
          font-weight: 700;
          color: var(--hr-heading);
          margin: 0 0 8px;
          line-height: 1.2;
        }
        .hr-cta-banner p {
          font-size: 14.5px;
          line-height: 1.65;
          color: var(--hr-text-muted);
          margin: 0;
          max-width: 540px;
        }
        .hr-cta-banner p strong { color: var(--hr-text); font-weight: 600; }
        .hr-cta-banner-actions {
          display: flex;
          flex-direction: column;
          align-items: flex-end;
          gap: 10px;
          flex-shrink: 0;
        }
        .hr-cta-banner-actions span {
          font-size: 12px;
          color: var(--hr-text-faint);
          white-space: nowrap;
        }

        /* ── Services responsive ─────────────────────────────────── */
        @media (max-width: 960px) {
          .hr-services-grid { grid-template-columns: 1fr; }
          .hr-cta-banner { flex-direction: column; align-items: flex-start; }
          .hr-cta-banner-actions { align-items: flex-start; }
        }
        @media (max-width: 600px) {
          .hr-services-section { padding: 56px 0 48px; }
          .hr-cta-banner { padding: 28px 24px; }
        }

        /* ── Shared section card shell (services, skills, contact) ──── */
        .hr-section-card {
          border: 1px solid var(--hr-border);
          background: var(--hr-bg-card);
          border-radius: 22px;
          padding: 32px;
          position: relative;
        }

        #contact .hr-contact-card {
          overflow: hidden;
          isolation: isolate;
          border-color: var(--hr-border-grid);
          background:
            radial-gradient(circle at top left, rgba(99, 102, 241, 0.16), transparent 34%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
          box-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
        }
        html.dark #contact .hr-contact-card {
          background:
            radial-gradient(circle at top left, rgba(129, 140, 248, 0.24), transparent 36%),
            linear-gradient(135deg, rgba(18, 19, 28, 0.98), rgba(10, 11, 18, 0.94));
          box-shadow: 0 28px 80px rgba(0, 0, 0, 0.44);
        }
        #contact .hr-contact-card::before {
          content: "";
          position: absolute;
          inset: 18px 18px auto auto;
          width: 160px;
          height: 160px;
          border-radius: 999px;
          background: rgba(99, 102, 241, 0.12);
          filter: blur(28px);
          z-index: -1;
        }
        #contact .hr-contact-card::after {
          content: "";
          position: absolute;
          inset: 0;
          background-image:
            linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
          background-size: 34px 34px;
          mask-image: linear-gradient(135deg, transparent 0%, rgba(0, 0, 0, 0.72) 45%, transparent 100%);
          pointer-events: none;
          z-index: -1;
        }
        .hr-contact-content {
          display: grid;
          justify-items: center;
          gap: 20px;
          padding: 18px 0;
          text-align: center;
        }
        .hr-contact-kicker {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 7px 12px;
          border: 1px solid rgba(99, 102, 241, 0.22);
          border-radius: 999px;
          background: rgba(99, 102, 241, 0.08);
          color: var(--hr-accent-2);
          font-size: 12px;
          font-weight: 700;
          letter-spacing: 0.08em;
          text-transform: uppercase;
        }
        .hr-contact-kicker span {
          width: 7px;
          height: 7px;
          border-radius: 999px;
          background: var(--hr-accent-2);
          box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.12);
        }
        .hr-contact-title {
          margin: 0;
          max-width: 660px;
          color: var(--hr-heading);
          font-size: clamp(2rem, 4vw, 3.35rem);
          line-height: 1.04;
          letter-spacing: -0.045em;
          font-weight: 800;
        }
        .hr-contact-copy {
          margin: -6px 0 0;
          max-width: 560px;
          color: var(--hr-text-muted);
          font-size: clamp(1rem, 2vw, 1.18rem);
          line-height: 1.7;
        }
        .hr-contact-points {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
          gap: 10px;
          margin-top: 2px;
        }
        .hr-contact-points span {
          padding: 8px 12px;
          border: 1px solid var(--hr-border);
          border-radius: 999px;
          background: rgba(255, 255, 255, 0.62);
          color: var(--hr-text);
          font-size: 13px;
          font-weight: 600;
        }
        html.dark .hr-contact-points span {
          background: rgba(255, 255, 255, 0.05);
        }
        .hr-contact-actions {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
          align-items: center;
          gap: 14px;
          margin-top: 8px;
        }
        .hr-contact-primary {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 10px;
          padding: 14px 22px;
          border-radius: 16px;
          background: linear-gradient(135deg, #4f46e5, #7c3aed);
          color: #fff;
          font-weight: 800;
          box-shadow: 0 18px 36px rgba(79, 70, 229, 0.28);
          transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }
        .hr-contact-primary:hover {
          transform: translateY(-2px);
          filter: saturate(1.08);
          box-shadow: 0 22px 44px rgba(79, 70, 229, 0.36);
        }
        .hr-contact-primary:active {
          transform: translateY(0) scale(0.99);
        }
        .hr-contact-primary svg {
          width: 18px;
          height: 18px;
          transition: transform 0.2s ease;
        }
        .hr-contact-primary:hover svg {
          transform: translateX(3px);
        }
        .hr-contact-secondary {
          color: var(--hr-text-muted);
          font-size: 14px;
          font-weight: 700;
          transition: color 0.2s ease;
        }
        .hr-contact-secondary:hover {
          color: var(--hr-accent-2);
        }
        @media (max-width: 640px) {
          #contact .hr-contact-card { padding: 24px 18px; }
          .hr-contact-content { padding: 12px 0; }
          .hr-contact-actions,
          .hr-contact-primary { width: 100%; }
          .hr-contact-secondary { padding: 6px 0; }
        }

        /* ── Services styles used by "Hero Redesign.html" markup ────── */
        .services {
          display: grid;
          grid-template-columns: 1fr 2fr;
          gap: 36px;
        }
        /* Services: slightly brighter surface + clearer edge (only this block) */
        #services .hr-section-card {
          border-color: var(--hr-border-grid);
          background: var(--hr-bg-services-inset);
        }
        .services-head .eyebrow {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          font-family: 'JetBrains Mono', monospace;
          font-size: 11.5px;
          letter-spacing: 0.16em;
          text-transform: uppercase;
          color: var(--hr-accent-2);
          font-weight: 600;
          margin-bottom: 14px;
        }
        .services-head .eyebrow .dot {
          width: 7px;
          height: 7px;
          background: var(--hr-accent-2);
          border-radius: 999px;
        }
        .services-head h2 {
          font-family: 'Geist', system-ui, sans-serif;
          font-size: 30px;
          line-height: 1.15;
          letter-spacing: -0.02em;
          font-weight: 700;
          margin: 0 0 16px;
          color: var(--hr-heading);
        }
        .services-head h2 .accent { color: var(--hr-accent-2); }
        .services-head p {
          color: var(--hr-text-muted);
          font-size: 14px;
          line-height: 1.6;
          margin: 0 0 24px;
          max-width: 280px;
        }
        .services .pill-cta {
          display: inline-flex;
          align-items: center;
          gap: 10px;
          padding: 9px 16px 9px 18px;
          border-radius: 999px;
          border: 1px solid var(--hr-border-strong);
          color: var(--hr-text);
          font-size: 13.5px;
          font-weight: 500;
          transition: background .2s, border-color .2s;
        }
        .services .pill-cta:hover {
          background: var(--hr-pill-hover-bg);
          border-color: var(--hr-pill-hover-border);
        }
        .services .pill-cta .arrow {
          width: 22px;
          height: 22px;
          border-radius: 999px;
          background: var(--hr-pill-arrow-bg);
          display: inline-flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
        }
        /* Nested card: services columns + vertical rules (horizontal when stacked) */
        .services-grid {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 0;
          align-items: stretch;
          border: 1px solid var(--hr-border-grid);
          /* Inset panel: slightly different from .hr-section-card */
          background: var(--hr-bg-services-grid);
          border-radius: 18px;
          padding: 24px 28px;
          box-shadow: inset 0 1px 0 var(--hr-grid-inset-line);
        }
        .services-grid .svc {
          padding: 0 22px;
        }
        .services-grid .svc:first-child {
          padding-left: 0;
        }
        .services-grid .svc:last-child {
          padding-right: 0;
        }
        .services-grid .svc:not(:first-child) {
          border-left: 1px solid var(--hr-border-grid);
        }
        .svc {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .svc-icon {
          width: 36px;
          height: 36px;
          border-radius: 10px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          background: rgba(99,102,241,0.12);
          color: var(--hr-accent-2);
        }
        .svc-icon svg { width: 18px; height: 18px; }
        .svc h3 {
          font-size: 16px;
          font-weight: 600;
          color: var(--hr-heading);
          margin: 0;
        }
        .svc p {
          font-size: 13px;
          line-height: 1.55;
          color: var(--hr-text-muted);
          margin: 0 0 8px;
        }
        .svc ul {
          list-style: none;
          margin: 0;
          padding: 0;
          display: flex;
          flex-direction: column;
          gap: 8px;
        }
        .svc li {
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 13px;
          color: var(--hr-text);
        }
        .svc li .check {
          width: 14px;
          height: 14px;
          color: var(--hr-accent);
          flex-shrink: 0;
        }
        .svc .pill-cta {
          margin-top: auto;
          width: max-content;
        }
        .services-footer {
          grid-column: 1 / -1;
          margin-top: 8px;
          text-align: center;
          color: var(--hr-text-muted);
          font-size: 13px;
        }
        .services-footer .accent {
          color: var(--hr-accent-2);
          font-weight: 600;
        }
        @media (max-width: 1080px) {
          .services { grid-template-columns: 1fr; }
          .services-grid {
            grid-template-columns: 1fr;
            padding: 0;
            gap: 14px;
            border: 0;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
          }
          .services-grid .svc {
            padding: 18px 16px;
            border: 1px solid var(--hr-border-grid);
            border-radius: 14px;
            background: var(--hr-bg-services-grid);
            gap: 10px;
          }
          .services-grid .svc:first-child,
          .services-grid .svc:last-child {
            padding-left: 16px;
            padding-right: 16px;
          }
          .services-grid .svc:not(:first-child) {
            border-left: none;
            margin-top: 0;
            padding-top: 18px;
          }
          .services-grid .svc ul {
            border-top: 1px solid var(--hr-border-grid);
            margin-top: 6px;
            padding-top: 10px;
          }
        }

        /* ── WebGL layer: transparent; page uses same body/html bg as the rest of the site ── */
        .hr-shader-canvas {
          position: absolute; inset: 0;
          width: 100%; height: 100%;
          display: block;
          z-index: 0;
          pointer-events: none;
          background: transparent;
        }

        /* ── Hero (no own fill: inherits page background through parent) ───────────────── */
        .hr-hero {
          position: relative;
          z-index: 1;
          border-radius: 0;
          overflow: hidden;
          border: none;
          background: transparent;
          margin-top: 0;
          padding-top: 80px;
        }
        /* Soft mask so the fluid band eases out toward the footer (opaque = show WebGL tint only) */
        .hr-hero-bg-clip {
          --hr-shader-mask-fade: min(clamp(200px, 44vh, 520px), 90%);
          position: absolute; inset: 0; border-radius: 0;
          overflow: hidden; pointer-events: none; z-index: 0;
          -webkit-mask-image: linear-gradient(
            to bottom,
            #000 0,
            #000 calc(100% - var(--hr-shader-mask-fade)),
            rgba(0, 0, 0, 0.88) calc(100% - (var(--hr-shader-mask-fade) * 0.78)),
            rgba(0, 0, 0, 0.45) calc(100% - (var(--hr-shader-mask-fade) * 0.42)),
            transparent 100%
          );
          mask-image: linear-gradient(
            to bottom,
            #000 0,
            #000 calc(100% - var(--hr-shader-mask-fade)),
            rgba(0, 0, 0, 0.88) calc(100% - (var(--hr-shader-mask-fade) * 0.78)),
            rgba(0, 0, 0, 0.45) calc(100% - (var(--hr-shader-mask-fade) * 0.42)),
            transparent 100%
          );
          mask-mode: alpha;
          -webkit-mask-size: 100% 100%;
          mask-size: 100% 100%;
          -webkit-mask-repeat: no-repeat;
          mask-repeat: no-repeat;
        }
        .hr-hero-inner {
          position: relative; z-index: 3;
          display: grid; grid-template-columns: 1.2fr 1fr;
          gap: 40px; align-items: stretch;
          min-height: 580px;
          /* section now starts below navbar, so only visual breathing room needed */
          padding: 48px 56px 56px 64px;
          overflow: visible;
          max-width: 1280px;
          margin: 0 auto;
        }

        /* ── Status row ─────────────────────────────────────────────── */
        .hr-status-row {
          display: flex; align-items: center; gap: 14px;
          font-family: 'JetBrains Mono', monospace;
          font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase;
          color: var(--hr-text-muted);
          margin-top: 16px;
          margin-bottom: 14px;
        }
        .hr-available { display: inline-flex; align-items: center; gap: 8px; color: var(--hr-available); font-weight: 600; }
        .hr-status-dot {
          width: 8px; height: 8px; border-radius: 999px;
          background: var(--hr-available);
          box-shadow: 0 0 0 4px rgba(34,197,94,0.18);
          animation: hr-pulse 2.4s ease-in-out infinite;
        }
        @keyframes hr-pulse {
          0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.45); }
          50%       { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
        }
        .hr-status-sep { color: var(--hr-text-faint); }

        /* ── Headline ────────────────────────────────────────────────── */
        .hr-h1 {
          font-family: 'Geist', system-ui, sans-serif;
          font-weight: 700; font-size: clamp(40px, 4.4vw, 60px);
          line-height: 1.04; letter-spacing: -0.025em;
          margin: 0 0 22px; color: var(--hr-heading); max-width: 640px;
        }
        .hr-h1 .hr-accent-word { color: var(--hr-accent-2); }
        /* Subrayado neón (borde elíptico + máscara fade, sin cortes duros) */
        .hr-h1 .subrayado-exacto {
          position: relative;
          display: inline-block;
          white-space: nowrap;
          isolation: isolate;
        }
        .hr-h1 .subrayado-exacto::after {
          content: "";
          position: absolute;
          left: 0%;
          top: 100%;
          margin-top: 2px;
          width: 96%;
          height: 0;
          border-top: 9px solid #4361ee;
          border-bottom: 20px solid transparent;
          border-radius: 50%;
          transform: rotate(-1.5deg);
          filter: blur(1.5px) drop-shadow(0 5px 7px rgba(67, 97, 238, 0.8));
          -webkit-mask-image: linear-gradient(
            to right,
            rgba(0, 0, 0, 0) 0%,
            rgba(0, 0, 0, 1) 8%,
            rgba(0, 0, 0, 0.9) 45%,
            rgba(0, 0, 0, 0.2) 80%,
            rgba(0, 0, 0, 0) 100%
          );
          mask-image: linear-gradient(
            to right,
            rgba(0, 0, 0, 0) 0%,
            rgba(0, 0, 0, 1) 8%,
            rgba(0, 0, 0, 0.9) 45%,
            rgba(0, 0, 0, 0.2) 80%,
            rgba(0, 0, 0, 0) 100%
          );
          z-index: -1;
        }

        /* ── Sub-headline ─────────────────────────────────────────────── */
        .hr-sub {
          color: var(--hr-text-muted); font-size: 16px; line-height: 1.6;
          max-width: 520px; margin: 0 0 32px;
        }

        /* ── CTA buttons ─────────────────────────────────────────────── */
        .hr-cta-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
        .hr-btn {
          display: inline-flex; align-items: center; gap: 10px;
          padding: 13px 22px; border-radius: 12px;
          font-size: 14.5px; font-weight: 500; cursor: pointer;
          transition: transform .2s ease, background .2s, border-color .2s, color .2s, box-shadow .2s ease;
          border: 1px solid transparent; line-height: 1; text-decoration: none;
        }
        .hr-btn:active { transform: translateY(1px); }
        .hr-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
        .hr-btn-primary { background: var(--hr-accent); color: #fff; box-shadow: 0 8px 24px -8px rgba(99,102,241,0.65); }
        .hr-btn-primary svg { transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .hr-btn-primary:hover {
          background: var(--hr-accent-2);
          color: #fff;
          transform: translateY(-2px);
          box-shadow: 0 14px 36px -12px rgba(99, 102, 241, 0.72);
        }
        .hr-btn-primary:hover svg { transform: translate(3px, -2px) rotate(-8deg); }
        .hr-btn-primary:active { transform: translateY(0); box-shadow: 0 8px 22px -10px rgba(99, 102, 241, 0.55); }
        .hr-btn-ghost { background: var(--hr-btn-ghost); color: var(--hr-text); border-color: var(--hr-border-strong); }
        .hr-btn-ghost:hover {
          background: var(--hr-btn-ghost-hover);
          color: var(--hr-text);
          border-color: rgba(99, 102, 241, 0.28);
          transform: translateY(-1px);
        }
        /* Reuse demo-eye-blink keyframes from spotlight.css (loaded on public layout) */
        .hr-btn-ghost:hover .demo-eye-blink {
          animation: demo-eye-blink 650ms ease-in-out 1 forwards;
          transform-origin: center;
        }
        .hr-btn-ghost:active { transform: translateY(0); }

        /* ── Tech rail ────────────────────────────────────────────────── */
        .hr-tech-rail { display: flex; align-items: center; gap: 32px; flex-wrap: wrap; color: var(--hr-text-muted); font-size: 15px; font-weight: 600; }
        .hr-tech { display: inline-flex; align-items: center; gap: 10px; }
        .hr-tech svg { width: 24px; height: 24px; }
        .hr-tech-dot {
          width: 24px; height: 24px; border-radius: 6px;
          display: inline-flex; align-items: center; justify-content: center;
          font-size: 12px; font-family: 'JetBrains Mono', monospace; font-weight: 700;
        }
        .hr-dot-laravel { background: rgba(248,113,113,0.14); color: #f87171; }
        .hr-dot-react   { background: rgba(97,218,251,0.12);  color: #61dafb; }
        .hr-dot-js      { background: rgba(234,179,8,0.14);   color: #eab308; }
        .hr-dot-php     { background: rgba(139,92,246,0.14);  color: #a78bfa; }
        .hr-dot-mysql   { background: rgba(59,130,246,0.14);  color: #60a5fa; }

        /* ── Right column ─────────────────────────────────────────────── */
        .hr-hero-right {
          position: relative;
          display: flex;
          align-items: stretch;
          justify-content: center;
          overflow: visible;
          /* Extra canvas so glow / radial don’t run into the stage’s rounded rect or section clip */
          padding: clamp(12px, 2.2vw, 28px) clamp(16px, 3vw, 44px) clamp(10px, 1.8vw, 24px) clamp(8px, 1.5vw, 20px);
          box-sizing: border-box;
        }
        .hr-photo-stage {
          position: relative;
          width: 100%;
          border-radius: 18px;
          isolation: isolate;
          box-sizing: border-box;
          /* Inset content so the purple wash + blur fade fully before the outer border */
          --hr-stage-pady: clamp(18px, 3vw, 36px);
          --hr-stage-padx: clamp(22px, 4vw, 48px);
          padding: var(--hr-stage-pady) var(--hr-stage-padx);
        }
        .hr-portrait-inline {
          display: none;
          width: min(100%, 520px);
          height: auto;
          object-fit: contain;
          object-position: bottom center;
          filter: drop-shadow(var(--hr-portrait-shadow));
          position: relative;
          z-index: 4;
        }
        /* Ambient light: padded stage + softer mask stops so nothing “rings” at the rounded edge */
        .hr-photo-stage::before {
          content: "";
          position: absolute;
          inset: 0;
          border-radius: inherit;
          background: radial-gradient(120% 100% at 60% 40%, rgba(99,102,241,0.2) 0%, rgba(99,102,241,0.06) 32%, transparent 56%);
          -webkit-mask-image: radial-gradient(ellipse 72% 70% at 60% 40%, #000 14%, transparent 66%);
          mask-image: radial-gradient(ellipse 72% 70% at 60% 40%, #000 14%, transparent 66%);
          pointer-events: none;
          z-index: 0;
        }

        /* ── Dots panel ──────────────────────────────────────────────── */
        .hr-dots-panel {
          position: absolute; left: -30%; right: -30%; top: 0; bottom: 0;
          z-index: 1;
          pointer-events: none; overflow: hidden;
          /* Fade alpha on all edges so the grid never hard-cuts at the hero bounds */
          -webkit-mask-image:
            linear-gradient(to bottom, transparent 0%, #000 var(--hr-dots-fade-h), #000 calc(100% - var(--hr-dots-fade-h)), transparent 100%),
            linear-gradient(to right, transparent 0%, #000 var(--hr-dots-fade-w), #000 calc(100% - var(--hr-dots-fade-w)), transparent 100%);
          mask-image:
            linear-gradient(to bottom, transparent 0%, #000 var(--hr-dots-fade-h), #000 calc(100% - var(--hr-dots-fade-h)), transparent 100%),
            linear-gradient(to right, transparent 0%, #000 var(--hr-dots-fade-w), #000 calc(100% - var(--hr-dots-fade-w)), transparent 100%);
          -webkit-mask-composite: source-in;
          mask-composite: intersect;
          mask-mode: alpha;
          -webkit-mask-size: 100% 100%;
          mask-size: 100% 100%;
          -webkit-mask-repeat: no-repeat;
          mask-repeat: no-repeat;
        }
        .hr-dots-panel canvas { display: block; width: 100%; height: 100%; background-color: transparent; }
        .hr-photo-glow {
          position: absolute; left: 50%; top: 58%;
          transform: translate(-50%, -50%);
          width: 72%; aspect-ratio: 1/1;
          background: radial-gradient(circle at center, rgba(99,102,241,0.42) 0%, rgba(99,102,241,0.12) 32%, rgba(99,102,241,0) 58%);
          -webkit-mask-image: radial-gradient(circle at center, #000 0%, transparent 68%);
          mask-image: radial-gradient(circle at center, #000 0%, transparent 68%);
          filter: blur(10px); pointer-events: none; z-index: 2;
        }

        /* ── Portrait ────────────────────────────────────────────────── */
        .hr-portrait-layer {
          position: absolute;
          /* Align with the right edge of the 1280px content column */
          right: max(0px, calc((100% - 1280px) / 2));
          top: 0; bottom: 0;
          /*
            Size ties to SECTION HEIGHT, not viewport width:
            capped width avoids covering the headline; image uses max-height:100%.
          */
          width: auto;
          max-width: min(520px, calc((min(100%, 1280px) / 2) - 32px));
          min-width: 0;
          z-index: 8;
          pointer-events: none; display: flex; align-items: flex-end; justify-content: center;
          overflow: visible;
        }
        .hr-portrait-layer img {
          max-height: 100%;
          width: auto;
          max-width: 100%;
          height: auto;
          vertical-align: bottom;
          object-fit: contain; object-position: bottom center;
          filter: drop-shadow(var(--hr-portrait-shadow));
        }
        /* Desktop/layer portrait: hard switch by theme (prevents both images showing). */
        .hr-portrait-layer .hr-portrait-dark { display: none; }
        html.dark .hr-portrait-layer .hr-portrait-light { display: none; }
        html.dark .hr-portrait-layer .hr-portrait-dark { display: block; }

        /* ── Idea card ───────────────────────────────────────────────── */
        .hr-idea-card {
          position: absolute;
          right: max(40px, calc((100% - 1280px) / 2 + 40px));
          bottom: 108px; /* above stats strip overlap */
          width: 210px;
          padding: 16px 18px; border-radius: 14px;
          border: 1px solid var(--hr-border-strong);
          background: var(--hr-idea-card-bg);
          backdrop-filter: blur(12px) saturate(140%);
          -webkit-backdrop-filter: blur(12px) saturate(140%);
          z-index: 20; display: flex; flex-direction: column; gap: 8px;
        }
        .hr-idea-head { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--hr-accent-2); }
        .hr-idea-card p { margin: 0; font-size: 12.5px; line-height: 1.5; color: var(--hr-text-muted); }
        .hr-idea-arrow {
          align-self: flex-end; width: 28px; height: 28px;
          border-radius: 999px; border: 1px solid var(--hr-border-strong);
          display: inline-flex; align-items: center; justify-content: center;
          color: var(--hr-text); text-decoration: none;
          transition: background .2s, border-color .2s;
        }
        .hr-idea-arrow:hover { background: var(--hr-idea-arrow-hover); }

        /* ── Stats strip ─────────────────────────────────────────────── */
        .hr-stats {
          margin-top: 0;
          border: 1px solid var(--hr-border); background: var(--hr-bg-card);
          border-radius: 22px; padding: 20px;
          display: grid;
          /* Always one row of four; minmax(0,1fr) lets cells shrink without forcing a grid wrap */
          grid-template-columns: repeat(4, minmax(0, 1fr));
          gap: 12px; position: relative; z-index: 1;
          transition: background 0.2s, border-color 0.2s;
        }
        .hr-stat { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .hr-stat-icon {
          width: 38px; height: 38px; border-radius: 11px;
          display: inline-flex; align-items: center; justify-content: center;
          background: rgba(99,102,241,0.12); color: var(--hr-accent-2); flex-shrink: 0;
        }
        .hr-stat-icon svg { width: 18px; height: 18px; }
        .hr-stat-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .hr-stat-title { font-size: 13.5px; font-weight: 600; color: var(--hr-text); white-space: nowrap; }
        .hr-stat-sub   { font-size: 11.5px; color: var(--hr-text-muted); white-space: nowrap; }
        .hr-stat + .hr-stat { border-left: 1px solid var(--hr-border); padding-left: 12px; }

        /* ── Responsive ──────────────────────────────────────────────── */
        @media (max-width: 1080px) {
          /* Single source of truth: dots stage height tracks portrait column (no huge empty band) */
          .hr-hero {
            /*
              Match content width (~ padded viewport) so aspect height tracks the real image width.
              92vw was often wider than the text column on phones → box taller than needed + felt like a growing gap.
            */
            --hr-portrait-w: min(560px, calc(100vw - max(56px, 32px + env(safe-area-inset-left, 0px) + env(safe-area-inset-right, 0px))));
            --hr-portrait-h: clamp(340px, min(72dvh, 70vh), 580px);
            /*
              Image asset is 520×720. A viewport-tall --hr-portrait-h alone leaves a fixed-height box
              taller than the contained image → empty band above the photo on phones.
              Box height = min(cap, aspect-fit height).
            */
            --hr-portrait-box-h: min(var(--hr-portrait-h), calc(var(--hr-portrait-w) * 720 / 520));
          }
          /* Explicit horizontal padding (+ safe-area) so phone layout never looks “collapsed” on one side */
          .hr-hero-inner {
            grid-template-columns: 1fr;
            /* Desktop left `gap: 40px`; stacked layout must not inherit 40px *row* gap (huge band under copy). */
            gap: 0;
            row-gap: 0;
            column-gap: 0;
            padding-top: 40px;
            padding-bottom: 32px;
            padding-left: max(28px, 16px + env(safe-area-inset-left, 0px));
            padding-right: max(28px, 16px + env(safe-area-inset-right, 0px));
            /* Drop desktop min-height; let section height follow copy + portrait slot only */
            min-height: unset;
            /* Prevent leftover block-size from stretching rows: avoids a growing empty band
               under .hr-hero-copy when the viewport is very narrow (portrait slot shrinks). */
            align-content: start;
            align-items: start;
            max-width: 100%;
          }
          /*
            Mobile: collapse any artificial vertical box height so the portrait
            hugs the copy more closely. The generic min-height based on
            --hr-portrait-box-h was leaving extra empty space above the bitmap.
          */
          .hr-photo-stage {
            /* Let the image define the height; keep just enough padding for the glow. */
            min-height: auto;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-top: calc(var(--hr-stage-pady) * 0.5);
            /* Remove extra blank band under the bitmap on stacked/mobile layout. */
            padding-bottom: 0;
          }
          .hr-hero-right {
            /* Keep hero-right snug to the stats strip so the portrait appears to emerge from behind it,
               without reintroducing a tall fixed box above the bitmap. */
            min-height: auto;
            padding-top: clamp(4px, 1.5vw, 10px);
            padding-bottom: 0;
          }
          /* Stacked hero: width + aspect-capped height so img fills the box (no letterboxing gap) */
          .hr-portrait-layer {
            display: none;
          }
          .hr-portrait-inline {
            display: block;
            width: min(100%, var(--hr-portrait-w));
            /* Let intrinsic aspect + stage padding drive total height; avoid ghost box above image. */
            height: auto;
          }
          /* Mobile/inline portrait: force a single visible image by theme. */
          .hr-portrait-inline.hr-portrait-dark { display: none; }
          html.dark .hr-portrait-inline.hr-portrait-light { display: none; }
          html.dark .hr-portrait-inline.hr-portrait-dark { display: block; }
          .hr-idea-card { right: 32px; bottom: 100px; }
        }
        /* Narrow viewports: keep 4 stats in one row, stack icon → title → text inside each cell */
        @media (max-width: 900px) {
          .hr-stat {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
          }
          .hr-stat-text { align-items: center; }
          .hr-stat-title,
          .hr-stat-sub {
            white-space: normal;
            line-height: 1.3;
          }
        }
        @media (max-width: 720px) {
          .hr-hero {
            padding-top: 72px; /* match fixed navbar height on mobile */
            --hr-portrait-w: min(540px, calc(100vw - max(56px, 32px + env(safe-area-inset-left, 0px) + env(safe-area-inset-right, 0px))));
            --hr-portrait-h: clamp(300px, min(68dvh, 65vh), 540px);
            --hr-portrait-box-h: min(var(--hr-portrait-h), calc(var(--hr-portrait-w) * 720 / 520));
          }
          .hr-hero-inner {
            padding-bottom: 24px;
          }
          .hr-stats {
            gap: 8px;
            padding: 14px 10px;
          }
          .hr-stat + .hr-stat {
            border-left: 1px solid var(--hr-border);
            padding-left: 8px;
          }
          .hr-stat-title { font-size: 12px; }
          .hr-stat-sub { font-size: 10px; }
          .hr-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
          }
          .hr-stat-icon svg { width: 16px; height: 16px; }
          .hr-h1 { font-size: 38px; }
          .hr-idea-card { right: max(16px, env(safe-area-inset-right, 0px)); bottom: 72px; }
          /* Pull stats strip up a bit more on phones so it visually meets the portrait. */
          .hr-stats-wrapper { padding: 0 12px; margin-top: -40px; }
        }

        /* ── Card sections: headings follow theme heading token ─────── */
        .hr-section-card h2,
        .hr-section-card h3 { color: var(--hr-heading) !important; }
        .hr-section-card .mb-12 > p {
          color: var(--hr-text-muted) !important;
        }
        #about h2, #about h3   { color: var(--hr-heading) !important; }
        #about p               { color: var(--hr-text-muted) !important; }
        #projects h2           { color: var(--hr-heading) !important; }

        /* ── Landing: scroll reveal (entrada global → layouts/public <main>) ── */
        [data-reveal] {
            opacity: 0;
            transform: translate3d(0, 18px, 0);
            transition:
                opacity 1.05s cubic-bezier(0.22, 0.61, 0.36, 1),
                transform 1.05s cubic-bezier(0.22, 0.61, 0.36, 1);
            transition-delay: var(--hr-reveal-delay, 0ms);
        }
        [data-reveal-direction="left"] {
            transform: translate3d(-16px, 16px, 0);
        }
        [data-reveal-direction="right"] {
            transform: translate3d(16px, 16px, 0);
        }
        [data-reveal].is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        @media (prefers-reduced-motion: reduce) {
            [data-reveal],
            [data-reveal-direction="left"],
            [data-reveal-direction="right"] {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>

    {{-- ── Hero: full-width ─────────────────────────────────────────── --}}
    <section class="hr-hero" id="home" aria-label="Presentación">

            {{-- WebGL tint only (transparent); page background matches body ─────────── --}}
            <div class="hr-hero-bg-clip">
                <canvas id="hr-shader-canvas" class="hr-shader-canvas" aria-hidden="true"></canvas>
            </div>

            <div class="hr-hero-inner">

                {{-- ── LEFT: copy ──────────────────────────────────── --}}
                <div class="hr-hero-copy">

                    {{-- Status row --}}
                    <div class="hr-status-row">
                        <span class="hr-available">
                            <span class="hr-status-dot"></span>Disponible
                        </span>
                        <span class="hr-status-sep">·</span>
                        <span>UTC+1</span>
                    </div>

                    {{-- Headline --}}
                    <h1 class="hr-h1">
                        Desarrollo <span class="hr-accent-word">webs y apps</span><br>
                        que hacen <span class="subrayado-exacto">crecer negocios</span>
                    </h1>

                    {{-- Sub-headline --}}
                    <p class="hr-sub">
                        Ingeniero full‑stack en Sevilla. Diseño, desarrollo y lanzo
                        productos digitales modernos que generan resultados reales.
                    </p>

                    {{-- CTAs --}}
                    <div class="hr-cta-row">
                        <a class="hr-btn hr-btn-primary" href="{{ route('public.contact') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            Solicitar proyecto
                        </a>
                        <a class="hr-btn hr-btn-ghost" href="#projects">
                            <svg class="demo-eye-blink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Ver trabajos
                        </a>
                    </div>

                    {{-- Tech rail --}}
                    <div class="hr-tech-rail">
                        <span class="hr-tech">
                            <x-icons.tech-react />
                            React
                        </span>
                        <span class="hr-tech">
                            <x-icons.tech-node-js />
                            Node.js
                        </span>
                        <span class="hr-tech">
                            <x-icons.tech-kotlin />
                            Kotlin
                        </span>
                        <span class="hr-tech">
                            <x-icons.tech-laravel />
                            Laravel
                        </span>
                        <span class="hr-tech">
                            <x-icons.tech-aws />
                            AWS
                        </span>
                    </div>

                </div>{{-- /hr-hero-copy --}}

                {{-- ── RIGHT: dots panel + portrait --}}
                <div class="hr-hero-right">
                    <div class="hr-photo-stage">
                        {{-- Dots background (shared x-ai-dots-background component) --}}
                        <x-ai-dots-background variant="hero" :waves-overlay="false" />
                        <div class="hr-photo-glow" aria-hidden="true"></div>
                        <img class="hr-portrait-inline hr-portrait-light"
                             src="{{ asset('img/me-noBg-light.webp') }}"
                             alt=""
                             width="520" height="720"
                             decoding="async" fetchpriority="high">
                        <img class="hr-portrait-inline hr-portrait-dark"
                             src="{{ asset('img/me-noBg-dark.webp') }}"
                             alt=""
                             width="520" height="720"
                             decoding="async" fetchpriority="high">
                    </div>
                </div>{{-- /hr-hero-right --}}

            </div>{{-- /hr-hero-inner --}}

            {{-- Portrait anchored to the bottom of the hero card --}}
            <div class="hr-portrait-layer" aria-hidden="true">
                <img class="hr-portrait-light"
                     src="{{ asset('img/me-noBg-light.webp') }}"
                     alt="Carlos — Fullstack Developer"
                     width="520" height="720"
                     loading="eager" decoding="async">
                <img class="hr-portrait-dark"
                     src="{{ asset('img/me-noBg-dark.webp') }}"
                     alt="Carlos — Fullstack Developer"
                     width="520" height="720"
                     loading="eager" decoding="async">
            </div>

            {{-- Floating idea card (hidden temporarily) --}}
            {{--
            <aside class="hr-idea-card">
                <div class="hr-idea-head">
                    <span style="display:inline-block;width:6px;height:6px;border-radius:999px;background:var(--hr-accent-2);"></span>
                    ¿Tienes una idea?
                </div>
                <p>La convierto en un producto digital sólido y escalable.</p>
                <a class="hr-idea-arrow" href="#contact" aria-label="Cuéntame tu idea">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </aside>
            --}}

    </section>{{-- /hr-hero --}}

    {{-- ── Stats strip: centered wrapper ──────────────────────────── --}}
    <div class="hr-stats-wrapper">
        <section class="hr-stats" aria-label="Indicadores">

            <div class="hr-stat" data-reveal data-reveal-delay="0">
                <div class="hr-stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .587l3.668 7.431L24 9.25l-6 5.847L19.336 24 12 19.897 4.664 24 6 15.097 0 9.25l8.332-1.232z"/></svg>
                </div>
                <div class="hr-stat-text">
                    <span class="hr-stat-title">A medida</span>
                    <span class="hr-stat-sub">Soluciones web escalables</span>
                </div>
            </div>

            <div class="hr-stat" data-reveal data-reveal-delay="70">
                <div class="hr-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="hr-stat-text">
                    <span class="hr-stat-title">Clientes satisfechos</span>
                    <span class="hr-stat-sub">Trabajo cercano y transparente</span>
                </div>
            </div>

            <div class="hr-stat" data-reveal data-reveal-delay="140">
                <div class="hr-stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>
                </div>
                <div class="hr-stat-text">
                    <span class="hr-stat-title">Respuesta en 24h</span>
                    <span class="hr-stat-sub">Atención rápida y directa</span>
                </div>
            </div>

            <div class="hr-stat" data-reveal data-reveal-delay="210">
                <div class="hr-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="hr-stat-text">
                    <span class="hr-stat-title">Freelance disponible</span>
                    <span class="hr-stat-sub">Nuevos proyectos</span>
                </div>
            </div>

        </section>{{-- /hr-stats --}}
    </div>{{-- /hr-stats-wrapper --}}


    <!--
    |------------------------------------------------------------------|
    |  ##########             SERVICIOS SECTION            ##########  |                
    |------------------------------------------------------------------|
    -->
    <section id="services" class="relative z-10 transition-colors duration-300 mx-3 md:mx-6 lg:mx-10 mt-8 mb-8">
      <div class="max-w-screen-xl px-4 mx-auto">
        <div class="hr-section-card services">
          <div class="services-head" data-reveal>
            <span class="eyebrow"><span class="dot"></span>Mis servicios</span>
            <h2>Soluciones digitales <span class="accent">a medida</span> para tu negocio</h2>
            <p>Desde la idea hasta el lanzamiento. Me encargo de todo el proceso para que tú te centres en lo importante.</p>
            <a class="pill-cta" href="{{ route('public.services') }}">
              Explorar servicios
              <span class="arrow">→</span>
            </a>
          </div>

          <div class="services-grid">
            <div class="svc" data-reveal data-reveal-delay="0">
              <span class="svc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"></rect><path d="M8 21h8M12 17v4"></path></svg>
              </span>
              <h3>Desarrollo Web</h3>
              <p>Páginas rápidas, modernas y optimizadas para convertir.</p>
              <ul>
                <li><x-icons.check />Landing pages</li>
                <li><x-icons.check />Webs corporativas</li>
                <li><x-icons.check />E‑commerce</li>
              </ul>
              <a href="{{ route('public.services.web') }}" class="pill-cta">Ver servicio</a>
            </div>

            <div class="svc" data-reveal data-reveal-delay="85">
              <span class="svc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12" y2="18"></line></svg>
              </span>
              <h3>Apps Móviles</h3>
              <p>Apps nativas Android y multiplataforma con excelente experiencia.</p>
              <ul>
                <li><x-icons.check />Android (Kotlin)</li>
                <li><x-icons.check />Apps multiplataforma</li>
                <li><x-icons.check />Integraciones y APIs</li>
              </ul>
              <a href="{{ route('public.services.app') }}" class="pill-cta">Ver servicio</a>
            </div>

            <div class="svc" data-reveal data-reveal-delay="170">
              <span class="svc-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
              </span>
              <h3>Soluciones a medida</h3>
              <p>Si no encaja en un servicio estándar, definimos juntos una solución personalizada.</p>
              <ul>
                <li><x-icons.check />APIs y backends</li>
                <li><x-icons.check />Paneles administrativos</li>
                <li><x-icons.check />Integraciones externas</li>
              </ul>
              <a href="{{ route('public.contact', ['interest' => 'web']) }}" class="pill-cta">Hablar de mi caso</a>
            </div>
          </div>

          <div class="services-footer" data-reveal data-reveal-delay="60">
            Tecnología moderna. Código limpio. <span class="accent">Resultados reales.</span>
          </div>
        </div>
      </div>
    </section>


    <!--
    |------------------------------------------------------------------|
    |  ##########             SOBRE MI SECTION             ##########  |
    |------------------------------------------------------------------|
    -->
    <section id="about" class="relative py-24 bg-transparent transition-colors duration-300 overflow-x-clip">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 bg-indigo-400/10 dark:bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-indigo-500/10 dark:bg-indigo-500/18 rounded-full blur-3xl pointer-events-none hidden md:block"></div>

        <div class="max-w-screen-xl px-4 mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">

                <div class="lg:col-span-5 relative group lg:pr-10" data-reveal data-reveal-direction="left">
                    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-blue-500 rounded-2xl transform rotate-3 scale-105 opacity-20 dark:opacity-40 transition-transform duration-500 group-hover:rotate-6"></div>
                    <div class="relative overflow-hidden rounded-2xl shadow-xl transition-transform duration-500 group-hover:-translate-y-2 border border-white/50 dark:border-gray-700 bg-white dark:bg-gray-800 p-2">
                        <div class="relative overflow-hidden rounded-xl">
                            <img src="{{ asset('img/logo.svg') }}" alt="" aria-hidden="true" class="absolute inset-x-0 top-0 w-full h-auto object-contain p-8 translate-x-1 translate-y-4 scale-[0.98] opacity-25 blur-[3px] brightness-0 pointer-events-none select-none">
                            <img src="{{ asset('img/logo.svg') }}" alt="Logo Carlos Codex" class="relative w-full h-auto object-contain p-8 transform transition-transform duration-700 group-hover:scale-105 drop-shadow-[2px_14px_26px_rgba(15,23,42,0.22)] dark:drop-shadow-[2px_14px_30px_rgba(154,209,210,0.18)]">
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -right-2 lg:right-4 bg-white dark:bg-gray-900 p-4 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 flex items-center gap-4 animate-floating z-20">
                        <div class="bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-full text-indigo-600 dark:text-indigo-400">
                            <x-icons.cpu class="w-6 h-6" />
                        </div>
                        <div class="whitespace-nowrap">
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">+7 años de experiencia</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Desarrollando software</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7" data-reveal data-reveal-delay="150" data-reveal-direction="right">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                        </span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold tracking-widest uppercase text-xs">Conoce mi perfil</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 leading-[1.15]">
                        Diseñando y programando apps y webs con la mejor <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-400">arquitectura de software.</span>
                    </h2>
                    <div class="space-y-4 text-base md:text-lg text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
                        <p>Empecé como profesional en el sector electrónico aeroespacial, un entorno <strong>científico y metódico</strong>.</p>
                        <p>Con el tiempo decidí licenciarme como programador web y de aplicaciones y actualmente llevo <strong>más de 7 años</strong> combinando una metodología científica y mi visión creativa para construir proyectos modernos y óptimos para particulares y empresas.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                        <div class="flex items-center gap-3 bg-white dark:bg-gray-800/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-indigo-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg></div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Sistemas ERP, CRM y CMS</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white dark:bg-gray-800/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-indigo-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Desarrollo móvil</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('public.about') }}" class="group inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-white bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 rounded-lg transition-all shadow-md hover:shadow-lg">
                            Conoce más Sobre Mí
                        <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="{{ route('public.stack') }}" class="group inline-flex items-center justify-center px-6 py-3.5 text-base font-semibold text-gray-900 dark:text-white bg-white/80 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-300 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1 backdrop-blur-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            Ver mi stack tecnológico
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>




    <!--
    |------------------------------------------------------------------|
    |  ##########             PROJECTS SECTION             ##########  |
    |------------------------------------------------------------------|
    -->
    <section id="projects" class="relative py-24 bg-transparent transition-colors duration-300">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 bg-indigo-500/10 dark:bg-indigo-500/18 rounded-full blur-3xl pointer-events-none hidden lg:block"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-indigo-500/10 dark:bg-indigo-500/18 rounded-full blur-3xl pointer-events-none hidden md:block"></div>
        <div class="max-w-screen-xl px-4 mx-auto relative z-10">
            <div class="mb-16" data-reveal>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Proyectos</h2>
                <div class="w-20 h-1.5 bg-indigo-600 mt-4 rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-reveal data-reveal-delay="100">
                @forelse($projects->take(4) as $index => $project)
                    <div @class(['hidden md:block lg:hidden' => $index === 3])>
                        <x-project-card :project="$project" />
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500 py-12">No hay proyectos destacados.</p>
                @endforelse
            </div>
            <div class="mt-16 text-center" data-reveal data-reveal-delay="80">
                <a href="/proyectos" class="group inline-flex items-center gap-3 px-8 py-2 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-sm tracking-wide transition-all duration-200 hover:scale-105 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 hover:shadow-indigo-500/30">
                    Ver más proyectos
                    <span class="text-indigo-600 dark:text-indigo-400 font-mono text-lg transition-transform duration-300 group-hover:text-white group-hover:translate-x-1">&lt;&gt;</span>
                </a>
            </div>
        </div>
    </section>


    <!--
    |------------------------------------------------------------------|
    |  ##########        CONTACT (acceso al asistente)      ##########  |
    |------------------------------------------------------------------|
    -->
    <section id="contact" class="relative z-10 py-24 transition-colors duration-300 mx-3 md:mx-6 lg:mx-10 mt-8 mb-10">
        <div class="max-w-screen-lg mx-auto px-4 relative z-10">
            <div class="hr-section-card hr-contact-card" data-reveal>
                <div class="hr-contact-content">
                    <div class="hr-contact-kicker">
                        <span aria-hidden="true"></span>
                        Nuevo proyecto
                    </div>
                    <h2 class="hr-contact-title">
                        ¿Tienes una idea en mente?
                    </h2>
                    <p class="hr-contact-copy">
                        Cuéntame qué quieres construir y te responderé con una primera orientación clara.
                    </p>
                    <div class="hr-contact-points" aria-label="Ventajas del contacto">
                        <span>Sin compromiso</span>
                        <span>Respuesta en menos de 24 h</span>
                        <span>Por el canal que prefieras</span>
                    </div>
                    <div class="hr-contact-actions">
                        <a href="{{ route('public.contact') }}" class="hr-contact-primary">
                            Empezar conversación
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        @if(filled(config('contact.email')))
                            <a href="mailto:{{ config('contact.email') }}" class="hr-contact-secondary">
                                Escribir por correo
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@push('scripts')
{{-- Scroll-triggered reveal (data-reveal); re-binds after Alpine x-for paint --}}
<script>
(function () {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let revealIo = null;

    function bindScrollReveals() {
        if (reduceMotion.matches) {
            document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
            return;
        }
        if (!revealIo) {
            revealIo = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        revealIo.unobserve(entry.target);
                    });
                },
                { root: null, rootMargin: '0px 0px -10% 0px', threshold: 0.06 }
            );
        }
        document.querySelectorAll('[data-reveal]:not([data-reveal-bound])').forEach((el) => {
            el.setAttribute('data-reveal-bound', '');
            const ms = el.dataset.revealDelay;
            if (ms !== undefined && ms !== '') {
                el.style.setProperty('--hr-reveal-delay', `${ms}ms`);
            }
            revealIo.observe(el);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindScrollReveals();
        requestAnimationFrame(() => {
            bindScrollReveals();
            requestAnimationFrame(bindScrollReveals);
        });
    });
    window.addEventListener('load', bindScrollReveals);
})();
</script>
{{-- ── WebGL diagonal-flow shader (hero card) + AI dots background ── --}}
<script>
/* ────────────────────────────────────────────────────────────────────
   1. WebGL shader — dark diagonal-flow contained in .hr-hero card
   ──────────────────────────────────────────────────────────────────── */
(function initHeroShader() {
  const canvas = document.getElementById('hr-shader-canvas');
  if (!canvas) return;
  /* Sin antialias: es un único rectángulo a pantalla completa, el multisampling no cambia nada y cuesta memoria */
  const gl = canvas.getContext('webgl', { premultipliedAlpha: false, antialias: false, alpha: true });
  if (!gl) return;
  gl.disable(gl.DEPTH_TEST);
  gl.enable(gl.BLEND);
  gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);

  function resize() {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const rect = canvas.getBoundingClientRect();
    const w = Math.max(1, Math.floor(rect.width * dpr));
    const h = Math.max(1, Math.floor(rect.height * dpr));
    if (canvas.width !== w || canvas.height !== h) { canvas.width = w; canvas.height = h; }
    gl.viewport(0, 0, w, h);
  }
  resize();
  window.addEventListener('resize', resize);
  if (window.ResizeObserver) new ResizeObserver(resize).observe(canvas);

  const vsSrc = `attribute vec2 a; void main(){ gl_Position = vec4(a, 0., 1.); }`;
  const fsSrc = `
    precision highp float;
    uniform vec2  u_res;
    uniform float u_time;
    uniform float u_intensity;
    uniform vec3  u_cA;
    uniform vec3  u_cB;
    uniform float u_fluidMix;
    uniform float u_pastel;
    uniform float u_vignFloor;
    float noise(vec2 p) { return fract(sin(dot(p, vec2(12.9898, 78.233))) * 43758.5453); }
    void main(){
      vec2 uv = gl_FragCoord.xy / u_res.xy;
      float ratio = u_res.x / u_res.y;
      vec2 p = uv; p.x *= ratio;
      float t = u_time * 0.18;
      vec2 shift = p;
      for (float i = 1.0; i < 4.0; i++) {
        shift.x += 0.42 / i * sin(i * 1.7 * p.y + t * 1.05);
        shift.y += 0.36 / i * cos(i * 1.7 * p.x + t * 0.95);
      }
      float diagonal = shift.x + shift.y * ratio;
      /* Bounded phase drift keeps motion alive without pushing band off-canvas */
      float sweep = sin(t * 0.72) * (0.32 * max(ratio, 0.35));
      float wobble = 0.22 * sin(p.y * 2.6 + t * 1.15)
                   + 0.16 * cos(p.x * 2.2 - t * 0.88)
                   + 0.09 * sin((p.x + p.y * 0.7) * 3.4 + t * 0.42);
      float target = ratio * 1.02 + wobble;
      float band = abs((diagonal + sweep) - target);
      band += 0.055 * (noise(p * 6.2 + vec2(t * 0.12, -t * 0.08)) - 0.5);
      /*
        band scales ~linearly with aspect ratio (width/height). Fixed smoothstep edges
        blow up on tall phones (tiny ratio) and invert smoothstep had edge0 > edge1 (undefined).
        Normalizing makes stripe width consistent across viewports.
      */
      float bandN = band / max(ratio, 0.04);
      /* Narrow core, soft falloff (valid smoothstep: low < high) */
      float mask = 1.0 - smoothstep(0.06, 0.74, bandN);
      float mixer    = smoothstep(0.2, 0.8, uv.x + sin(t * 0.5) * 0.2);
      vec3  bandCol  = mix(u_cA, u_cB, mixer);
      vec3  fluid    = bandCol * u_fluidMix;
      vec3  ice      = vec3(0.99, 0.99, 1.0);
      vec3  pastelCore = mix(ice, bandCol, 0.62);
      pastelCore = mix(pastelCore, vec3(1.0), 0.12);
      vec3  color = mix(fluid, pastelCore, u_pastel);
      float vign     = smoothstep(1.1, 0.3, length(uv - vec2(0.35, 0.5)));
      color *= mix(u_vignFloor, 1.0, vign);
      /*
        Peak opacity must exceed legacy “opaque mix × u_intensity”: same fluid over page bg disappears
        if α stays ~0.35. Boost coverage; pow softens halo without killing the stripe core.
      */
      float stripeAmp = clamp(mask * u_intensity * 3.05, 0.0, 1.0);
      float blendAlpha = clamp(pow(stripeAmp, 0.76), 0.0, 1.0);
      float g = (noise(uv + fract(u_time)) - 0.5) * 0.025;
      color += g * mix(1.0, 0.45, u_pastel) * stripeAmp;
      gl_FragColor = vec4(color, blendAlpha);
    }
  `;
  function mkS(type, src) {
    const s = gl.createShader(type);
    gl.shaderSource(s, src); gl.compileShader(s);
    return s;
  }
  const prog = gl.createProgram();
  gl.attachShader(prog, mkS(gl.VERTEX_SHADER, vsSrc));
  gl.attachShader(prog, mkS(gl.FRAGMENT_SHADER, fsSrc));
  gl.linkProgram(prog); gl.useProgram(prog);
  const buf = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, buf);
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1,1,-1,-1,1,-1,1,1,-1,1,1]), gl.STATIC_DRAW);
  const aLoc = gl.getAttribLocation(prog, 'a');
  gl.enableVertexAttribArray(aLoc);
  gl.vertexAttribPointer(aLoc, 2, gl.FLOAT, false, 0, 0);
  const uRes = gl.getUniformLocation(prog, 'u_res');
  const uTime = gl.getUniformLocation(prog, 'u_time');
  const uIntensity = gl.getUniformLocation(prog, 'u_intensity');
  const uCA = gl.getUniformLocation(prog, 'u_cA');
  const uCB = gl.getUniformLocation(prog, 'u_cB');
  const uFluidMix = gl.getUniformLocation(prog, 'u_fluidMix');
  const uPastel = gl.getUniformLocation(prog, 'u_pastel');
  const uVignFloor = gl.getUniformLocation(prog, 'u_vignFloor');
  function parseHexRgbNorm(hex) {
    let h = hex.trim().replace(/^#/, '');
    if (h.length === 3) h = h.split('').map((c) => c + c).join('');
    const n = parseInt(h, 16);
    if (!Number.isFinite(n) || h.length !== 6) return [0.39, 0.4, 0.59];
    return [(n >> 16) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
  }
  function parseFluidMix() {
    const v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--hr-shader-fluid-mix').trim());
    return Number.isFinite(v) ? v : 0.55;
  }
  function parsePastelUniform() {
    const v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--hr-shader-pastel').trim());
    return Number.isFinite(v) ? Math.min(1, Math.max(0, v)) : 0;
  }
  function parseVignFloor() {
    const v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--hr-shader-vign-floor').trim());
    return Number.isFinite(v) ? v : 0.85;
  }
  /*
    Los colores dependen solo del tema: se leen al arrancar y al cambiar la clase de <html>,
    no en cada frame (cada getComputedStyle obligaba a recalcular estilos 60 veces por segundo).
  */
  let theme;
  function readTheme() {
    const style = getComputedStyle(document.documentElement);
    theme = {
      intensity: parseFloat(style.getPropertyValue('--hr-shader-intensity').trim()) || 0.22,
      soft: parseHexRgbNorm(style.getPropertyValue('--hr-accent-soft')),
      accent: parseHexRgbNorm(style.getPropertyValue('--hr-accent')),
      fluidMix: parseFluidMix(),
      pastel: parsePastelUniform(),
      vignFloor: parseVignFloor(),
    };
  }
  readTheme();
  new MutationObserver(readTheme).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

  const t0 = performance.now();
  /* Start from a later visual phase so first frame looks "settled" */
  const shaderStartOffsetSec = 50.0;
  let active = true;
  let pending = null;
  function frame() {
    pending = null;
    if (!active || document.hidden) return;
    const t = ((performance.now() - t0) / 1000 + shaderStartOffsetSec) * 0.5;
    const { intensity, fluidMix, pastel, vignFloor } = theme;
    const [ar, ag, ab] = theme.soft;
    const [cr, cg, cb] = theme.accent;
    gl.clearColor(0, 0, 0, 0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    gl.uniform2f(uRes, canvas.width, canvas.height);
    gl.uniform1f(uTime, t);
    gl.uniform1f(uIntensity, intensity);
    gl.uniform3f(uCA, ar, ag, ab);
    gl.uniform3f(uCB, cr, cg, cb);
    gl.uniform1f(uFluidMix, fluidMix);
    gl.uniform1f(uPastel, pastel);
    gl.uniform1f(uVignFloor, vignFloor);
    gl.drawArrays(gl.TRIANGLES, 0, 6);
    pending = requestAnimationFrame(frame);
  }
  /* Un único bucle: entrar y salir de pantalla deprisa podía arrancar otro en paralelo */
  const resume = () => { if (active && !document.hidden && pending === null) frame(); };
  frame();
  document.addEventListener('visibilitychange', resume);
  if (window.IntersectionObserver) {
    new IntersectionObserver(([e]) => { active = e.isIntersecting; resume(); }, { threshold: 0 }).observe(canvas);
  }
})();
</script>


@endpush
