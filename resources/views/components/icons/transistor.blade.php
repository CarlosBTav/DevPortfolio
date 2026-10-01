{{-- Transistor en encapsulado TO-92 (el clásico negro de tres patas), a color --}}
<svg {{ $attributes->merge(['class' => 'w-6 h-6']) }} viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="to92-body" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#111827"/>
            <stop offset=".45" stop-color="#4b5563"/>
            <stop offset="1" stop-color="#1f2937"/>
        </linearGradient>
    </defs>
    <path d="M11.5 18v11M16 18v11M20.5 18v11" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round"/>
    <path d="M8 18.5V9.5a8 8 0 0 1 16 0v9a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1z" fill="url(#to92-body)"/>
    <path d="M11 9.5h10M12 12.5h8" stroke="#e5e7eb" stroke-width="1.1" stroke-linecap="round" opacity=".75"/>
</svg>
