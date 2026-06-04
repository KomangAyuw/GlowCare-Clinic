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

// ── Header berubah saat scroll ──
var header = document.querySelector('header');
if (header) {
    window.addEventListener('scroll', function() {
        if (window.scrollY > 60) {
            header.style.background = '#458b8b';
            header.style.backdropFilter = 'blur(12px)';
            header.style.boxShadow = '0 2px 24px rgba(0,0,0,0.06)';
            document.querySelectorAll('nav a').forEach(function(a) { a.style.color = '#ffffff'; });
            document.querySelector('.logo').style.color = '#ffffff';
        } else {
            header.style.background = '';
            header.style.backdropFilter = '';
            header.style.boxShadow = '';
            document.querySelectorAll('nav a').forEach(function(a) { a.style.color = ''; });
            document.querySelector('.logo').style.color = '';
        }
    });
}

// ── Validasi & Submit Form ──
const form = document.querySelector('.kontak-form');
if (form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const nama  = form.nama.value.trim();
        const telp  = form.telp.value.trim();
        const email = form.email.value.trim();
        const pesan = form.pesan.value.trim();

        if (!nama && !telp && !email && !pesan) { 
            alert('Semua form wajib diisi!'); 
            return; 
        }
        else if (!nama) { 
            alert('Nama tidak boleh kosong!'); 
            return; 
        }
        else if (/[0-9]/.test(nama)) { 
            alert('Nama tidak boleh mengandung angka!'); 
            return; 
        }
        else if (/[^a-zA-Z\s]/.test(nama)) { 
            alert('Nama tidak boleh mengandung karakter khusus!'); 
            return; 
        }
        else if (!telp) { 
            alert('Nomor telepon tidak boleh kosong!'); 
            return; 
        }
        else if (/[^0-9+]/.test(telp)) { 
            alert('Nomor telepon hanya boleh berisi angka!'); 
            return; 
        }
        else if (telp.length < 10) { 
            alert('Nomor telepon minimal 10 digit!'); 
            return; 
        }
        else if (telp.length > 15) { 
            alert('Nomor telepon maksimal 15 digit!'); 
            return; 
        }
        else if (!email) { 
            alert('Email tidak boleh kosong!'); 
            return; 
        }
        else if (!email.includes('@')) { 
            alert('Format email tidak valid!'); 
            return; 
        }
        else if (!pesan) { alert('Pesan tidak boleh kosong!'); 
            return; 
        }

        form.submit();
    });
}

// ── FAQ ──
function toggleFaq(btn) {
    var answer = btn.nextElementSibling;
    var isOpen = btn.classList.contains('open');
    document.querySelectorAll('.faq-q.open').forEach(function(openBtn) {
        openBtn.classList.remove('open');
        openBtn.nextElementSibling.classList.remove('visible');
    });
    if (!isOpen) {
        btn.classList.add('open');
        answer.classList.add('visible');
    }
}

// ── Auto-scroll ke #kontak jika ada notifikasi ──
if (new URLSearchParams(window.location.search).has('success') ||
    new URLSearchParams(window.location.search).has('error')) {
    document.getElementById('kontak').scrollIntoView({ behavior: 'smooth' });
}