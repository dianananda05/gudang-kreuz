<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPenerimaan;
use App\Models\ModelPengadaan;
use App\Models\ModelDetailPenerimaan;
use App\Models\ModelBarang;
use TCPDF;

class Penerimaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPenerimaan = new ModelPenerimaan();
        $this->ModelPengadaan = new ModelPengadaan();
        $this->ModelDetailPenerimaan = new ModelDetailPenerimaan();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();

        $penerimaan = $this->ModelPenerimaan->findAll();
        $detail_penerimaan = [];

        foreach ($penerimaan as $pene) {
            $detail_penerimaan[$pene['kode_penerimaan']] = $this->ModelDetailPenerimaan->AllData($pene['kode_penerimaan']);
        }

        $data = [
            'judul' => 'Penerimaan Barang',
            'subjudul' => 'Penerimaan',
            'menu' => 'masterdata',
            'submenu' => 'penerimaan',
            'page' => 'penerimaan',
            'penerimaan' => $penerimaan,
            'pengadaan' => $this->ModelPengadaan->AllData(),
            'detail_penerimaan' => $detail_penerimaan,
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
        ];
        return view('penerimaan/get', $data);
    }

    public function tambahdata()
    {
        $data = [
            'judul' => 'Penerimaan Barang',
            'subjudul' => 'Penerimaan',
            'menu' => 'masterdata',
            'submenu' => 'penerimaan',
            'page' => 'penerimaan',
            'penerimaan' => $this->ModelPenerimaan->AllData(),
            'detail_penerimaan' => $this->ModelDetailPenerimaan->AllData(),
            'pengadaan' => $this->ModelPengadaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('penerimaan/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_penerimaan' => $this->request->getPost('kode_penerimaan'),
            'kode_po' => $this->request->getPost('kode_po'),
            'tanggal_penerimaan' => $this->request->getPost('tanggal_penerimaan'),
            'nomor_po' => $this->request->getPost('nomor_po')
        ];
        $this->ModelPenerimaan->Tambah($data);

        // Insert data detail penerimaan
        $kode_penerimaan = $this->request->getPost('kode_penerimaan');
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_yang_diterima = $this->request->getPost('jumlah_yang_diterima');
        $kondisi_barang = $this->request->getPost('kondisi_barang');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_penerimaan' => $kode_penerimaan,
                'kode_barang' => $kode_barang[$i],
                'jumlah_yang_diterima' => $jumlah_yang_diterima[$i],
                'kondisi_barang' => $kondisi_barang[$i],
            ];
            $this->ModelDetailPenerimaan->Tambah($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Penerimaan');
    }

    public function hapusdata($kode_penerimaan)
    {
        $data = [
            'kode_penerimaan' => $kode_penerimaan,
        ];
        $this->ModelPenerimaan->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Penerimaan');
    }

    public function filter()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['penerimaan'] = $this->ModelPenerimaan->filterByDate($start_date, $end_date);

        foreach ($data['penerimaan'] as $penerimaan) {
            $kode_penerimaan = $penerimaan['kode_penerimaan'];
            $data['detail_penerimaan'][$kode_penerimaan] = $this->ModelPenerimaan->getDetailPenerimaan($kode_penerimaan);
        }

        return view('penerimaan/get', $data);
    }

    public function cetakLaporan()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $penerimaan = $this->ModelPenerimaan->getLaporan($start_date, $end_date);
        $detail_penerimaan = [];

        foreach ($penerimaan as $trm) {
            $detail_penerimaan[$trm['kode_penerimaan']] = $this->ModelDetailPenerimaan->AllData($trm['kode_penerimaan']);
        }

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Konten laporan
        $html = '<h1>Laporan Penerimaan Barang</h1>';
        $html .= '<p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p>';

        foreach ($penerimaan as $trm) {
            $html .= '<table border="1" cellspacing="0" cellpadding="8">';
            $html .= '<tr>
                        <th>Kode PO</th>
                        <th>Kode Penerimaan</th>
                        <th>Tanggal Penerimaan</th>
                        <th>Nomor PO</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jumlah Yang Diterima</th>
                        <th>Kondisi Barang</th>
                    </tr>';

            $totalBarang = 0;

            foreach ($detail_penerimaan[$trm['kode_penerimaan']] as $index => $detail) {
                $html .= '<tr>';

                if ($index === 0) { 
                    $html .= '<td rowspan="' . count($detail_penerimaan[$trm['kode_penerimaan']]) . '">' . $trm['kode_po'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_penerimaan[$trm['kode_penerimaan']]) . '">' . $trm['kode_penerimaan'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_penerimaan[$trm['kode_penerimaan']]) . '">' . date('d-m-Y', strtotime($trm['tanggal_penerimaan'])) . '</td>';
                    $html .= '<td rowspan="' . count($detail_penerimaan[$trm['kode_penerimaan']]) . '">' . $trm['nomor_po'] . '</td>';
                }

                // Kolom untuk detail barang
                $html .= '<td>' . $detail['kode_barang'] . '</td>';
                $html .= '<td>' . $detail['nama_barang'] . '</td>';
                $html .= '<td>' . $detail['satuan'] . '</td>';
                $html .= '<td>' . $detail['jumlah_yang_diterima'] . '</td>';
                $html .= '<td>' . $detail['kondisi_barang'] . '</td>';
                $html .= '</tr>';

                // Akumulasi total barang
                $totalBarang += $detail['jumlah_yang_diterima'];
            }

            $html .= '<tr>';
            $html .= '<td colspan="6" align="right"><strong>Total Barang :</strong></td>';
            $html .= '<td><strong>' . $totalBarang . '</strong></td>';
            $html .= '</tr>';

            $html .= '</table>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Penerimaan_Barang.pdf', 'D');
    }
}
