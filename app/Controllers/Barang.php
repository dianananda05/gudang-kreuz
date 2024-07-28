<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelBarang;
use App\Models\ModelDetailPenerimaan;
use App\Models\ModelDetailPengeluaran;
use App\Models\ModelDetailPenukaran;
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
        $this->ModelDetailPenukaran = new ModelDetailPenukaran();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    protected function isKepalaPembelian()
    {
        return session()->get('level') === 'kepalapembelian';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaPembelian = $this->isKepalaPembelian();
        
        $barang = $this->ModelBarang->findAll();
        $stok_barang = [];

        foreach ($barang as $item) {
            $barang_masuk = $this->ModelDetailPenerimaan->where('kode_barang', $item['kode_barang'])->findAll();
            $barang_keluar = $this->ModelDetailPengeluaran->where('kode_barang', $item['kode_barang'])->findAll();

            $total_masuk = array_sum(array_column($barang_masuk, 'jumlah_yang_diterima'));
            $total_keluar = array_sum(array_column($barang_keluar, 'jumlah_yang_diserahkan'));

            $repairData = $this->ModelDetailPenukaran->getRepairCount($item['kode_barang']);
            $rejectData = $this->ModelDetailPenukaran->getRejectCount($item['kode_barang']);

            $repair = $repairData['total_repair'] ?? 0;
            $reject = $rejectData['total_reject'] ?? 0;

            $item['total_masuk'] = $total_masuk;
            $item['total_keluar'] = $total_keluar;
            $item['stok_tersisa'] = $item['stok'] + $total_masuk - $total_keluar - $repair - $reject;
            $item['repair'] = $repair;
            $item['reject'] = $reject;

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
            'isKepalaPembelian' => $isKepalaPembelian,
        ];
        return view('barang/get', $data);
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
        // Ambil data barang berdasarkan kode barang
        $barang = $this->ModelBarang->where('kode_barang', $kode_barang)->first();

        // Jika barang tidak ditemukan, lemparkan PageNotFoundException
        if (!$barang) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Barang tidak ditemukan');
        }

        // Format QR Code content sesuai yang diinginkan
        $qrCodeContent = $barang['kode_barang'] . ' - ' . $barang['nama_barang'];

        // Instansiasi QR Code dengan konten yang sudah di-generate
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

        // Tampilkan view dengan data QR Code yang disimpan
        $data = [
            'publicQrCodePath' => base_url($publicQrCodePath),
            'kode_barang' => $barang['kode_barang'],
            'nama_barang' => $barang['nama_barang']
        ];

        return $this->response->setJSON($data); // Mengembalikan data JSON berisi path QR Code dan data barang
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
        $kodeBarang = $this->generateKodeBarang();
    
        $data = [
            'kode_barang' => $kodeBarang
        ];

        return view('barang/tambah', $data);
    }

    public function generateKodeBarang()
    {
        $nama_barang = 'BRG';
        $prefix = strtoupper(substr($nama_barang, 0, 3));
        
        // Dapatkan kode barang terbesar berdasarkan prefix
        $kodeTerbesar = $this->ModelBarang->getMaxKodeBarangByPrefix($prefix);
        
        // Ambil angka dari kode barang terbesar
        if ($kodeTerbesar) {
            $urutan = (int) substr($kodeTerbesar, 3, 3);
        } else {
            $urutan = 0;
        }
        
        // Tambahkan 1 untuk kode barang baru
        $urutan++;
        return $prefix . sprintf("%03s", $urutan);
    }
    
    public function insertdata() 
    {
        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan' => $this->request->getPost('satuan'),
            'stok' => $this->request->getPost('stok'),
            'harga_satuan' => $this->request->getPost('harga_satuan')
        ];
        $this->ModelBarang->Tambah($data);
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Barang');
    }

    public function edit($kode_barang = null)
    {
        $isKepalaPembelian = $this->isKepalaPembelian();
        if ($kode_barang != null) {
            $barang = $this->ModelBarang->where('kode_barang', $kode_barang)->first();

            if ($barang) {
                $jsonBarang = json_encode($barang);

                $data = [
                    'judul' => 'Edit Barang',
                    'barang' => $jsonBarang,
                    'isKepalaPembelian' => $isKepalaPembelian,
                ];

                return view('barang/edit', $data);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function ubahdata($kode_barang)
    {
        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'satuan' => $this->request->getPost('satuan'),
            'stok' => $this->request->getPost('stok'),
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
        // Set zona waktu menjadi Waktu Indonesia Barat (WIB)
        date_default_timezone_set('Asia/Jakarta');
    
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
    
        // Path logo menggunakan FCPATH
        $logoPath = FCPATH . 'template/assets/img/kreuz.png';
    
        $namaPerusahaan = 'PT. Kreuz Bike Indonesia';
        $alamatPerusahaan = 'Jl. Rereng Adumanis No.47, Sukaluyu, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40123';
        $teleponPerusahaan = '+62 819-1500-2786';
        $emailPerusahaan = 'Kreuzbikeindonesia@gmail.com';
        $igPerusahaan = 'kreuzbikeid';
    
        $kopSurat = '
            <div style="margin-bottom: 20px;">
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; text-align: left; vertical-align: top; border-bottom: 1px solid #000;">
                            <img src="' . $logoPath . '" style="width: 100px; height: auto; vertical-align: middle;"/>
                        </td>
                        <td style="width: 80%; text-align: center; vertical-align: top; border-bottom: 1px solid #000; padding-left: 10px;">
                            <h2 style="margin: 0; font-size: 18px; font-weight: bold; line-height: 1;">' . $namaPerusahaan . '</h2>
                            <p style="font-size: 12px; margin-top: 5px; line-height: 1.4;">' . $alamatPerusahaan . '<br>
                                Telp: ' . $teleponPerusahaan . '<br>
                                Email: ' . $emailPerusahaan . '<br>
                                Instagram: ' . $igPerusahaan . '
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center; font-size: 14px; font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center; font-size: 14px; font-weight: bold;">
                            Laporan Stok Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';
    
        // Contoh template laporan
        $html = '<table border="1" cellspacing="0" cellpadding="8" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 8px; text-align: left;">Kode Barang</th>
                            <th style="padding: 8px; text-align: left;">Nama Barang</th>
                            <th style="padding: 8px; text-align: left;">Satuan</th>
                            <th style="padding: 8px; text-align: right;">Stok Tersisa</th>
                        </tr>
                    </thead>
                    <tbody>';
    
        $totalStok = 0; // Inisialisasi variabel totalStok sebelum perulangan foreach
    
        foreach ($barang as $item) {
            $html .= '<tr>';
            $html .= '<td style="padding: 8px; text-align: left;">' . $item['kode_barang'] . '</td>';
            $html .= '<td style="padding: 8px; text-align: left;">' . $item['nama_barang'] . '</td>';
            $html .= '<td style="padding: 8px; text-align: left;">' . $item['satuan'] . '</td>';
            $html .= '<td style="padding: 8px; text-align: right;">' . $item['stok'] . '</td>';
            $html .= '</tr>';
            $totalStok += $item['stok'];
        }
    
        // Tambahkan baris total stok
        $html .= '<tr style="font-weight:bold; background-color: #f2f2f2;">
                    <td colspan="3" style="padding: 8px; text-align:right;">Total Stok:</td>
                    <td style="padding: 8px; text-align: right;">' . $totalStok . '</td>
                </tr>';
    
        $html .= '</tbody></table>';
    
        $html .= '<div style="margin-top: 20px;">
                    <table width="100%">
                        <tr>
                            <td style="width: 50%;"></td>
                            <td style="text-align: center;">
                                <p style="margin-bottom: 5px;">Kepala Gudang</p>
                                <p style="font-weight: bold; margin-bottom: 10px;"></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%;"></td>
                            <td style="text-align: center;">
                                <p style="margin-bottom: 5px;"></p>
                                <p style="font-weight: bold; margin-bottom: 10px;">Budi Setiawan</p>
                            </td>
                        </tr>
                    </table>
                </div>';
    
        $content = $kopSurat . $html;
    
        // Tulis konten HTML ke PDF
        $pdf->writeHTML($content, true, false, true, false, '');
    
        // Output PDF ke browser atau menyimpannya ke file
        $pdf->Output('Laporan_Stok_Barang.pdf', 'I'); // 'I' untuk menampilkan di browser, 'F' untuk menyimpan ke file
    
        exit;
    }    

}
