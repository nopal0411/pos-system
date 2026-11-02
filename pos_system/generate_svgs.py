import re

def M(x, y): return f"M {x} {y}"
def L(x, y): return f"L {x} {y}"
def V(y): return f"V {y}"
def H(x): return f"H {x}"

def path(d, arrow=True, color="#000"):
    marker = ' marker-end="url(#arrow)"' if arrow else ''
    if color == "#f00" and arrow:
        marker = ' marker-end="url(#arrow-red)"'
    return f'<path d="{d}" fill="none" stroke="{color}" stroke-width="1.5"{marker}/>'

def rect(x, y, w, h, text1, text2="", rx=8):
    lines = f'<text x="{x+w/2}" y="{y+h/2+4}" text-anchor="middle">{text1}</text>'
    if text2:
        lines = f'<text x="{x+w/2}" y="{y+h/2-4}" text-anchor="middle">{text1}</text>\n<text x="{x+w/2}" y="{y+h/2+12}" text-anchor="middle">{text2}</text>'
    return f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="{rx}" fill="#fff" stroke="#000" stroke-width="1"/>\n{lines}'

def decision(x, y, text1, text2=""):
    # w=150, h=60 => left x-75, top y-30
    pts = f"{x},{y-30} {x+75},{y} {x},{y+30} {x-75},{y}"
    lines = f'<text x="{x}" y="{y+4}" text-anchor="middle">{text1}</text>'
    if text2:
        lines = f'<text x="{x}" y="{y-4}" text-anchor="middle">{text1}</text>\n<text x="{x}" y="{y+12}" text-anchor="middle">{text2}</text>'
    return f'<polygon points="{pts}" fill="#fff" stroke="#000" stroke-width="1"/>\n{lines}'

def text(x, y, t, anchor="middle", color="#000", bg=None):
    if bg:
        # crude rect behind text
        return f'<rect x="{x-40}" y="{y-12}" width="80" height="18" fill="{bg}" stroke="#000" stroke-width="1"/><text x="{x}" y="{y}" text-anchor="{anchor}" fill="{color}">{t}</text>'
    return f'<text x="{x}" y="{y}" text-anchor="{anchor}" fill="{color}">{t}</text>'

def sync_bar(x, y, w):
    return f'<rect x="{x}" y="{y}" width="{w}" height="6" fill="#000"/>'

# ================= Diagram 1 =================
svg1 = []
# Headers
svg1.append('<rect x="0" y="0" width="470" height="40" fill="#fff" stroke="#000" stroke-width="1"/>')
svg1.append('<rect x="470" y="0" width="470" height="40" fill="#fff" stroke="#000" stroke-width="1"/>')
svg1.append('<text x="235" y="25" text-anchor="middle" font-weight="bold">User (Admin)</text>')
svg1.append('<text x="705" y="25" text-anchor="middle" font-weight="bold">Sistem</text>')
svg1.append('<line x1="470" y1="40" x2="470" y2="1580" stroke="#000" stroke-width="1"/>')

svg1.append('<circle cx="235" cy="80" r="12" fill="#000"/>')
svg1.append(path(f"{M(235,92)} {V(148)}", color="#f00"))

svg1.append(rect(120, 148, 230, 46, "Buka Aplikasi POS System"))
svg1.append(path(f"{M(350,171)} {H(540)}"))

svg1.append(rect(540, 148, 230, 46, "Tampilkan Halaman Login"))
svg1.append(path(f"{M(655,194)} {V(233)} {H(350)}"))

svg1.append(rect(120, 210, 230, 46, "Input Username & Password"))
svg1.append(path(f"{M(235,256)} {V(287)} {H(540)}"))

svg1.append(rect(540, 264, 230, 46, "Validasi Kredensial (Cek DB)"))
svg1.append(path(f"{M(655,310)} {V(346)}"))

svg1.append(decision(655, 376, "Valid?"))
svg1.append(path(f"{M(730,376)} {H(820)} {V(284)}"))
svg1.append(text(750, 370, "Tidak"))
svg1.append(rect(750, 238, 140, 46, "Tampil Pesan Error"))
svg1.append(path(f"{M(750,261)} {H(350)}")) # goes left back to A2

svg1.append(path(f"{M(655,406)} {V(430)}"))
svg1.append(text(670, 420, "Ya"))
svg1.append(rect(540, 430, 230, 46, "Buat Session Admin", "Redirect Dashboard"))
svg1.append(path(f"{M(540,453)} {H(350)}"))

svg1.append(rect(120, 430, 230, 46, "Akses Dashboard", "Pilih Menu Manajemen"))
svg1.append(path(f"{M(235,476)} {V(520)}"))

