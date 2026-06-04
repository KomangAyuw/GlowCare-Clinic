<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jadwal Dokter - GlowCare Clinic</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400&family=DM+Sans:wght@300;400;500&display=swap">
        <link rel="stylesheet" href="asset/css/style.css?v=4">
        <style>
            body {
                min-height: 100vh;
                background: #e6f0f0;
                padding-top: 100px;
            }
        </style>
    </head>
    <body>
        <?php include 'backend/nav.php'; ?>
        <main>
            <h1 class="page-title">Jadwal <em>Dokter & Spesialis</em></h1>
            <p class="page-sub">Jadwal praktik dokter minggu ini.</p>

            <table class="jadwal-table">
                <thead> <!-- ini judul tabel -->
                    <tr> <!-- ini baris pada tabel -->
                        <th>Dokter</th> <!-- ini sel header -->
                        <th>Senin - Rabu</th>
                        <th>Kamis - Jumat</th>
                        <th>Sabtu</th>
                        <th>Minggu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody> <!-- ini isi data tabel -->
                    <tr>
                        <td> <!-- ini sel data dalam baris -->
                            <span class="td-nama">dr. Amelia Putri</span>
                            <span class="td-spesialis">Sp. Kulit & Kelamin</span>
                        </td>
                        <td class="td-jam">09.00 - 14.00</td>
                        <td class="td-jam">13.00 - 18.00</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Aktif</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="td-nama">dr. Sarah Wijaya</span>
                            <span class="td-spesialis">Aesthetic Medicine</span>
                        </td>
                        <td class="td-jam">10.00 - 16.00</td>
                        <td class="td-jam">10.00 - 16.00</td>
                        <td class="td-jam">09.00 - 13.00</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Aktif</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="td-nama">dr. Reza Mahardika</span>
                            <span class="td-spesialis">Laser Specialist</span>
                        </td>
                        <td class="td-jam">13.00 - 19.00</td>
                        <td class="td-jam">13.00 - 19.00</td>
                        <td class="td-jam">10.00 - 15.00</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Aktif</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="td-nama">dr. Nadia Sari</span>
                            <span class="td-spesialis">Anti-Aging Expert</span>
                        </td>
                        <td class="td-jam">08.00 - 13.00</td>
                        <td class="td-jam">08.00 - 13.00</td>
                        <td class="td-jam">09.00 - 12.00</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Aktif</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="td-nama">dr. Kevin Hartanto</span>
                            <span class="td-spesialis">Body Treatment</span>
                        </td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Libur</td>
                        <td class="td-jam">Cuti</td>
                    </tr>
                </tbody>
            </table>
        </main>

    </body>
</html>