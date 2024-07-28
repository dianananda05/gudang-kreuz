<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPenerimaan;
use App\Models\ModelPengadaan;
use App\Models\ModelDetailPenerimaan;
use App\Models\ModelDetailPengadaan;
use App\Models\ModelBarang;
use TCPDF;

class Penerimaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPenerimaan = new ModelPenerimaan();
        $this->ModelPengadaan = new ModelPengadaan();
        $this->ModelDetailPenerimaan = new ModelDetailPenerimaan();
        $this->ModelDetailPengadaan = new ModelDetailPengadaan();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    protected function isKepalaGudang()
    {
        return session()->get('level') === 'kepalagudang';
    }

    protected function isKepalaPembelian()
    {
        return session()->get('level') === 'kepalapembelian';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaGudang = $this->isKepalaGudang();
        $isKepalaPembelian = $this->isKepalaPembelian();

        $barang = $this->ModelBarang->findAll();
        $penerimaan = $this->ModelPenerimaan->findAll();
        $detail_penerimaan = [];

        foreach ($penerimaan as $pene) {
            $detail_penerimaan[$pene['kode_penerimaan']] = $this->ModelDetailPenerimaan->AllData($pene['kode_penerimaan']);
        }
        
        $pengadaan = $this->ModelPengadaan->findAll();
        $detail_pengadaan = [];

        foreach ($pengadaan as $peng) {
            $detail_pengadaan[$peng['kode_po']] = $this->ModelDetailPengadaan->AllData($peng['kode_po']);
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
            'detail_pengadaan' => $detail_pengadaan,
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
            'isKepalaGudang' => $isKepalaGudang,
            'isKepalaPembelian' => $isKepalaPembelian,
        ];
        return view('penerimaan/get', $data);
    }

    public function tambahdata()
    {
        $pengadaan = $this->ModelPengadaan->select('pengadaan.*')
        ->join('penerimaan', 'penerimaan.kode_po = pengadaan.kode_po', 'left')
        ->where('penerimaan.kode_po IS NULL')
        ->where('pengadaan.status', 1)
        ->findAll();
        
        $kode_po_available = [];

        foreach ($pengadaan as $row) {
            $kode_po_available[] = $row['kode_po'];
        }
        
        $kodeTRM = $this->ModelPenerimaan->generateKodeTRM();
        $kode_po = $this->request->getPost('kode_po');
        $data_barang = $this->ModelDetailPengadaan->getByKodePO($kode_po);

        $selectedKodePo = $this->request->getGet('kode_po');

        $list_barang = [];
        if ($selectedKodePo) {
            $list_barang = $this->ModelDetailPengadaan->getByKodePO($selectedKodePo);
        }
        
        $data = [
            'judul' => 'Penerimaan Barang',
            'subjudul' => 'Penerimaan',
            'menu' => 'masterdata',
            'submenu' => 'penerimaan',
            'page' => 'penerimaan',
            'penerimaan' => $this->ModelPenerimaan->AllData(),
            'detail_penerimaan' => $this->ModelDetailPenerimaan->AllData(),
            'detail_pengadaan' => $this->ModelDetailPengadaan->AllData(),
            'pengadaan' => $this->ModelPengadaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
            'kode_penerimaan' => $kodeTRM,
            'data_barang' => $data_barang,
            'kode_po_available' => $kode_po_available,
            'list_barang' => $list_barang,
        ];
        return view('penerimaan/tambah', $data);
    }

    public function selesaiPenerimaan($kode_penerimaan)
    {
        // Update status penerimaan menjadi 'selesai'
        $this->ModelPenerimaan->updateStatusSelesai($kode_penerimaan);

        // Redirect kembali ke halaman penerimaan
        return redirect()->to(site_url('Penerimaan/index'))->with('success', 'Status penerimaan telah diperbarui menjadi selesai.');
    }

    public function getBarangByKodePO($kode_po)
    {
        $data_barang = $this->ModelDetailPengadaan->getByKodePO($kode_po);
        return json_encode($data_barang);
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

    public function edit($kode_penerimaan)
    {
        $penerimaan = $this->ModelPenerimaan->findByKodeTRM($kode_penerimaan);
        $detail_penerimaan = $this->ModelDetailPenerimaan->AllData($kode_penerimaan);
        $pengadaan = $this->ModelPengadaan->AllData($kode_penerimaan);
        $kode_po = $penerimaan['kode_po'];

        $jumlah_barang_dipesan = $this->ModelDetailPengadaan->getJumlahBarangDipesanByKodePO($kode_po);

        $data = [
            'judul' => 'Edit Penerimaan Barang',
            'subjudul' => 'Penerimaan',
            'menu' => 'masterdata',
            'submenu' => 'penerimaan',
            'page' => 'penerimaan',
            'penerimaan' => $penerimaan,
            'detail_penerimaan' => $detail_penerimaan,
            'pengadaan' => $pengadaan,
            'barang' => $this->ModelBarang->AllData(),
            'jumlah_barang_dipesan' => $jumlah_barang_dipesan,
        ];

        return view('penerimaan/edit', $data);
    }

    public function ubahdata($kode_penerimaan)
    {
        // Ambil data penerimaan yang ada
        $penerimaan = $this->ModelPenerimaan->findByKodeTRM($kode_penerimaan);
        if (!$penerimaan) {
            return redirect()->to('Penerimaan')->with('error', 'Penerimaan tidak ditemukan');
        }

        // Ambil data detail penerimaan dari form
        $jumlahYangDiterima = $this->request->getPost('jumlah_yang_diterima');
        $kodeBarang = $this->request->getPost('kode_barang');
        $kondisiBarang = $this->request->getPost('kondisi_barang');
        
        
        if (is_array($jumlahYangDiterima) && is_array($kondisiBarang) && is_array($kodeBarang)) {
            foreach ($kodeBarang as $index => $kode) {
                $dataDetail = [
                    'jumlah_yang_diterima' => $jumlahYangDiterima[$index],
                    'kondisi_barang' => $kondisiBarang[$index]
                ];
    
                // Update data detail_penerimaan berdasarkan kode_penerimaan dan kode_barang
                $this->ModelDetailPenerimaan->updateDetail($kode_penerimaan, $kode, $dataDetail);
            }
        }

        return redirect()->to('Penerimaan')->with('success', 'Penerimaan berhasil diupdate');
    }

    // public function hapusdata($kode_penerimaan)
    // {
    //     $data = [
    //         'kode_penerimaan' => $kode_penerimaan,
    //     ];
    //     $this->ModelPenerimaan->hapusdata($data);
    //     session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
    //     return redirect()->to('Penerimaan');
    // }

    public function filter()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaPembelian = $this->isKepalaPembelian();
        $isKepalaGudang = $this->isKepalaGudang();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['penerimaan'] = $this->ModelPenerimaan->filterByDate($start_date, $end_date);
        $data['isAdmin'] = $isAdmin;
        $data['isKepalaPembelian'] = $isKepalaPembelian;
        $data['isKepalaGudang'] = $isKepalaGudang;
        foreach ($data['penerimaan'] as $penerimaan) {
            $kode_penerimaan = $penerimaan['kode_penerimaan'];
            $data['detail_penerimaan'][$kode_penerimaan] = $this->ModelPenerimaan->getDetailPenerimaan($kode_penerimaan);
        }

        return view('penerimaan/get', $data);
    }

    public function cetakLaporan()
    {
        date_default_timezone_set('Asia/Jakarta');

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
                            Laporan Penerimaan Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';

        // Konten laporan
        $html = '<strong><p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p></strong>';

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
            $html .= '<td colspan="7" align="right"><strong>Total Barang :</strong></td>';
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
        $pdf->Output('Laporan_Penerimaan_Barang.pdf', 'D');
    }
}
