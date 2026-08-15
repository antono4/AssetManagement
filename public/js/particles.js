/* ==================================================================
   Particle background — ringan untuk halaman login.
   Canvas full-screen, partikel bergerak & terhubung garis halus.
   ================================================================== */
(function () {
    const canvas = document.getElementById('particles');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let w, h, particles;
    const COUNT = 70;

    function resize() {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
    }

    function init() {
        resize();
        particles = [];
        for (let i = 0; i < COUNT; i++) {
            particles.push({
                x: Math.random() * w,
                y: Math.random() * h,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                r: Math.random() * 1.6 + 0.4,
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, w, h);

        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0 || p.x > w) p.vx *= -1;
            if (p.y < 0 || p.y > h) p.vy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(165, 180, 252, 0.65)';
            ctx.fill();
        }

        // garis koneksi
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 120) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = `rgba(99, 102, 241, ${0.15 * (1 - dist / 120)})`;
                    ctx.lineWidth = 0.6;
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', () => { resize(); init(); });
    init();
    draw();
})();
