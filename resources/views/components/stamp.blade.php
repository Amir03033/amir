{{-- The hero stamp (DESIGN.md §6): a thin burgundy ring with circular mono text. stamp.js adds the tilt and the imprints. --}}
<div class="stamp-wrap" aria-hidden="false">
    <button type="button" class="stamp" data-stamp aria-label="{{ __('site.hero.stamp') }}">
        <svg class="stamp-svg" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
            <defs><path id="stamp-path" d="M50,50 m-37,0 a37,37 0 1,1 74,0 a37,37 0 1,1 -74,0" /></defs>
            <circle cx="50" cy="50" r="48.5" />
            <circle cx="50" cy="50" r="27" class="stamp-inner" />
            <g class="stamp-ring">
                <text><textPath href="#stamp-path" textLength="230" lengthAdjust="spacing">AMIRS.LIFE · ZWOLLE · AMSTERDAM ·</textPath></text>
            </g>
            <circle cx="50" cy="50" r="3" class="stamp-dot" />
        </svg>
    </button>
</div>
