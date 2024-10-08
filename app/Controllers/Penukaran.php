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
        $pengeluaran = $this->ModelPengeluaran->findAll();
        $kodeTKR = $this->ModelPenukaran->generateKodeTKR();
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
            'kode_penukaran' => $kodeTKR,
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
        $isAdmin = $this->isAdmin();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['penukaran'] = $this->ModelPenukaran->filterByDate($start_date, $end_date);
        $data['isAdmin'] = $isAdmin;
        foreach ($data['penukaran'] as $penukaran) {
            $kode_penukaran = $penukaran['kode_penukaran'];
            $data['detail_penukaran'][$kode_penukaran] = $this->ModelPenukaran->getDetailPenukaran($kode_penukaran);
        }

        return view('penukaran/get', $data);
    }

    public function cetakLaporan()
    {
        date_default_timezone_set('Asia/Jakarta');

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
                            Laporan Penukaran Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';

        // Konten laporan
        $html = '<strong><p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p></strong>';

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
        $pdf->Output('Laporan_Penukaran_Barang.pdf', 'D');
    }
}