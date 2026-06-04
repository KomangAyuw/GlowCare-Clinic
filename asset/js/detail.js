function toggleFaq(btn) {
    var answer = btn.nextElementSibling;
    var isOpen = btn.classList.contains('open');
 
    // Tutup semua FAQ lain dulu
    document.querySelectorAll('.faq-q.open').forEach(function(openBtn) {
        openBtn.classList.remove('open');
        openBtn.nextElementSibling.classList.remove('visible');
    });
 
    // Buka yang diklik (jika belum terbuka)
    if (!isOpen) {
        btn.classList.add('open');
        answer.classList.add('visible');
    }
}
 
// ── Smooth scroll ──
document.querySelectorAll('a[href^="#"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
 
// ── Header scroll effect ──
var header = document.querySelector('header');
if (header) {
    window.addEventListener('scroll', function() {
        if (window.scrollY > 60) {
            header.style.background = '#458b8b';
            header.style.boxShadow = '0 2px 24px rgba(0,0,0,0.06)';
            document.querySelectorAll('nav a').forEach(function(a) {
                a.style.color = '#ffffff';
            });
            document.querySelector('.logo').style.color = '#ffffff';
        } else {
            header.style.background = '';
            header.style.boxShadow = '';
            document.querySelectorAll('nav a').forEach(function(a) {
                a.style.color = '';
            });
            document.querySelector('.logo').style.color = '';
        }
    });
}