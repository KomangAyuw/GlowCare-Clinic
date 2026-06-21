const titles = {dashboard:'Dashboard',pasien:'Data Pasien',dokter:'Data Dokter',jadwal:'Jadwal Dokter',aktivitas:'Aktivitas',pesan:'Pesan Kontak',laporan:'Laporan',treatment:'Treatment',profil:'Profil',keuangan:'Keuangan',pengumuman:'Pengumuman'};

function showPanel(id,el){
    document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
    document.getElementById('panel-'+id).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
    if(el) el.classList.add('active');
    document.getElementById('topbar-title').textContent=titles[id]||id;
    document.getElementById('topbar-bc').textContent='GlowCare Admin → '+(titles[id]||id);

    if (id === 'aktivitas') {
        const badge = document.getElementById('aktivitas-badge');
        if (badge) badge.style.display = 'none';
        fetch('../../backend/admin/baca_log.php');
    }
}
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function closeModalOutside(e,id){if(e.target.classList.contains('modal-overlay'))closeModal(id);}
function showToast(msg,ok=true){
    const t=document.getElementById('toast');
    t.innerHTML=(ok?'✅ ':'❌ ')+'<span>'+msg+'</span>';
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),3500);
}

// ── PASIEN ──
function editPasien(p){
    document.getElementById('mp-title').innerHTML='Edit <em>Pasien</em>';
    document.getElementById('form-pasien').action='../../backend/admin/simpan_pasien.php';
    document.getElementById('mp-id').value=p.id;
    document.getElementById('mp-nama').value=p.nama;
    document.getElementById('mp-no').value=p.no_pasien;
    document.getElementById('mp-tgl').value=p.tanggal_lahir||'';
    document.getElementById('mp-jk').value=p.jenis_kelamin;
    document.getElementById('mp-telp').value=p.telepon||'';
    document.getElementById('mp-email').value=p.email||'';
    document.getElementById('mp-alamat').value=p.alamat||'';
    document.getElementById('mp-status').value=p.status;
    document.getElementById('mp-catatan').value=p.catatan_medis||'';
    openModal('modal-pasien');
}
document.getElementById('modal-pasien').addEventListener('click',function(e){
    if(e.target===this) closeModal('modal-pasien');
});
document.querySelector('#form-pasien button[type=submit]').addEventListener('click',function(){
    const f=document.getElementById('form-pasien');
    if(!f.action.includes('simpan_pasien')) f.action='../../backend/admin/simpan_pasien.php';
});
// Reset modal pasien saat tambah baru
document.querySelector('[onclick="openModal(\'modal-pasien\')"]') && document.querySelectorAll('[onclick="openModal(\'modal-pasien\')"]').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.getElementById('mp-title').innerHTML='Tambah <em>Pasien</em>';
        document.getElementById('form-pasien').action='../../backend/admin/simpan_pasien.php';
        document.getElementById('form-pasien').reset();
        document.getElementById('mp-id').value='';
    });
});

// ── DOKTER ──
function editDokter(d){
    document.getElementById('md-title').innerHTML='Edit <em>Dokter</em>';
    document.getElementById('form-dokter').action='../../backend/admin/simpan_dokter.php';
    document.getElementById('md-id').value=d.id;
    document.getElementById('md-current-foto').value=d.foto||'';
    document.getElementById('md-foto').value='';
    document.getElementById('md-foto').required=false;
    document.getElementById('md-nama').value=d.nama;
    document.getElementById('md-str').value=d.no_str||'';
    document.getElementById('md-spesialis').value=d.spesialisasi;
    document.getElementById('md-telp').value=d.telepon||'';
    document.getElementById('md-email').value=d.email||'';
    document.getElementById('md-exp').value=d.pengalaman||0;
    const ratingEl = document.getElementById('md-rating-display');
    if (ratingEl) ratingEl.value = d.rating ? d.rating + ' / 5.0' : 'Belum ada ulasan';
    document.getElementById('md-status').value=d.status;
    document.getElementById('md-bio').value=d.bio||'';
    openModal('modal-dokter');
}

// ── JADWAL ──
function editJadwal(j){
    document.getElementById('mj-title').innerHTML='Edit <em>Jadwal</em>';
    document.getElementById('form-jadwal').action='../../backend/admin/simpan_jadwal.php';
    document.getElementById('mj-id').value=j.id;
    document.getElementById('mj-dokter').value=j.dokter_id;
    document.getElementById('mj-hari').value=j.hari;
    document.getElementById('mj-mulai').value=j.jam_mulai;
    document.getElementById('mj-selesai').value=j.jam_selesai;
    document.getElementById('mj-max').value=j.max_pasien;
    document.getElementById('mj-treatment').value=j.treatment_id||'';
    document.getElementById('mj-status').value=j.status;
    openModal('modal-jadwal');
}

