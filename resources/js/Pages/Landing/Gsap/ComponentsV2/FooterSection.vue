<template>
  <footer
    class="footer-root"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
  >
    <!-- ── Layer 1: Animated gradient background (always behind) ── -->
    <div class="footer-gradient" :class="{ active: hovered }" />

    <!-- ── Layer 2: Dark overlay that CONVERGES into oval on hover ── -->
    <div class="dark-overlay" :class="{ converged: hovered }" />

    <!-- ── Layer 3: Main area (always on top of overlay) ── -->
    <div class="footer-body">

      <!-- Floating social icon badges with INDIVIDUAL LINKS (visible on hover) -->
      <div class="floating-icons" :class="{ visible: hovered }">
        <!-- Masing-masing badge sekarang adalah <a> dengan link sendiri -->
        <a :href="iconLinks.pinterest" target="_blank" rel="noopener noreferrer" class="fi fi-1" title="Pinterest">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        </a>
        
        <a :href="iconLinks.behance" target="_blank" rel="noopener noreferrer" class="fi fi-2" title="Behance">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 7h-7V5h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14H15.97c.13 3.211 3.483 3.312 4.588 2.029H23.726zm-7.726-3h3.578c-.108-1.674-1.262-2.107-1.848-2.107-.78 0-1.56.467-1.73 2.107zM8 13.656c1.534.122 2.872.729 2.872 2.286 0 1.722-1.405 2.715-3.533 2.715H0V5h7.267c1.969 0 3.39.914 3.39 2.667 0 1.149-.576 1.957-1.657 2.378zM3 10.914h3.145c.97 0 1.667-.425 1.667-1.332 0-.862-.548-1.277-1.657-1.277H3v2.609zm0 4.91h3.445c.97 0 1.818-.423 1.818-1.441 0-1.018-.88-1.381-1.818-1.381H3v2.822z"/></svg>
        </a>
        
        <a :href="iconLinks.tiktok" target="_blank" rel="noopener noreferrer" class="fi fi-3" title="TikTok">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.73a8.28 8.28 0 004.85 1.56V6.84a4.85 4.85 0 01-1.08-.15z"/></svg>
        </a>
        
        <a :href="iconLinks.instagram" target="_blank" rel="noopener noreferrer" class="fi fi-4" title="Instagram">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        
        <a :href="iconLinks.x" target="_blank" rel="noopener noreferrer" class="fi fi-5" title="X">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.629L18.244 2.25zM17.083 20.027h1.833L7.084 4.126H5.117l11.966 15.901z"/></svg>
        </a>
        
        <a :href="iconLinks.threads" target="_blank" rel="noopener noreferrer" class="fi fi-6" title="Threads">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.964-.065-1.19.408-2.285 1.33-3.082.88-.76 2.119-1.207 3.583-1.291a13.853 13.853 0 012.087.068c-.188-.84-.187-1.55.002-2.1.198-.583.555-.99 1.12-1.252.533-.248 1.2-.356 2.033-.332.806.023 1.49.178 2.041.46.627.319 1.069.8 1.316 1.434.241.62.261 1.356.058 2.19-.203.832-.608 1.59-1.204 2.257-.595.667-1.38 1.226-2.332 1.664-.945.434-2.049.713-3.286.832-.388.037-.783.056-1.18.056h-.108c-.7 0-1.36-.09-1.953-.267a3.68 3.68 0 01-.72-.297c.02.233.052.463.097.686.207 1.02.68 1.897 1.37 2.526.685.625 1.597.986 2.712 1.073.117.01.236.014.354.014.847 0 1.666-.218 2.334-.617.748-.448 1.268-1.095 1.545-1.924l1.93.697c-.438 1.218-1.226 2.172-2.341 2.834C14.993 23.69 13.641 24 12.186 24z"/></svg>
        </a>
      </div>

      <!-- ── "Get in Touch" pill dengan HYPERLINK (seluruh area klikable) ── -->
      <!-- 
        ============================================================
        🔧 CUSTOMIZATION COMMENT:
        
        1. UNTUK MENGUBAH LINK:
           Ganti nilai 'getInTouchUrl' di bagian <script setup>
           Bisa pakai external URL (https://...) atau mailto:email@gmail.com
        
        2. UNTUK MENGGANTI MASCOT DENGAN GAMBAR CUSTOM:
           - Cari comment "REPLACE THIS BLOCK WITH CUSTOM IMAGE" di bawah
           - Ganti seluruh elemen <svg class="mascot-svg" ...> dengan:
             <img class="mascot-svg" src="/path/to/your-image.svg" alt="Mascot">
           - Atau jika pakai gambar PNG/JPG: 
             <img class="mascot-svg" src="/images/mascot.png" alt="Mascot">
           - Class "mascot-svg" tetap dipertahankan untuk styling (ukuran & animasi)
           - Pastikan gambar memiliki background transparan agar cocok dengan tema
        
        3. UNTUK MENGGANTI TEKS "Get in" dan "Touch":
           - Edit langsung di baris 146 dan 242 (tag <span class="git-text">)
           - Teks baru akan otomatis menyesuaikan ukuran (clamp responsive)
        
        4. STRUKTUR PILL:
           - Seluruh area (Get in + Mascot + Touch) terbungkus tag <a class="pill-link">
           - Jadi link akan tetap utuh meskipun Anda mengganti konten di dalamnya
        ============================================================
      -->
      <a :href="getInTouchUrl" target="_blank" rel="noopener noreferrer" class="pill-link">
        <div class="pill-wrapper">
          <span class="git-text">Get in</span>

          <!-- MASCOT SECTION - REPLACE THIS BLOCK WITH CUSTOM IMAGE IF NEEDED -->
          <div class="mascot-wrap">
            <!-- 
              🔁 UNTUK MENGGANTI DENGAN GAMBAR CUSTOM:
              Hapus tag <svg> di bawah ini dan ganti dengan:
              <img class="mascot-svg" src="your-image.svg" alt="Mascot Illustration">
            -->
            <svg class="mascot-svg" viewBox="0 0 120 180" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M60 155 Q85 165 90 150 Q95 135 80 128 Q72 124 68 130 Q65 138 60 155Z" fill="#c0392b" stroke="#1a1a1a" stroke-width="1.5"/>
              <path d="M62 147 Q80 152 82 142 Q84 133 74 130" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/>
              <rect x="46" y="130" width="14" height="32" rx="7" fill="#3498db"/>
              <rect x="62" y="130" width="14" height="32" rx="7" fill="#3498db"/>
              <ellipse cx="53" cy="162" rx="9" ry="5" fill="#1a1a1a"/>
              <ellipse cx="69" cy="162" rx="9" ry="5" fill="#1a1a1a"/>
              <rect x="38" y="90" width="44" height="48" rx="10" fill="#f0f0f0"/>
              <rect x="38" y="90" width="44" height="10" rx="5" fill="#c0392b"/>
              <rect x="44" y="104" width="32" height="28" rx="4" fill="#e8e8e8"/>
              <text x="60" y="116" text-anchor="middle" font-size="5" fill="#333" font-family="sans-serif" font-weight="bold">Graphic</text>
              <text x="60" y="123" text-anchor="middle" font-size="5" fill="#333" font-family="sans-serif">Design is</text>
              <text x="60" y="130" text-anchor="middle" font-size="5" fill="#c0392b" font-family="sans-serif" font-weight="bold">my Passion</text>
              <path d="M38 100 Q22 108 20 118 Q19 124 24 126 Q30 128 34 120 Q38 112 40 106Z" fill="#c0392b"/>
              <path d="M82 100 Q98 108 100 118 Q101 124 96 126 Q90 128 86 120 Q82 112 80 106Z" fill="#c0392b"/>
              <path d="M96 116 L108 114 Q112 113 112 116 Q112 119 108 119 L96 119Z" fill="#f5cba7"/>
              <ellipse cx="60" cy="64" rx="28" ry="28" fill="#c0392b"/>
              <circle cx="34" cy="44" r="10" fill="#c0392b"/>
              <circle cx="86" cy="44" r="10" fill="#c0392b"/>
              <circle cx="34" cy="44" r="5" fill="#e8a0a0"/>
              <circle cx="86" cy="44" r="5" fill="#e8a0a0"/>
              <ellipse cx="60" cy="70" rx="18" ry="16" fill="#f9e4d4"/>
              <circle cx="52" cy="60" r="5" fill="white"/>
              <circle cx="68" cy="60" r="5" fill="white"/>
              <circle cx="53" cy="61" r="3" fill="#1a1a1a"/>
              <circle cx="69" cy="61" r="3" fill="#1a1a1a"/>
              <circle cx="54" cy="60" r="1" fill="white"/>
              <circle cx="70" cy="60" r="1" fill="white"/>
              <ellipse cx="60" cy="70" rx="4" ry="3" fill="#c0392b"/>
              <ellipse cx="60" cy="70" rx="2.5" ry="1.8" fill="#a93226"/>
              <path d="M52 76 Q60 83 68 76" stroke="#a93226" stroke-width="1.5" fill="none" stroke-linecap="round"/>
              <ellipse cx="46" cy="68" rx="5" ry="3" fill="#e8a0a0" opacity="0.6"/>
              <ellipse cx="74" cy="68" rx="5" ry="3" fill="#e8a0a0" opacity="0.6"/>
              <path d="M35 55 Q60 45 85 55" stroke="#1a1a1a" stroke-width="2" fill="none"/>
            </svg>
          </div>
          <!-- END OF MASCOT SECTION - REPLACE ABOVE BLOCK WITH CUSTOM IMAGE -->

          <span class="git-text">Touch</span>
        </div>
      </a>

      <!-- Contact -->
      <p class="contact-line">
        <strong>Contact :</strong> supplaybox999@gmail.com
      </p>
    </div>

    <!-- ── Bottom bar ── -->
    <div class="footer-bar">
      <span class="copyright">All Right Reserved Supplaybox. 2026</span>

        <div class="socials">
            <span class="follow-label">Follow</span>
            <a href="https://www.instagram.com/supplaybox/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                </svg>
            </a>
            <a href="https://www.tiktok.com/@supplaybox" target="_blank" rel="noopener noreferrer" aria-label="Tiktok" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.73a8.28 8.28 0 004.85 1.56V6.84a4.85 4.85 0 01-1.08-.15z"/>
                </svg>
            </a>
            <a href="https://www.pinterest.com/supplaybox/" target="_blank" rel="noopener noreferrer" aria-label="Pinterest" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                </svg>
            </a>
            <a href="https://www.behance.net/supplaybox" target="_blank" rel="noopener noreferrer" aria-label="Behance" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22 7h-7V5h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14H15.97c.13 3.211 3.483 3.312 4.588 2.029H23.726zm-7.726-3h3.578c-.108-1.674-1.262-2.107-1.848-2.107-.78 0-1.56.467-1.73 2.107zM8 13.656c1.534.122 2.872.729 2.872 2.286 0 1.722-1.405 2.715-3.533 2.715H0V5h7.267c1.969 0 3.39.914 3.39 2.667 0 1.149-.576 1.957-1.657 2.378zM3 10.914h3.145c.97 0 1.667-.425 1.667-1.332 0-.862-.548-1.277-1.657-1.277H3v2.609zm0 4.91h3.445c.97 0 1.818-.423 1.818-1.441 0-1.018-.88-1.381-1.818-1.381H3v2.822z"/>
                </svg>
            </a>
            <a href="https://x.com/supplaybox" target="_blank" rel="noopener noreferrer" aria-label="X" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.629L18.244 2.25zM17.083 20.027h1.833L7.084 4.126H5.117l11.966 15.901z"/>
                </svg>
            </a>
            <a href="https://www.threads.net/@supplaybox" target="_blank" rel="noopener noreferrer" aria-label="Threads" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.964-.065-1.19.408-2.285 1.33-3.082.88-.76 2.119-1.207 3.583-1.291a13.853 13.853 0 012.087.068c-.188-.84-.187-1.55.002-2.1.198-.583.555-.99 1.12-1.252.533-.248 1.2-.356 2.033-.332.806.023 1.49.178 2.041.46.627.319 1.069.8 1.316 1.434.241.62.261 1.356.058 2.19-.203.832-.608 1.59-1.204 2.257-.595.667-1.38 1.226-2.332 1.664-.945.434-2.049.713-3.286.832-.388.037-.783.056-1.18.056h-.108c-.7 0-1.36-.09-1.953-.267a3.68 3.68 0 01-.72-.297c.02.233.052.463.097.686.207 1.02.68 1.897 1.37 2.526.685.625 1.597.986 2.712 1.073.117.01.236.014.354.014.847 0 1.666-.218 2.334-.617.748-.448 1.268-1.095 1.545-1.924l1.93.697c-.438 1.218-1.226 2.172-2.341 2.834C14.993 23.69 13.641 24 12.186 24z"/>
                </svg>
            </a>
            <a href="https://www.facebook.com/supplaybox" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="social-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
        </div>
      <!-- <div class="socials">
        <span class="follow-label">Follow</span>
        <a v-for="s in socials" :key="s.name" :href="s.url" target="_blank" rel="noopener noreferrer" :aria-label="s.name" class="social-icon">
          <span v-html="s.svg"></span>
        </a>
      </div> -->
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const hovered = ref(false)

// ============================================================
// 🔗 LINK UNTUK FLOATING ICONS (masing-masing bisa diubah sendiri)
// ============================================================
const iconLinks = {
  pinterest: 'https://www.pinterest.com/supplaybox/',
  behance: 'https://www.behance.net/supplaybox',
  tiktok: 'https://www.tiktok.com/@supplaybox',
  instagram: 'https://www.instagram.com/supplaybox/',
  x: 'https://x.com/supplaybox',
  threads: 'https://www.threads.net/@supplaybox'
}

// ============================================================
// 🔗 LINK UNTUK "GET IN TOUCH" BUTTON (seluruh area pill)
// Ganti sesuai kebutuhan:
// - Pakai URL: 'https://supplaybox.com/contact'
// - Pakai Email: 'mailto:supplaybox999@gmail.com'
// - Pakai WhatsApp: 'https://wa.me/62xxxxxxxxxx'
// ============================================================
const getInTouchUrl = 'https://supplaybox.com/contact' // Ganti dengan link yang diinginkan
</script>

<style scoped>
/* ── semua CSS tetap sama seperti kode asli Anda ── */
.footer-root {
  position: relative;
  overflow: hidden;
  background: #0d0d0d;
  cursor: pointer;
  border-top: 1px solid rgba(255,255,255,0.06);
}

.footer-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #e8b4f8 0%, #c4a1f5 10%, #9bcffa 25%, #b8f0c8 45%, #a8e8d8 60%, #7dd8c8 75%, #85d8a8 90%, #a2e8b0 100%);
  opacity: 0;
  transition: opacity 0.55s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 0;
}
.footer-gradient.active { opacity: 1; }