svg1.append(decision(235, 550, "Pilih", "Modul?"))
# Produk
svg1.append(path(f"{M(160,550)} {H(60)} {V(600)}"))
svg1.append(text(100, 545, "Produk"))
svg1.append(rect(10, 600, 100, 46, "Input Produk"))
svg1.append(path(f"{M(110,623)} {H(540)}"))
svg1.append(rect(540, 600, 230, 46, "Validasi & Simpan Produk"))
svg1.append(path(f"{M(590,646)} {V(810)}", arrow=False))

# Kategori
svg1.append(path(f"{M(235,580)} {V(680)}"))
svg1.append(text(255, 630, "Kategori"))
svg1.append(rect(120, 680, 230, 46, "Input Kategori"))
svg1.append(path(f"{M(350,703)} {H(540)}"))
svg1.append(rect(540, 680, 230, 46, "Validasi & Simpan Kategori"))
svg1.append(path(f"{M(655,726)} {V(810)}", arrow=False))

# User
svg1.append(path(f"{M(310,550)} {H(410)} {V(760)}"))
svg1.append(text(360, 545, "User"))
svg1.append(rect(300, 760, 220, 46, "Input Data User"))
svg1.append(path(f"{M(520,783)} {H(540)}"))
svg1.append(rect(540, 760, 230, 46, "Validasi & Simpan User"))
svg1.append(path(f"{M(720,806)} {V(810)}", arrow=False))

# Sync bar
svg1.append(sync_bar(540, 810, 230))
svg1.append(path(f"{M(655,816)} {V(848)}"))

svg1.append(rect(540, 848, 230, 46, "Tampil Notifikasi Berhasil"))
svg1.append(path(f"{M(540,871)} {H(350)}"))

svg1.append(rect(120, 848, 230, 46, "Terima Notifikasi Sukses"))
svg1.append(path(f"{M(235,894)} {V(940)}"))

svg1.append(decision(235, 970, "Lihat", "Laporan?"))
# Ya
svg1.append(path(f"{M(310,970)} {H(655)} {V(1000)}"))
svg1.append(text(380, 965, "Ya"))
svg1.append(rect(540, 1000, 230, 46, "Filter & Query Data", "Transaksi dari DB"))
svg1.append(path(f"{M(655,1046)} {V(1080)}"))
svg1.append(rect(540, 1080, 230, 46, "Tampil Tabel Laporan", "& Grafik Penjualan"))
svg1.append(path(f"{M(540,1103)} {H(350)}"))
svg1.append(rect(120, 1080, 230, 46, "Lihat & Ekspor", "Laporan (PDF/Print)"))
svg1.append(path(f"{M(235,1126)} {V(1200)}"))

# Tidak
svg1.append(path(f"{M(160,970)} {H(60)} {V(1160)} {H(235)} {V(1200)}"))
svg1.append(text(100, 965, "Tidak"))

svg1.append(decision(235, 1230, "Logout?"))
# Tidak
svg1.append(path(f"{M(160,1230)} {H(30)} {V(510)} {H(235)} {V(520)}"))
svg1.append(text(90, 1225, "Tidak"))
# Ya
svg1.append(path(f"{M(235,1260)} {V(1300)}"))
svg1.append(text(250, 1280, "Ya"))

svg1.append(rect(120, 1300, 230, 46, "Klik Tombol Logout", "& Konfirmasi"))
svg1.append(path(f"{M(350,1323)} {H(540)}"))
svg1.append(rect(540, 1300, 230, 46, "Hapus Session", "& Redirect Login"))
svg1.append(path(f"{M(655,1346)} {V(1370)} {H(235)} {V(1400)}"))
svg1.append(rect(120, 1400, 230, 46, "Sesi Berakhir", "Kembali ke Login"))
svg1.append(path(f"{M(235,1446)} {V(1490)}"))

# End
svg1.append('<circle cx="235" cy="1506" r="16" fill="#fff" stroke="#f00" stroke-width="2"/>')
svg1.append('<circle cx="235" cy="1506" r="10" fill="#000"/>')

# ================= Diagram 2 =================
svg2 = []
svg2.append('<rect x="0" y="0" width="470" height="40" fill="#fff" stroke="#000" stroke-width="1"/>')
svg2.append('<rect x="470" y="0" width="470" height="40" fill="#fff" stroke="#000" stroke-width="1"/>')
svg2.append('<text x="235" y="25" text-anchor="middle" font-weight="bold">User (Kasir)</text>')
svg2.append('<text x="705" y="25" text-anchor="middle" font-weight="bold">Sistem</text>')
svg2.append('<line x1="470" y1="40" x2="470" y2="1820" stroke="#000" stroke-width="1"/>')

svg2.append('<circle cx="235" cy="80" r="12" fill="#000"/>')
svg2.append(path(f"{M(235,92)} {V(148)}", color="#f00"))