// ── TREATMENT ──
function editTreatment(t){
    document.getElementById('mt-title').innerHTML='Edit <em>Treatment</em>';
    document.getElementById('form-treatment').action='../../backend/admin/simpan_treatment.php';
    document.getElementById('mt-id').value=t.id;
    document.getElementById('mt-nama').value=t.nama;
    document.getElementById('mt-kategori').value=t.kategori;
    document.getElementById('mt-durasi').value=t.durasi||'60 Menit';
    document.getElementById('mt-urutan').value=t.urutan;
    document.getElementById('mt-desc').value=t.deskripsi||'';
    document.getElementById('mt-desc-panjang').value=t.deskripsi_panjang||'';
    document.getElementById('mt-status').value=t.status;
    // Simpan URL gambar lama dan tampilkan preview
    const lamaEl = document.getElementById('mt-gambar-lama');
    const previewWrap = document.getElementById('mt-gambar-preview-wrap');
    const previewImg = document.getElementById('mt-gambar-preview');
    if (lamaEl) lamaEl.value = t.gambar_url || '';
    if (previewWrap && previewImg && t.gambar_url) {
        previewImg.src = t.gambar_url;
        previewWrap.style.display = 'block';
    } else if (previewWrap) {
        previewWrap.style.display = 'none';
    }
    // Reset file input
    const fileEl = document.getElementById('mt-gambar');
    if (fileEl) fileEl.value = '';
    openModal('modal-treatment');
function previewTreatmentImage(input) {
    const wrap = document.getElementById('mt-gambar-preview-wrap');
    const img  = document.getElementById('mt-gambar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

}

function lihatPesan(pk) {
    document.getElementById('mp-detail-nama').textContent  = pk.nama  || '-';
    document.getElementById('mp-detail-telp').textContent  = pk.telepon || '-';
    document.getElementById('mp-detail-email').textContent = pk.email  || '-';
    document.getElementById('mp-detail-pesan').textContent = pk.pesan  || '-';
    document.getElementById('mp-pengirim-sub').textContent =
        'Dikirim pada ' + new Date(pk.created_at.replace(' ','T')).toLocaleString('id-ID');

    // Isi ID untuk form balas
    document.getElementById('mp-balas-id').value = pk.id || '';
    document.getElementById('mp-balas-teks').value = '';

    // Tampilkan balasan sebelumnya jika ada
    const balasanBox  = document.getElementById('mp-balasan-box');
    const balasanIsi  = document.getElementById('mp-balasan-isi');
    const balasanInfo = document.getElementById('mp-balasan-info');
    const labelBalas  = document.querySelector('#mp-form-balas > div:first-child');
    const btnKirim    = document.getElementById('mp-btn-kirim');

    if (pk.balasan && pk.balasan.trim() !== '') {
        balasanBox.style.display = 'block';
        balasanIsi.textContent   = pk.balasan;
        balasanInfo.textContent  = 'Dibalas'
            + (pk.dibalas_oleh ? ' oleh ' + pk.dibalas_oleh : '')
            + (pk.dibalas_at   ? ' · ' + new Date(pk.dibalas_at.replace(' ','T')).toLocaleString('id-ID') : '');
        if (labelBalas) labelBalas.textContent = 'Ubah Balasan';
        if (btnKirim)   btnKirim.textContent   = 'Perbarui Balasan';
    } else {
        balasanBox.style.display = 'none';
        if (labelBalas) labelBalas.textContent = 'Tulis Balasan';
        if (btnKirim)   btnKirim.textContent   = 'Kirim Balasan';
    }

    // Tombol "Tandai Baca" jika belum dibaca
    const footer   = document.getElementById('mp-detail-footer');
    const existing = footer.querySelector('form.baca-form');
    if (existing) existing.remove();

    if (!pk.sudah_baca || pk.sudah_baca == 0) {
        const f = document.createElement('form');
        f.method  = 'POST';
        f.action  = '../../backend/admin/kelola_pesan.php';
        f.className = 'baca-form';
        f.style.display = 'inline';
        f.innerHTML = `
            <input type="hidden" name="aksi" value="baca">
            <input type="hidden" name="id"   value="${pk.id}">
            <button type="submit" class="btn-save" style="font-size:11px;padding:7px 14px">Tandai Sudah Dibaca</button>
        `;
        footer.appendChild(f);
    }

    openModal('modal-pesan');
}
// Set form action untuk Tambah baru
document.getElementById('form-pasien').action='../../backend/admin/simpan_pasien.php';
document.getElementById('form-dokter').action='../../backend/admin/simpan_dokter.php';
document.getElementById('form-jadwal').action='../../backend/admin/simpan_jadwal.php';
document.getElementById('form-treatment').action='../../backend/admin/simpan_treatment.php';

// ── CONFIRM DELETE ──
function confirmDelete(file,id,nama){
    document.getElementById('confirm-desc').textContent='Data '+nama+' akan dihapus permanen dan tidak bisa dikembalikan.';
    document.getElementById('confirm-form').action='../../backend/admin/'+file;
    document.getElementById('confirm-id').value=id;
    document.getElementById('confirm-overlay').classList.add('open');
}
function closeConfirm(){document.getElementById('confirm-overlay').classList.remove('open');}

// Toast dari URL param
const up=new URLSearchParams(window.location.search);
if(up.get('success')) showToast(decodeURIComponent(up.get('success')));
if(up.get('error'))   showToast(decodeURIComponent(up.get('error')),false);