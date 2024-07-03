<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelBarang;
use App\Models\ModelDetailPenerimaan;
use App\Models\ModelDetailPengeluaran;
use TCPDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use CodeIgniter\API\ResponseTrait;

class Barang extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->ModelBarang = new ModelBarang();
        $this->ModelDetailPenerimaan = new ModelDetailPenerimaan();
        $this->ModelDetailPengeluaran = new ModelDetailPengeluaran();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
        
        $barang = $this->ModelBarang->findAll();
        $stok_barang = [];

        foreach ($barang as $item) {
            $barang_masuk = $this->ModelDetailPenerimaan->where('kode_barang', $item['kode_barang'])->findAll();
            $barang_keluar = $this->ModelDetailPengeluaran->where('kode_barang', $item['kode_barang'])->findAll();

            $total_masuk = array_sum(array_column($barang_masuk, 'jumlah_yang_diterima'));
            $total_keluar = array_sum(array_column($barang_keluar, 'jumlah_yang_diserahkan'));

            $item['total_masuk'] = $total_masuk;
            $item['total_keluar'] = $total_keluar;
            $item['stok_tersisa'] = $item['stok'] + $total_masuk - $total_keluar;

            $stok_minimum = 2;
            $stok_tersisa = $item['stok_tersisa'];

            if ($stok_tersisa <= $stok_minimum) {
                $item['stok_status'] = 'stok_minimum'; // Stok minimum
            } elseif ($stok_tersisa <= 10) {
                $item['stok_status'] = 'stok_mendekati_minimum'; // Mendekati stok minimum
            } else {
                $item['stok_status'] = 'stok_aman'; // Stok aman
            }

            $stok_barang[] = $item;
        }

        $data = [
            'judul' => 'Master Data',
            'subjudul' => 'Barang',
            'menu' => 'barang',
            'submenu' => '',
            'page' => 'barang',
            'barang' => $stok_barang,
            'isAdmin' => $isAdmin,
        ];
        return view('barang/get', $data);
    }

    public function scan()
    {
        if ($this->request->isAJAX()) {
            $kode_barang = $this->request->getVar('kode_barang');

            // Fetch the item from the database
            $item = $this->ModelBarang->where('kode_barang', $kode_barang)->first();

            if ($item) {
                return $this->respond([
                    'success' => true,
                    'item' => $item
                ]);
            } else {
                return $this->respond([
                    'success' => false,
                    'message' => 'Item not found'
                ]);
            }
        }

        return $this->fail('Invalid request');
    }

    public function barangMasuk()
    {
        $data = [
            'judul' => 'Data Barang Masuk',
            'subjudul' => 'Barang Masuk',
            'menu' => 'barang',
            'submenu' => 'barang_masuk',
            'page' => 'barang',
            'detail_penerimaan' => $this->ModelDetailPenerimaan->findAll(),
        ];
        return view('barang/get', $data);
    }

    public function barangKeluar()
    {
        $data = [
            'judul' => 'Data Barang Keluar',
            'subjudul' => 'Barang Keluar',
            'menu' => 'barang',
            'submenu' => 'barang_keluar',
            'page' => 'barang',
            'detail_pengeluaran' => $this->ModelDetailPengeluaran->findAll(),
        ];
        return view('barang/get', $data);
    }

    public function qr_code($kode_barang)
    {
        $barang = $this->ModelBarang->where('kode_barang', $kode_barang)->first();

        if (!$barang) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Barang tidak ditemukan');
        }

        // Generate QR Code
        $qrCodeContent = $barang['kode_barang'];
        $qrCode = new QrCode($qrCodeContent);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Path untuk menyimpan file QR Code di dalam folder 'public'
        $publicQrCodePath = 'uploads/qr-code/item-' . $barang['kode_barang'] . '.png';
        $qrCodePath = WRITEPATH . 'uploads/qr-code/item-' . $barang['kode_barang'] . '.png';

        // Simpan QR Code ke dalam file
        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile($qrCodePath);

        // Verifikasi apakah file benar-benar tersimpan
        if (!file_exists($qrCodePath)) {
            throw new \Exception('Gagal menyimpan QR Code.');
        }

        // Pindahkan file QR Code ke direktori 'public'
        $this->moveToPublic($qrCodePath, $publicQrCodePath);

        // Tampilkan view dengan data QR Code
        $data = [
            'publicQrCodePath' => base_url($publicQrCodePath)
        ];

        return $this->response->setJSON($data); // Mengembalikan data JSON berisi path QR Code
    }

    protected function moveToPublic($sourcePath, $targetPath)
    {
        // Pindahkan file dari sumber ke target
        if (!copy($sourcePath, FCPATH . $targetPath)) {
            throw new \Exception('Gagal memindahkan file ke direktori public.');
        }

        // Hapus file sumber jika perlu
        unlink($sourcePath);
    }

    public function tambahdata()
    {
        return view('barang/tambah');
    }
    
    public function insertdata() 
    {
        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan' => $this->request->getPost('satuan'),
            'stok' => $this->request->getPost('stok'),
            'kategori_barang' => $this->request->getPost('kategori_barang'),
            'harga_satuan' => $this->request->getPost('harga_satuan')
        ];
        $this->ModelBarang->Tambah($data);
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Barang');
    }

    public function edit($kode_barang = null)
    {
        if ($kode_barang != null) {
            $barang = $this->ModelBarang->where('kode_barang', $kode_barang)->first();

            if ($barang) {
                $jsonBarang = json_encode($barang);

                $data = [
                    'judul' => 'Edit Barang',
                    'barang' => $jsonBarang
                ];

                return view('barang/edit', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function ubahdata()
    {
        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan' => $this->request->getPost('satuan'),
            'stok' => $this->request->getPost('stok'),
            'kategori_barang' => $this->request->getPost('kategori_barang'),
            'harga_satuan' => $this->request->getPost('harga_satuan')
        ];
        $this->ModelBarang->ubahdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Diubah');
        return redirect()->to(base_url('Barang'));
    }

    public function hapusdata($kode_barang)
    {
        $data = [
            'kode_barang' => $kode_barang,
        ];
        $this->ModelBarang->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Barang');
    }

    public function cetakLaporan()
    {
        // Ambil data barang dari model (misalnya semua data barang)
        $barang = $this->ModelBarang->AllData();

        // Load TCPDF library
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set dokumen informasi
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nama Anda');
        $pdf->SetTitle('Laporan Stok Barang');
        $pdf->SetSubject('Laporan Stok Barang');
        $pdf->SetKeywords('TCPDF, PDF, laporan, data, barang');

        // Set header dan footer
        $pdf->setPrintHeader(false); // Jangan tampilkan header
        $pdf->setPrintFooter(false); // Jangan tampilkan footer

        // Tambahkan halaman
        $pdf->AddPage();

        $logoPath = base_url('template/assets/img/kreuz.png'); // Ganti dengan path logo perusahaan Anda
        $namaPerusahaan = 'PT. Kreuz Bike Indonesia';
        $alamatPerusahaan = 'Jl. Rereng Adumanis No.47, Sukaluyu, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40123';
        $teleponPerusahaan = '+62 819-1500-2786';
        $emailPerusahaan = 'Kreuzbikeindonesia@gmail.com';
        $igPerusahaan = 'kreuzbikeid';

        $kopSurat = '
        <div style="margin-bottom: 20px;">
            <table width="100%">
                <tr>
                    <td style="text-align:center;">
                        <h2>' . $namaPerusahaan . ' - Laporan Stok Barang</h2>
                        <p style="font-size: 10px;">Alamat: '  . $alamatPerusahaan . '<br>
                        Nomor Telepon: ' . $teleponPerusahaan . '<br>
                        Email: ' . $emailPerusahaan . '<br>
                        Instagram: ' . $igPerusahaan . '
                        </p>
                    </td>
                </tr>
            </table>
        </div>';

        // Contoh template laporan
        $html = '<table border="1" cellspacing="0" cellpadding="8">';
        $html .= '<tr style="font-weight:bold;">
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Stok Tersisa</th>
                    <th>Harga Satuan</th>
                </tr>';

        foreach ($barang as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item['kode_barang'] . '</td>';
            $html .= '<td>' . $item['nama_barang'] . '</td>';
            $html .= '<td>' . $item['satuan'] . '</td>';
            $html .= '<td>' . $item['stok'] . '</td>';
            $html .= '<td>' . 'Rp. ' . number_format($item['harga_satuan'], 2, ',', '.') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $content = $kopSurat . $html;

        // Tulis konten HTML ke PDF
        $pdf->writeHTML($content, true, false, true, false, '');

        // Output PDF ke browser atau menyimpannya ke file
        $pdf->Output('Laporan_Data_Stok_Barang.pdf', 'I'); // 'I' untuk menampilkan di browser, 'F' untuk menyimpan ke file

        exit;
    }

}