svg2.append(rect(120, 148, 230, 46, "Buka Aplikasi POS System"))
svg2.append(path(f"{M(350,171)} {H(540)}"))

svg2.append(rect(540, 148, 230, 46, "Tampilkan Form Login Kasir"))
svg2.append(path(f"{M(655,194)} {V(233)} {H(350)}"))

svg2.append(rect(120, 210, 230, 46, "Input Username & Password"))
svg2.append(path(f"{M(235,256)} {V(287)} {H(540)}"))

svg2.append(rect(540, 264, 230, 46, "Validasi Kredensial (Role Kasir)"))
svg2.append(path(f"{M(655,310)} {V(346)}"))

svg2.append(decision(655, 376, "Valid?"))
# NO
svg2.append(path(f"{M(730,376)} {H(820)} {V(284)}"))
svg2.append(text(750, 370, "Tidak"))
svg2.append(rect(750, 238, 140, 46, "Tampil Pesan Error"))
svg2.append(path(f"{M(750,261)} {H(350)}")) # left to A2

# YES
svg2.append(path(f"{M(655,406)} {V(430)}"))
svg2.append(text(670, 420, "Ya"))
svg2.append(rect(540, 430, 230, 46, "Buat Session Kasir", "Tampilkan Halaman POS"))
svg2.append(path(f"{M(540,453)} {H(350)}"))

svg2.append(rect(120, 430, 230, 46, "Akses Halaman", "Kasir / POS Interface"))
svg2.append(path(f"{M(235,476)} {V(520)}"))

# LINEAR SEARCH Box label
svg2.append(rect(10, 520, 460, 22, "PROSES LINEAR SEARCH"))

svg2.append(rect(120, 560, 230, 46, "Input Kata Kunci", "Nama / Kode Produk"))
svg2.append(path(f"{M(350,583)} {H(540)}"))

svg2.append(rect(540, 560, 390, 60, "Algoritma Linear Search", "Bandingkan keyword dg array produk"))
svg2.append(path(f"{M(655,620)} {V(660)}"))

svg2.append(decision(655, 690, "Produk", "Ditemukan?"))
# YA Found
svg2.append(path(f"{M(730,690)} {H(820)} {V(740)}"))
svg2.append(text(760, 685, "Ya"))
svg2.append(rect(720, 740, 200, 46, "Tampil Hasil Produk", "Nama, Harga, Stok"))
svg2.append(path(f"{M(720,763)} {H(350)}"))
# TIDAK Found
svg2.append(path(f"{M(580,690)} {H(470)}"))
svg2.append(text(520, 685, "Tidak"))
svg2.append(rect(290, 667, 180, 46, "Tampil: Produk", "Tidak Ditemukan"))
svg2.append(path(f"{M(290,690)} {H(235)} {V(606)}"))
svg2.append(text(270, 685, "Cari ulang"))

svg2.append(rect(120, 740, 230, 46, "Klik Produk", "Tambah ke Keranjang"))
svg2.append(path(f"{M(235,786)} {V(862)} {H(540)}"))

svg2.append(rect(480, 810, 452, 22, "KERANJANG BELANJA"))

svg2.append(rect(540, 840, 230, 46, "Tambah ke Keranjang", "Cek Stok & Hitung Subtotal"))
svg2.append(path(f"{M(540,863)} {H(350)}"))

svg2.append(decision(235, 893, "Tambah", "Produk Lain?"))
# YA Tambah
svg2.append(path(f"{M(160,893)} {H(60)} {V(540)} {H(235)} {V(560)}"))
svg2.append(text(100, 888, "Ya"))
# TIDAK Tambah
svg2.append(path(f"{M(310,893)} {H(655)} {V(940)}"))
svg2.append(text(410, 888, "Tidak"))

svg2.append(rect(540, 940, 230, 46, "Tampil Ringkasan", "Total & Item Keranjang"))
svg2.append(path(f"{M(540,963)} {H(350)}"))

svg2.append(rect(10, 1020, 460, 22, "PROSES PEMBAYARAN"))

svg2.append(rect(120, 940, 230, 46, "Pilih Metode Bayar", "Cash / QRIS"))
svg2.append(path(f"{M(235,986)} {V(1070)}"))

svg2.append(decision(235, 1100, "Metode", "Bayar?"))
# Cash
svg2.append(path(f"{M(160,1100)} {H(60)} {V(1160)}"))
svg2.append(text(110, 1095, "Cash"))
svg2.append(rect(10, 1160, 100, 46, "Input", "Uang Bayar"))
svg2.append(path(f"{M(110,1183)} {H(655)} {V(1240)}"))
# QRIS
svg2.append(path(f"{M(310,1100)} {H(420)} {V(1160)}"))
svg2.append(text(360, 1095, "QRIS"))
svg2.append(rect(360, 1160, 100, 46, "Scan QR", "Code"))
svg2.append(path(f"{M(460,1183)} {H(720)} {V(1240)}"))

