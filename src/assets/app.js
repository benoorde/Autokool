// Dünaamilised efektid: ilmumine kerimisel, loendurid, 3D kaldumine kaartidel
(function () {
  'use strict';

  // Elemendid ilmuvad, kui need kerimisel nähtavale tulevad
  const reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting) {
          en.target.classList.add('in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12 });
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add('in'));
  }

  // Animeeritud loendurid
  document.querySelectorAll('.count').forEach((el) => {
    const target = Number(el.dataset.to) || 0;
    const start = performance.now();
    const duration = 1500;
    function frame(now) {
      const p = Math.min((now - start) / duration, 1);
      el.textContent = Math.round(target * p).toLocaleString();
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  });

  // Kaardi kallutamine hiirega
  document.querySelectorAll('.tilt').forEach((card) => {
    card.addEventListener('mousemove', (e) => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `perspective(700px) rotateX(${(-y * 8).toFixed(2)}deg) rotateY(${(x * 8).toFixed(2)}deg)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
  });

  // Päise vari kerimisel
  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    if (header) header.style.opacity = window.scrollY > 40 ? '0.97' : '1';
  }, { passive: true });
})();
