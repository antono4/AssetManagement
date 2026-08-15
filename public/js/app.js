/* ==================================================================
   Asset Management — app.js
   Helpers umum: custom cursor, toast, fetch API wrapper, dll.
   ================================================================== */

// ---------- Custom interactive cursor ----------
(function initCursor() {
    if (window.matchMedia('(hover: none)').matches) return;
    const dot  = document.createElement('div');
    const ring = document.createElement('div');
    dot.className  = 'cursor-dot';
    ring.className = 'cursor-ring';
    document.body.appendChild(dot);
    document.body.appendChild(ring);

    let mx = 0, my = 0, rx = 0, ry = 0;
    window.addEventListener('mousemove', (e) => {
        mx = e.clientX; my = e.clientY;
        dot.style.transform = `translate(${mx}px, ${my}px) translate(-50%, -50%)`;
    });
    function loop() {
        rx += (mx - rx) * 0.18;
        ry += (my - ry) * 0.18;
        ring.style.transform = `translate(${rx}px, ${ry}px) translate(-50%, -50%)`;
        requestAnimationFrame(loop);
    }
    loop();

    document.addEventListener('mouseover', (e) => {
        if (e.target.closest('a, button, .nav-link, input, select, textarea, [data-cursor]')) {
            ring.classList.add('is-hover');
        }
    });
    document.addEventListener('mouseout', (e) => {
        if (e.target.closest('a, button, .nav-link, input, select, textarea, [data-cursor]')) {
            ring.classList.remove('is-hover');
        }
    });
})();

// ---------- Toast ----------
function toast(message, type = 'info', duration = 3500) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = message;
    container.appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';
        setTimeout(() => el.remove(), 300);
    }, duration);
}

// ---------- API fetch wrapper ----------
async function api(url, options = {}) {
    const opts = {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        ...options,
    };
    if (opts.body && !(opts.body instanceof FormData) && typeof opts.body === 'object') {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(opts.body);
    }
    try {
        const res = await fetch(url, opts);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            const msg = data.message || `Terjadi kesalahan (${res.status}).`;
            return { ok: false, status: res.status, data, message: msg };
        }
        return { ok: true, status: res.status, data };
    } catch (err) {
        return { ok: false, status: 0, data: {}, message: 'Tidak dapat terhubung ke server.' };
    }
}

// ---------- Helpers format ----------
function formatRupiah(n) {
    const num = Number(n) || 0;
    return 'Rp ' + num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

function formatDate(d) {
    if (!d) return '-';
    const date = new Date(d);
    if (isNaN(date)) return d;
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function statusBadge(status) {
    const label = { tersedia: 'Tersedia', dipinjam: 'Dipinjam', rusak: 'Rusak' }[status] || status;
    return `<span class="badge badge-${status}">${label}</span>`;
}

function roleBadge(role) {
    const label = { admin: 'Admin', staff: 'Staff' }[role] || role;
    return `<span class="badge badge-${role}">${label}</span>`;
}

// ---------- Modal helper ----------
function openModal(html) {
    let overlay = document.getElementById('modal-root');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'modal-root';
        document.body.appendChild(overlay);
    }
    overlay.innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal-panel glass p-6">${html}</div>
        </div>`;
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    const overlay = document.getElementById('modal-root');
    if (overlay) overlay.innerHTML = '';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});
