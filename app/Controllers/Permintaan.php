<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPermintaan;
use App\Models\ModelBarang;
use App\Models\ModelPengadaan;
use App\Models\ModelPengeluaran;
use TCPDF;

class Permintaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPermintaan = new ModelDetailPermintaan();
        $this->ModelBarang = new ModelBarang();
        $this->ModelPengadaan = new ModelPengadaan();
        $this->ModelPengeluaran = new ModelPengeluaran();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    protected function isKepalaGudang()
    {
        return session()->get('level') === 'kepalagudang';
    }

    protected function isKepalaProduksi()
    {
        return session()->get('level') === 'kepalaproduksi';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaGudang = $this->isKepalaGudang();
        $isKepalaProduksi = $this->isKepalaProduksi();

        $permintaan = $this->ModelPermintaan->findAll();
        $detail_permintaan = [];

        foreach ($permintaan as $perm) {
            $detail_permintaan[$perm['kode_permintaan']] = $this->ModelDetailPermintaan->AllData($perm['kode_permintaan']);
        }

        $data = [
            'judul' => 'Permintaan Barang',
            'subjudul' => 'Permintaan',
            'menu' => 'masterdata',
            'submenu' => 'permintaan',
            'page' => 'permintaan',
            'permintaan' => $permintaan,
            'detail_permintaan' => $detail_permintaan,
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
            'isKepalaGudang' => $isKepalaGudang,
            'isKepalaProduksi' => $isKepalaProduksi,
        ];
        return view('permintaan/get', $data);
    }

    public function getNewPermintaan() {
        $newPermintaan = $this->ModelPermintaan->getNewPermintaan();
        return $this->response->setJSON($newPermintaan);
    }

    public function approve($kode_permintaan)
    {
        $data = [
            'status' => 1
        ];

        $this->ModelPermintaan->updateStatus($kode_permintaan, $data);
        session()->setFlashdata('pesan', 'Permintaan berhasil disetujui.');
        return redirect()->to('Permintaan');
    }

    public function reject($kode_permintaan)
    {
        $catatan = $this->request->getPost('catatan');

        $data = [
            'status' => 0,
            'catatan' => $catatan,
        ];

        $this->ModelPermintaan->updateStatus($kode_permintaan, $data);
        session()->setFlashdata('pesan', 'Permintaan berhasil ditolak.');
        return redirect()->to('Permintaan');
    }

    public function generateKodePermintaan($type_permintaan)
    {
        $modelPermintaan = new ModelPermintaan(); // Ganti ModelPermintaan dengan nama model yang sesuai
        $newKode = $modelPermintaan->generateKodePermintaan($type_permintaan);

        if ($newKode) {
            return $this->response->setJSON(['newKode' => $newKode]);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Failed to generate kode permintaan.']);
        }
    }

    public function tambahdata()
    {
        $modelpermintaan = new ModelPermintaan();

        $type_permintaan = $this->request->getPost('type_permintaan') ?? 'PRODUKSI';

        $newKode = $modelpermintaan->generateKodePermintaan($type_permintaan);

        $data = [
            'judul' => 'Permintaan Barang',
            'subjudul' => 'Permintaan',
            'menu' => 'masterdata',
            'submenu' => 'permintaan',
            'page' => 'permintaan',
            'permintaan' => $this->ModelPermintaan->AllData(),
            'newKode' => $newKode,
            'modelpermintaan' => $modelpermintaan,
            'detail_permintaan' => $this->ModelDetailPermintaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('permintaan/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_permintaan' => $this->ModelPermintaan->generateKodePermintaan($this->request->getPost('type_permintaan')),
            'nama_pengaju' => $this->request->getPost('nama_pengaju'),
            'tanggal_permintaan' => $this->request->getPost('tanggal_permintaan'),
            'type_permintaan' => $this->request->getPost('type_permintaan')
        ];
        $this->ModelPermintaan->Tambah($data);

        // Insert data detail permintaan
        $kode_permintaan = $this->request->getPost('kode_permintaan');
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_yang_diminta = $this->request->getPost('jumlah_yang_diminta');
        $keterangan = $this->request->getPost('keterangan');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_permintaan' => $kode_permintaan,
                'kode_barang' => $kode_barang[$i],
                'jumlah_yang_diminta' => $jumlah_yang_diminta[$i],
                'keterangan' => $keterangan[$i],
            ];
            $this->ModelDetailPermintaan->Tambah($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Permintaan');
    }

    public function edit($kode_permintaan)
    {
        $permintaan = $this->ModelPermintaan->getPermintaan($kode_permintaan);
        $detail_permintaan = $this->ModelDetailPermintaan->AllData($kode_permintaan);

        $data = [
            'judul' => 'Permintaan Barang',
            'subjudul' => 'Permintaan',
            'menu' => 'masterdata',
            'submenu' => 'permintaan',
            'page' => 'permintaan',
            'permintaan' => $permintaan,
            'detail_permintaan' => $detail_permintaan,
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('permintaan/edit', $data);
    }

    public function ubahdata($kode_permintaan)
    {
        $data = [
            'kode_permintaan' => $kode_permintaan,
            'nama_pengaju' => $this->request->getPost('nama_pengaju'),
            'tanggal_permintaan' => $this->request->getPost('tanggal_permintaan'),
            'type_permintaan' => $this->request->getPost('type_permintaan')
        ];

        $statusFromForm = $this->request->getPost('status');
        if ($statusFromForm !== null) {
            $data['status'] = $statusFromForm;
    
            // Periksa status yang sedang diedit
            if ($data['status'] == 0) { // Jika status adalah ditolak
                $data['status'] = null; // Atur ulang status menjadi menunggu konfirmasi
            }
        }

        $this->ModelPermintaan->ubahdata($data);

        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_yang_diminta = $this->request->getPost('jumlah_yang_diminta');
        $keterangan = $this->request->getPost('keterangan');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_permintaan' => $kode_permintaan,
                'kode_barang' => $kode_barang[$i],
                'jumlah_yang_diminta' => $jumlah_yang_diminta[$i],
                'keterangan' => $keterangan[$i],
            ];
            $this->ModelDetailPermintaan->ubahdata($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Diupdate');
        return redirect()->to('Permintaan');
    }

    public function hapusdata($kode_permintaan)
    {
        // Pastikan status permintaan adalah null (belum dikonfirmasi)
        $permintaan = $this->ModelPermintaan->getPermintaan($kode_permintaan);

        if ($permintaan['status'] == 1) {
            return redirect()->to('Permintaan')->with('error', 'Permintaan tidak dapat dihapus karena sudah disetujui.');
        }

        // Hapus data permintaan
        $this->ModelPermintaan->hapusdata($kode_permintaan);

        // Redirect dengan pesan sukses
        return redirect()->to('Permintaan')->with('pesan', 'Permintaan berhasil dihapus.');
    }

    public function filter()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaProduksi = $this->isKepalaProduksi(); 
        $isKepalaGudang = $this->isKepalaGudang();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['permintaan'] = $this->ModelPermintaan->filterByDate($start_date, $end_date);
        $data['isAdmin'] = $isAdmin;
        $data['isKepalaProduksi'] = $isKepalaProduksi;
        $data['isKepalaGudang'] = $isKepalaGudang;
        foreach ($data['permintaan'] as $permintaan) {
            $kode_permintaan = $permintaan['kode_permintaan'];
            $data['detail_permintaan'][$kode_permintaan] = $this->ModelPermintaan->getDetailPermintaan($kode_permintaan);
        }

        return view('permintaan/get', $data);
    }

    // Method untuk mencetak laporan ke PDF
    public function cetakLaporan()
    {
        date_default_timezone_set('Asia/Jakarta');

        $isAdmin = $this->isAdmin();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $permintaan = $this->ModelPermintaan->getLaporan($start_date, $end_date);
        $detail_permintaan = [];

        foreach ($permintaan as $perm) {
            $detail_permintaan[$perm['kode_permintaan']] = $this->ModelDetailPermintaan->AllData($perm['kode_permintaan']);
        }

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 7);
        $pdf->setPrintHeader(false); // Jangan tampilkan header
        $pdf->setPrintFooter(false); // Jangan tampilkan footer

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
                            Laporan Permintaan Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';
        // Konten laporan
        $html = '<strong><p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p></strong>';

        $html .= '<table border="1" cellspacing="0" cellpadding="8">';
        $html .= '<tr>
                    <th>Kode Permintaan</th>
                    <th>Nama Pengaju</th>
                    <th>Tanggal Permintaan</th>
                    <th>Type Permintaan</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jumlah yang Diminta</th>
                    <th>Keterangan</th>
                </tr>';

        foreach ($permintaan as $perm) {
            $totalBarang = 0;
            $firstRow = true; // Untuk menandai baris pertama dari setiap permintaan

            foreach ($detail_permintaan[$perm['kode_permintaan']] as $detail) {
                $html .= '<tr>';

                if ($firstRow) {
                    // Baris pertama dari setiap permintaan
                    $html .= '<td rowspan="' . count($detail_permintaan[$perm['kode_permintaan']]) . '">' . $perm['kode_permintaan'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_permintaan[$perm['kode_permintaan']]) . '">' . $perm['nama_pengaju'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_permintaan[$perm['kode_permintaan']]) . '">' . date('d-m-Y', strtotime($perm['tanggal_permintaan'])) . '</td>';
                    $html .= '<td rowspan="' . count($detail_permintaan[$perm['kode_permintaan']]) . '">' . $perm['type_permintaan'] . '</td>';
                    $firstRow = false; // Setel ke false setelah baris pertama ditampilkan
                }

                // Kolom untuk detail barang
                $html .= '<td>' . $detail['kode_barang'] . '</td>';
                $html .= '<td>' . $detail['nama_barang'] . '</td>';
                $html .= '<td>' . $detail['satuan'] . '</td>';
                $html .= '<td>' . $detail['jumlah_yang_diminta'] . '</td>';
                $html .= '<td>' . $detail['keterangan'] . '</td>';
                $html .= '</tr>';

                // Hitung total barang
                $totalBarang += $detail['jumlah_yang_diminta'];
            }

            // Baris total barang
            $html .= '<tr>';
            $html .= '<td colspan="7" align="right"><strong>Total Barang Diminta:</strong></td>';
            $html .= '<td><strong>' . $totalBarang . '</strong></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

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

        $pdf->writeHTML($content, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Permintaan_Barang.pdf', 'D');
    }
    
}