# Sync bar
svg2.append(sync_bar(540, 1240, 370))
svg2.append(path(f"{M(655,1246)} {V(1280)}"))

svg2.append(rect(540, 1280, 230, 46, "Hitung Total", "& Kembalian"))
svg2.append(path(f"{M(655,1326)} {V(1380)}"))

svg2.append(decision(655, 1410, "Bayar", "Cukup?"))
# TIDAK Cukup
svg2.append(path(f"{M(730,1410)} {H(820)} {V(1334)}"))
svg2.append(text(770, 1405, "Tidak"))
svg2.append(rect(750, 1288, 140, 46, "Tampil Uang Kurang"))
svg2.append(path(f"{M(750,1311)} {H(60)} {V(1206)}"))

# YA Cukup
svg2.append(path(f"{M(655,1440)} {V(1480)}"))
svg2.append(text(670, 1460, "Ya"))

svg2.append(rect(540, 1480, 230, 46, "Simpan Transaksi", "& Update Stok Produk"))
svg2.append(path(f"{M(655,1526)} {V(1560)}"))

svg2.append(rect(540, 1560, 230, 46, "Generate Struk", "& Tampil Modal Sukses"))
svg2.append(path(f"{M(540,1583)} {H(350)}"))

svg2.append(rect(120, 1560, 230, 46, "Cetak Struk", "/ Transaksi Baru"))
svg2.append(path(f"{M(235,1606)} {V(1650)}"))

svg2.append(decision(235, 1680, "Transaksi", "Baru?"))
# YA
svg2.append(path(f"{M(160,1680)} {H(30)} {V(450)} {H(235)} {V(476)}"))
svg2.append(text(100, 1675, "Ya"))
# TIDAK
svg2.append(path(f"{M(310,1680)} {H(655)} {V(1710)}"))
svg2.append(text(410, 1675, "Tidak"))

svg2.append(rect(540, 1710, 230, 46, "Hapus Session", "& Redirect Login"))
svg2.append(path(f"{M(540,1733)} {H(350)}"))

svg2.append(rect(120, 1710, 230, 46, "Sesi Berakhir", "Kembali ke Halaman Login"))
svg2.append(path(f"{M(235,1756)} {V(1790)}"))

# END
svg2.append('<circle cx="235" cy="1806" r="16" fill="#fff" stroke="#f00" stroke-width="2"/>')
svg2.append('<circle cx="235" cy="1806" r="10" fill="#000"/>')

html = f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Activity Diagram Sistem Usulan</title>
<style>
body {{ font-family: Arial, sans-serif; background: #fff; padding: 20px; color: #000; }}
.diagram-container {{ max-width: 940px; margin: 0 auto 40px; border: 1px solid #000; position: relative; }}
svg {{ width: 100%; height: auto; display: block; }}
h2 {{ text-align: center; margin-bottom: 20px; font-size: 18px; font-weight: bold; }}
text {{ font-family: Arial, sans-serif; font-size: 13px; fill: #000; }}
</style>
</head>
<body>

<h2>Diagram 1: Admin &amp; Sistem</h2>
<div class="diagram-container">
<svg viewBox="0 0 940 1550" xmlns="http://www.w3.org/2000/svg">
<defs>
  <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3.5" orient="auto"><polygon points="0 0,10 3.5,0 7" fill="#000"/></marker>
  <marker id="arrow-red" markerWidth="10" markerHeight="10" refX="9" refY="3.5" orient="auto"><polygon points="0 0,10 3.5,0 7" fill="#f00"/></marker>
</defs>
{chr(10).join(svg1)}
</svg>
</div>

<h2>Diagram 2: Kasir &amp; Sistem</h2>
<div class="diagram-container">
<svg viewBox="0 0 940 1850" xmlns="http://www.w3.org/2000/svg">
<defs>
  <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3.5" orient="auto"><polygon points="0 0,10 3.5,0 7" fill="#000"/></marker>
  <marker id="arrow-red" markerWidth="10" markerHeight="10" refX="9" refY="3.5" orient="auto"><polygon points="0 0,10 3.5,0 7" fill="#f00"/></marker>
</defs>
{chr(10).join(svg2)}
</svg>
</div>
</body>
</html>
"""

with open(r'c:\laragon\www\SkripsiFibril\pos_system\activity_diagram_2_sistem_usulan.html', 'w', encoding='utf-8') as f:
    f.write(html)
with open(r'c:\laragon\www\SkripsiFibril\pos_system\activity_diagram_sistem_usulan.html', 'w', encoding='utf-8') as f:
    f.write(html)
