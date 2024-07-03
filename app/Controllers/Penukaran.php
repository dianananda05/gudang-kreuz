<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPenukaran;
use App\Models\ModelPengeluaran;
use App\Models\ModelDetailPenukaran;
use App\Models\ModelBarang;
use TCPDF;

class Penukaran extends BaseController
{
    public function __construct()
    {
        $this->ModelPenukaran = new ModelPenukaran();
        $this->ModelPengeluaran = new ModelPengeluaran();
        $this->ModelDetailPenukaran = new ModelDetailPenukaran();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();

        $penukaran = $this->ModelPenukaran->findAll();
        $detail_penukaran = [];

        foreach ($penukaran as $tkr) {
            $detail_penukaran[$tkr['kode_penukaran']] = $this->ModelDetailPenukaran->AllData($tkr['kode_penukaran']);
        }

        $data = [
            'judul' => 'Penukaran Barang',
            'subjudul' => 'Penukaran',
            'menu' => 'masterdata',
            'submenu' => 'penukaran',
            'page' => 'v_penukaran',
            'penukaran' => $penukaran,
            'pengeluaran' => $this->ModelPengeluaran->AllData(),
            'detail_penukaran' => $detail_penukaran,
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
        ];
        return view('penukaran/get', $data);
    }

    public function tambahdata()
    {
        $data = [
            'judul' => 'Penukaran Barang',
            'subjudul' => 'Penukaran',
            'menu' => 'masterdata',
            'submenu' => 'penukaran',
            'page' => 'penukaran',
            'pengeluaran' => $this->ModelPengeluaran->AllData(),
            'detail_penukaran' => $this->ModelDetailPenukaran->AllData(),
            'penukaran' => $this->ModelPenukaran->AllData(),
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('penukaran/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_penukaran' => $this->request->getPost('kode_penukaran'),
            'kode_pengeluaran' => $this->request->getPost('kode_pengeluaran'),
            'tanggal_penukaran' => $this->request->getPost('tanggal_penukaran')
        ];
        $this->ModelPenukaran->Tambah($data);

        // Insert data detail penukaran
        $kode_penukaran = $this->request->getPost('kode_penukaran');
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_penukaran = $this->request->getPost('jumlah_penukaran');
        $alasan_penukaran = $this->request->getPost('alasan_penukaran');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_penukaran' => $kode_penukaran,
                'kode_barang' => $kode_barang[$i],
                'jumlah_penukaran' => $jumlah_penukaran[$i],
                'alasan_penukaran' => $alasan_penukaran[$i],
            ];
            $this->ModelDetailPenukaran->Tambah($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Penukaran');
    }

    public function hapusdata($kode_penukaran)
    {
        $data = [
            'kode_penukaran' => $kode_penukaran,
        ];
        $this->ModelPenukaran->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Penukaran');
    }

    public function filter()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['penukaran'] = $this->ModelPenukaran->filterByDate($start_date, $end_date);

        foreach ($data['penukaran'] as $penukaran) {
            $kode_penukaran = $penukaran['kode_penukaran'];
            $data['detail_penukaran'][$kode_penukaran] = $this->ModelPenukaran->getDetailPenukaran($kode_penukaran);
        }

        return view('penukaran/get', $data);
    }

    public function cetakLaporan()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $penukaran = $this->ModelPenukaran->getLaporan($start_date, $end_date);
        $detail_penukaran = [];

        foreach ($penukaran as $tkr) {
            $detail_penukaran[$tkr['kode_penukaran']] = $this->ModelDetailPenukaran->AllData($tkr['kode_penukaran']);
        }

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Konten laporan
        $html = '<h1>Laporan Penukaran Barang</h1>';
        $html .= '<p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p>';

        foreach ($penukaran as $tkr) {
            $html .= '<table border="1" cellspacing="0" cellpadding="8">';
            $html .= '<tr>
                        <th>Kode Pengeluaran</th>
                        <th>Kode Penukaran</th>
                        <th>Tanggal Penukaran</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jumlah Penukaran</th>
                        <th>Alasan Penukaran</th>
                    </tr>';

            $totalBarang = 0;

            foreach ($detail_penukaran[$tkr['kode_penukaran']] as $index => $detail) {
                $html .= '<tr>';

                if ($index === 0) { 
                    $html .= '<td rowspan="' . count($detail_penukaran[$tkr['kode_penukaran']]) . '">' . $tkr['kode_pengeluaran'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_penukaran[$tkr['kode_penukaran']]) . '">' . $tkr['kode_penukaran'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_penukaran[$tkr['kode_penukaran']]) . '">' . date('d-m-Y', strtotime($tkr['tanggal_penukaran'])) . '</td>';
                }

                // Kolom untuk detail barang
                $html .= '<td>' . $detail['kode_barang'] . '</td>';
                $html .= '<td>' . $detail['nama_barang'] . '</td>';
                $html .= '<td>' . $detail['satuan'] . '</td>';
                $html .= '<td>' . $detail['jumlah_penukaran'] . '</td>';
                $html .= '<td>' . $detail['alasan_penukaran'] . '</td>';
                $html .= '</tr>';

                // Akumulasi total barang
                $totalBarang += $detail['jumlah_penukaran'];
            }

            $html .= '<tr>';
            $html .= '<td colspan="6" align="right"><strong>Total Barang :</strong></td>';
            $html .= '<td><strong>' . $totalBarang . '</strong></td>';
            $html .= '</tr>';

            $html .= '</table>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Penukaran_Barang.pdf', 'D');
    }
}