.dark-overlay {
  position: absolute;
  inset: 0;
  background: #0d0d0d;
  clip-path: inset(0% 0% 0% 0% round 0px);
  transition: clip-path 0.65s cubic-bezier(0.77, 0, 0.175, 1);
  z-index: 1;
}
.dark-overlay.converged { clip-path: inset(12% 4% 12% 4% round 999px); }

.footer-body {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 48px 40px 24px;
  gap: 12px;
}

/* Style untuk pill link (agar tidak ada underline default) */
.pill-link {
  text-decoration: none;
  display: block;
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
}

.pill-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 40px;
  width: 100%;
  max-width: 1100px;
}

.git-text {
  color: #ffffff;
  font-size: clamp(48px, 10vw, 148px);
  font-weight: 900;
  line-height: 1;
  letter-spacing: -0.03em;
  font-family: 'Arial Black', 'Helvetica Neue', sans-serif;
  white-space: nowrap;
  text-transform: capitalize;
  user-select: none;
}

.mascot-wrap {
  flex-shrink: 0;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin: 0 8px;
  position: relative;
  z-index: 2;
}
.mascot-svg {
  width: clamp(60px, 8vw, 110px);
  height: auto;
  animation: mascot-idle 2.8s ease-in-out infinite;
  transform-origin: bottom center;
  filter: drop-shadow(0 4px 12px rgba(0,0,0,0.5));
}
@keyframes mascot-idle {
  0%, 100% { transform: translateY(0) rotate(-1deg); }
  30% { transform: translateY(-6px) rotate(1deg); }
  60% { transform: translateY(-3px) rotate(-0.5deg); }
}

