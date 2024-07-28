<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPengeluaran;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPengeluaran;
use App\Models\ModelDetailPermintaan;
use App\Models\ModelBarang;
use TCPDF;

class Pengeluaran extends BaseController
{
    public function __construct()
    {
        $this->ModelPengeluaran = new ModelPengeluaran();
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPengeluaran = new ModelDetailPengeluaran();
        $this->ModelDetailPermintaan = new ModelDetailPermintaan();
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

        $permintaan = $this->ModelPermintaan->findAll();
        $detail_permintaan = [];

        foreach ($permintaan as $perm) {
            $detail_permintaan[$perm['kode_permintaan']] = $this->ModelDetailPermintaan->AllData($perm['kode_permintaan']);
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
            'detail_permintaan' => $this->ModelDetailPermintaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
        ];
        return view('pengeluaran/get', $data);
    }

    public function tambahdata()
    {
        $permintaan = $this->ModelPermintaan->select('permintaan.*')
        ->join('pengeluaran', 'pengeluaran.kode_permintaan = permintaan.kode_permintaan', 'left')
        ->where('pengeluaran.kode_permintaan IS NULL')
        ->where('permintaan.status', '1')
        ->like('permintaan.kode_permintaan', 'PRO%', 'after')
        ->findAll();

        $kode_perm_available = [];

        foreach ($permintaan as $row) {
            $kode_perm_available[] = $row['kode_permintaan'];
        }
        
        $kodePRO = $this->ModelPengeluaran->generateKodePRO();
        $kode_permintaan = $this->request->getPost('kode_permintaan');
        $data_barang = $this->ModelDetailPermintaan->getByKodePRM($kode_permintaan);

        $selectedKodePrm = $this->request->getGet('kode_permintaan');

        $list_barang = [];
        if ($selectedKodePrm) {
            $list_barang = $this->ModelDetailPermintaan->getByKodePRM($selectedKodePrm);
        }

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
            'kode_pengeluaran' => $kodePRO,
            'data_barang' => $data_barang,
            'kode_perm_available' => $kode_perm_available,
            'list_barang' => $list_barang,
        ];
        return view('pengeluaran/tambah', $data);
    }

    public function getBarangByKodePRM($kode_permintaan)
    {
        $data_barang = $this->ModelDetailPermintaan->getByKodePRM($kode_permintaan);
        return json_encode($data_barang);
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
        $isAdmin = $this->isAdmin();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['pengeluaran'] = $this->ModelPengeluaran->filterByDate($start_date, $end_date);
        $data['isAdmin'] = $isAdmin;
        foreach ($data['pengeluaran'] as $pengeluaran) {
            $kode_pengeluaran = $pengeluaran['kode_pengeluaran'];
            $data['detail_pengeluaran'][$kode_pengeluaran] = $this->ModelPengeluaran->getDetailPengeluaran($kode_pengeluaran);
        }

        return view('pengeluaran/get', $data);
    }

    public function cetakLaporan()
    {
        date_default_timezone_set('Asia/Jakarta');

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
                            Laporan Pengeluaran Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';

        // Konten laporan
        $html = '<strong><p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p></strong>';

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
        $pdf->Output('Laporan_Pengeluaran_Barang.pdf', 'D');
    }
}
