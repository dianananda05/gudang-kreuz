<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPermintaan;
use App\Models\ModelBarang;
use TCPDF;

class Permintaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPermintaan = new ModelDetailPermintaan();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    protected function isKepalaProduksi()
    {
        return session()->get('level') === 'kepalaproduksi';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
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
        $data = [
            'status' => 0
        ];

        $this->ModelPermintaan->updateStatus($kode_permintaan, $data);
        session()->setFlashdata('pesan', 'Permintaan berhasil ditolak.');
        return redirect()->to('Permintaan');
    }

    public function tambahdata()
    {
        $isKepalaProduksi = $this->isKepalaProduksi();
        $data = [
            'judul' => 'Permintaan Barang',
            'subjudul' => 'Permintaan',
            'menu' => 'masterdata',
            'submenu' => 'permintaan',
            'page' => 'permintaan',
            'permintaan' => $this->ModelPermintaan->AllData(),
            'detail_permintaan' => $this->ModelDetailPermintaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
            'isKepalaProduksi' => $isKepalaProduksi,
        ];
        return view('permintaan/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_permintaan' => $this->request->getPost('kode_permintaan'),
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

    public function hapusdata($kode_permintaan)
    {
        $data = [
            'kode_permintaan' => $kode_permintaan,
        ];

        $this->ModelPermintaan->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Permintaan');
    }

    public function laporan()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $permintaan = $this->ModelPermintaan->getLaporan($start_date, $end_date);
        $detail_permintaan = [];

        foreach ($permintaan as $perm) {
            $detail_permintaan[$perm['kode_permintaan']] = $this->ModelDetailPermintaan->AllData($perm['kode_permintaan']);
        }

        $data = [
            'judul' => 'Laporan Permintaan Barang',
            'subjudul' => 'Laporan',
            'menu' => 'laporan',
            'submenu' => 'laporan_permintaan',
            'page' => 'laporan_permintaan',
            'permintaan' => $permintaan,
            'detail_permintaan' => $detail_permintaan,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        return view('permintaan/laporan', $data);
    }

    public function filter()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['permintaan'] = $this->ModelPermintaan->filterByDate($start_date, $end_date);

        foreach ($data['permintaan'] as $permintaan) {
            $kode_permintaan = $permintaan['kode_permintaan'];
            $data['detail_permintaan'][$kode_permintaan] = $this->ModelPermintaan->getDetailPermintaan($kode_permintaan);
        }

        return view('permintaan/get', $data);
    }

    // Method untuk mencetak laporan ke PDF
    public function cetakLaporan()
    {
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

        // Konten laporan
        $html = '<h1>Laporan Permintaan Barang</h1>';
        $html .= '<p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p>';

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

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Permintaan_Barang.pdf', 'D');
    }
    
}