.contact-line { color: rgba(255,255,255,0.6); font-size: 14px; letter-spacing: 0.02em; margin: 0; }
.contact-line strong { color: #ffffff; }

.floating-icons {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 2;
}
.fi {
  position: absolute;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.4) translateY(20px);
  transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  pointer-events: none;
  text-decoration: none; /* hilangkan underline pada link */
}
.floating-icons.visible .fi {
  opacity: 1;
  transform: scale(1) translateY(0);
  pointer-events: auto; /* aktifkan klik saat visible */
}
.fi svg { width: 18px; height: 18px; }

.fi-1 { top: 18%; left: 4%; background: #9b59b6; color: #fff; transition-delay: 0.05s; animation: float-a 3.2s ease-in-out infinite 0.6s; }
.fi-2 { top: 10%; left: 28%; background: #27ae60; color: #fff; transition-delay: 0.10s; animation: float-b 2.8s ease-in-out infinite 0.2s; }
.fi-3 { top: 8%; left: 50%; background: #1a1a1a; color: #fff; transition-delay: 0.15s; animation: float-a 3.5s ease-in-out infinite 1.0s; border: 2px solid #fff; }
.fi-4 { top: 12%; right: 28%; background: #27ae60; color: #fff; transition-delay: 0.12s; animation: float-b 3.0s ease-in-out infinite 0.4s; }
.fi-5 { top: 22%; right: 5%; background: #e74c3c; color: #fff; transition-delay: 0.08s; animation: float-a 2.6s ease-in-out infinite 0.8s; }
.fi-6 { bottom: 22%; left: 6%; background: #95a5a6; color: #fff; transition-delay: 0.18s; animation: float-b 3.3s ease-in-out infinite 1.2s; }

@keyframes float-a { 0%, 100% { transform: scale(1) translateY(0px) rotate(-3deg); } 50% { transform: scale(1) translateY(-10px) rotate(3deg); } }
@keyframes float-b { 0%, 100% { transform: scale(1) translateY(0px) rotate(4deg); } 50% { transform: scale(1) translateY(-12px) rotate(-4deg); } }

.footer-bar {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 40px;
  border-top: 1px solid rgba(255,255,255,0.08);
}
.copyright { color: rgba(255,255,255,0.25); font-size: 12px; }
.socials { display: flex; align-items: center; gap: 4px; }
.follow-label { color: rgba(255,255,255,0.5); font-size: 13px; font-weight: 600; margin-right: 6px; }
.social-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.55);
  border-radius: 50%;
  transition: color 0.2s, background 0.2s;
  text-decoration: none;
}
.social-icon:hover { color: #fff; background: rgba(255,255,255,0.1); }
.social-icon svg { width: 15px; height: 15px; }
</style>