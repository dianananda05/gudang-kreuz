<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPengeluaran;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPengeluaran;
use App\Models\ModelBarang;
use TCPDF;

class Pengeluaran extends BaseController
{
    public function __construct()
    {
        $this->ModelPengeluaran = new ModelPengeluaran();
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPengeluaran = new ModelDetailPengeluaran();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();

        $pengeluaran = $this->ModelPengeluaran->findAll();
        $detail_pengeluaran = [];

        foreach ($pengeluaran as $out) {
            $detail_pengeluaran[$out['kode_pengeluaran']] = $this->ModelDetailPengeluaran->AllData($out['kode_pengeluaran']);
        }

        $data = [
            'judul' => 'Pengeluaran Barang',
            'subjudul' => 'Pengeluaran',
            'menu' => 'masterdata',
            'submenu' => 'pengeluaran',
            'page' => 'v_pengeluaran',
            'pengeluaran' => $pengeluaran,
            'permintaan' => $this->ModelPermintaan->AllData(),
            'detail_pengeluaran' => $detail_pengeluaran,
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
        ];
        return view('pengeluaran/get', $data);
    }

    public function tambahdata()
    {
        $permintaan = $this->ModelPermintaan->like('kode_permintaan', 'PRO%', 'after')->findAll();

        $data = [
            'judul' => 'Pengeluaran Barang',
            'subjudul' => 'Pengeluaran',
            'menu' => 'masterdata',
            'submenu' => 'pengeluaran',
            'page' => 'pengeluaran',
            'pengeluaran' => $this->ModelPengeluaran->AllData(),
            'detail_pengeluaran' => $this->ModelDetailPengeluaran->AllData(),
            'permintaan' => $permintaan,
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('pengeluaran/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_pengeluaran' => $this->request->getPost('kode_pengeluaran'),
            'kode_permintaan' => $this->request->getPost('kode_permintaan'),
            'tanggal_pengeluaran' => $this->request->getPost('tanggal_pengeluaran')
        ];
        $this->ModelPengeluaran->Tambah($data);

        // Insert data detail pengeluaran
        $kode_pengeluaran = $this->request->getPost('kode_pengeluaran');
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_yang_diserahkan = $this->request->getPost('jumlah_yang_diserahkan');
        $keterangan = $this->request->getPost('keterangan');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_pengeluaran' => $kode_pengeluaran,
                'kode_barang' => $kode_barang[$i],
                'jumlah_yang_diserahkan' => $jumlah_yang_diserahkan[$i],
                'keterangan' => $keterangan[$i],
            ];
            $this->ModelDetailPengeluaran->Tambah($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Pengeluaran');
    }

    public function hapusdata($kode_pengeluaran)
    {
        $data = [
            'kode_pengeluaran' => $kode_pengeluaran,
        ];
        $this->ModelPengeluaran->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Pengeluaran');
    }

    public function filter()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['pengeluaran'] = $this->ModelPengeluaran->filterByDate($start_date, $end_date);

        foreach ($data['pengeluaran'] as $pengeluaran) {
            $kode_pengeluaran = $pengeluaran['kode_pengeluaran'];
            $data['detail_pengeluaran'][$kode_pengeluaran] = $this->ModelPengeluaran->getDetailPengeluaran($kode_pengeluaran);
        }

        return view('pengeluaran/get', $data);
    }

    public function cetakLaporan()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $pengeluaran = $this->ModelPengeluaran->getLaporan($start_date, $end_date);
        $detail_pengeluaran = [];

        foreach ($pengeluaran as $out) {
            $detail_pengeluaran[$out['kode_pengeluaran']] = $this->ModelDetailPengeluaran->AllData($out['kode_pengeluaran']);
        }

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Konten laporan
        $html = '<h1>Laporan Pengeluaran Barang</h1>';
        $html .= '<p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p>';

        foreach ($pengeluaran as $out) {
            $html .= '<table border="1" cellspacing="0" cellpadding="8">';
            $html .= '<tr>
                        <th>Kode Permintaan</th>
                        <th>Kode Pengeluaran</th>
                        <th>Tanggal Pengeluaran</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jumlah Yang Diserahkan</th>
                        <th>Keterangan</th>
                    </tr>';

            $totalBarang = 0;

            foreach ($detail_pengeluaran[$out['kode_pengeluaran']] as $index => $detail) {
                $html .= '<tr>';

                if ($index === 0) { 
                    $html .= '<td rowspan="' . count($detail_pengeluaran[$out['kode_pengeluaran']]) . '">' . $out['kode_permintaan'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_pengeluaran[$out['kode_pengeluaran']]) . '">' . $out['kode_pengeluaran'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_pengeluaran[$out['kode_pengeluaran']]) . '">' . date('d-m-Y', strtotime($out['tanggal_pengeluaran'])) . '</td>';
                }

                // Kolom untuk detail barang
                $html .= '<td>' . $detail['kode_barang'] . '</td>';
                $html .= '<td>' . $detail['nama_barang'] . '</td>';
                $html .= '<td>' . $detail['satuan'] . '</td>';
                $html .= '<td>' . $detail['jumlah_yang_diserahkan'] . '</td>';
                $html .= '<td>' . $detail['keterangan'] . '</td>';
                $html .= '</tr>';

                // Akumulasi total barang
                $totalBarang += $detail['jumlah_yang_diserahkan'];
            }

            $html .= '<tr>';
            $html .= '<td colspan="6" align="right"><strong>Total Barang :</strong></td>';
            $html .= '<td><strong>' . $totalBarang . '</strong></td>';
            $html .= '</tr>';

            $html .= '</table>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Pengeluaran_Barang.pdf', 'D');
    }
}
