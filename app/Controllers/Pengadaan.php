<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPengadaan;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPengadaan;
use App\Models\ModelDetailPermintaan;
use App\Models\ModelBarang;
use TCPDF;

class Pengadaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPengadaan = new ModelPengadaan();
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPengadaan = new ModelDetailPengadaan();
        $this->ModelDetailPermintaan = new ModelDetailPermintaan();
        $this->ModelBarang = new ModelBarang();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    protected function isKepalaPembelian()
    {
        return session()->get('level') === 'kepalapembelian';
    }
    protected function isKepalaGudang()
    {
        return session()->get('level') === 'kepalagudang';
    }
    
    public function index()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaPembelian = $this->isKepalaPembelian();
        $isKepalaGudang = $this->isKepalaGudang();

        $pengadaan = $this->ModelPengadaan->findAll();
        $detail_pengadaan = [];

        foreach ($pengadaan as $peng) {
            $detail_pengadaan[$peng['kode_po']] = $this->ModelDetailPengadaan->AllData($peng['kode_po']);
        }

        $data = [
            'judul' => 'Pengadaan Barang',
            'subjudul' => 'Pengadaan',
            'menu' => 'masterdata',
            'submenu' => 'pengadaan',
            'page' => 'pengadaan',
            'pengadaan' => $pengadaan,
            'detail_pengadaan' => $detail_pengadaan,
            'permintaan' => $this->ModelPermintaan->AllData(),
            'barang' => $this->ModelBarang->AllData(),
            'isAdmin' => $isAdmin,
            'isKepalaPembelian' => $isKepalaPembelian,
            'isKepalaGudang' => $isKepalaGudang,
        ];
        return view('pengadaan/get', $data);
    }

    public function approve($kode_po)
    {
        $data = [
            'status' => 1
        ];

        $this->ModelPengadaan->updateStatus($kode_po, $data);
        session()->setFlashdata('pesan', 'Pengadaan berhasil disetujui.');
        return redirect()->to('Pengadaan');
    }

    public function reject($kode_po)
    {
        $data = [
            'status' => 0
        ];

        $this->ModelPengadaan->updateStatus($kode_po, $data);
        session()->setFlashdata('pesan', 'Pengadaan berhasil ditolak.');
        return redirect()->to('Pengadaan');
    }

    public function tambahdata()
    {   
        $isKepalaPembelian = $this->isKepalaPembelian();
        $permintaan = $this->ModelPermintaan->select('permintaan.*')->join('pengadaan', 'pengadaan.kode_permintaan = permintaan.kode_permintaan', 'left')->where('pengadaan.kode_permintaan IS NULL')->like('permintaan.kode_permintaan', 'PO%', 'after')->findAll();
        
        $kode_perm_available = [];

        foreach ($permintaan as $row) {
            $kode_perm_available[] = $row['kode_permintaan'];
        }

        $kodePO = $this->ModelPengadaan->generateKodePO();
        $kode_permintaan = $this->request->getPost('kode_permintaan');
        $data_barang = $this->ModelDetailPengadaan->getByKodePO($kode_permintaan);

        $selectedKodePrm = $this->request->getGet('kode_permintaan');

        $list_barang = [];
        if ($selectedKodePrm) {
            $list_barang = $this->ModelDetailPermintaan->getByKodePRM($selectedKodePrm);
        }

        $data = [
            'judul' => 'Pengadaan Barang',
            'subjudul' => 'Pengadaan',
            'menu' => 'masterdata',
            'submenu' => 'pengadaan',
            'page' => 'pengadaan',
            'pengadaan' => $this->ModelPengadaan->AllData(),
            'detail_pengadaan' => $this->ModelDetailPengadaan->AllData(),
            'permintaan' => $permintaan,
            'barang' => $this->ModelBarang->AllData(),
            'kode_po' => $kodePO,
            'data_barang' => $data_barang,
            'kode_perm_available' => $kode_perm_available,
            'list_barang' => $list_barang,
            'isKepalaPembelian' => $isKepalaPembelian,
        ];
        return view('pengadaan/tambah', $data);
    }

    public function insertdata()
    {
        $data = [
            'kode_po' => $this->request->getPost('kode_po'),
            'kode_permintaan' => $this->request->getPost('kode_permintaan'),
            'tanggal_pengadaan' => $this->request->getPost('tanggal_pengadaan')
        ];
        $this->ModelPengadaan->Tambah($data);

        // Insert data detail pengadaan
        $kode_po = $this->request->getPost('kode_po');
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_barang = $this->request->getPost('jumlah_barang');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_po' => $kode_po,
                'kode_barang' => $kode_barang[$i],
                'jumlah_barang' => $jumlah_barang[$i],
            ];
            $this->ModelDetailPengadaan->Tambah($dataDetail);
        }
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('Pengadaan');
    }

    public function edit($kode_po)
    {
        $pengadaan = $this->ModelPengadaan->findByKodePO($kode_po);
        $detail_pengadaan = $this->ModelDetailPengadaan->AllData($kode_po);
        $permintaan = $this->ModelPermintaan->AllData($kode_po); 

        $data = [
            'judul' => 'Edit Pengadaan Barang',
            'subjudul' => 'Pengadaan',
            'menu' => 'masterdata',
            'submenu' => 'pengadaan',
            'page' => 'pengadaan',
            'pengadaan' => $pengadaan,
            'detail_pengadaan' => $detail_pengadaan,
            'permintaan' => $permintaan,
            'barang' => $this->ModelBarang->AllData(),
        ];
        return view('pengadaan/edit', $data);
    }

    public function ubahdata($kode_po)
    {
        $pengadaan = $this->ModelPengadaan->findByKodePO($kode_po);
        if (!$pengadaan) {
            return redirect()->to('Pengadaan')->with('error', 'Pengadaan tidak ditemukan');
        }

        $nama_supplier = $this->request->getPost('nama_supplier');

        $data = [
            'nama_supplier' => $nama_supplier,
        ]; 
        $this->ModelPengadaan->ubahdata($kode_po, $data);

        // Update data detail pengadaan
        $kodeBarang = $this->request->getPost('kode_barang');
        $jumlahYangDipesan = $this->request->getPost('jumlah_barang');

        if (is_array($jumlahYangDipesan) && is_array($kodeBarang)) {
            foreach ($kodeBarang as $index => $kode) {
                $dataDetail = [
                    'jumlah_barang' => $jumlahYangDipesan[$index]
                ];
    
                // Update data detail_penerimaan berdasarkan kode_penerimaan dan kode_barang
                $this->ModelDetailPengadaan->updateDetail($kode_po, $kode, $dataDetail);
            }
        }

        session()->setFlashdata('pesan', 'Data Berhasil Diupdate');
        return redirect()->to('Pengadaan');
    }

    public function hapusdata($kode_po)
    {
        $data = [
            'kode_po' => $kode_po,
        ];
        $this->ModelPengadaan->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('Pengadaan');
    }

    public function downloadInvoice($kode_po)
    {
        // Ambil data pengadaan berdasarkan kode PO
        $pengadaan = $this->ModelPengadaan->findByKodePO($kode_po);
        $supplier  = $this->ModelPengadaan->findSupplier($kode_po);
        $detail_pengadaan = $this->ModelDetailPengadaan->AllData($kode_po);

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 7);

        $pdf->setPrintHeader(false); // Jangan tampilkan header
        $pdf->setPrintFooter(false); // Jangan tampilkan footer

        $logoPath = FCPATH . 'template/assets/img/kreuz.png';
        $namaPerusahaan = 'PT. Kreuz Bike Indonesia';
        $alamatPerusahaan = 'Jl. Rereng Adumanis No.47, Sukaluyu, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40123';

        // Header invoice
        $html = '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td colspan="2"><hr style="margin-top: 20px; margin-bottom: 20px;"></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td style="width: 20%; text-align: left; vertical-align: top;">
                    <img src="' . $logoPath . '" style="width: 60px; height: auto; vertical-align: middle;"/>
                    </td>';
        $html .= '<td style="width: 80%; text-align: left; vertical-align: top; padding-top: 20px;">';
        $html .= '<h2 style="margin-top: 20px; margin-bottom: 5px; font-size: 10px; font-weight: bold; line-height: 1;">' . $namaPerusahaan . '</h2>';
        $html .= '<p style="font-size: 8px; margin-top: 5px; line-height: 1.2;">' . $alamatPerusahaan . '</p>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td colspan="2"><hr style="margin-top: 20px; margin-bottom: 20px;"></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="text-align: center;">';
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<h4 style="font-size: 12; font-weight: bold;">PURCHASE ORDER</h4>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td colspan="2"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2">';
        $html .= '<p><strong>Kode PO:</strong> ' . $pengadaan['kode_po'] . '</p>';
        $html .= '<p><strong>Tanggal Pengadaan:</strong> ' . date('d/m/Y', strtotime($pengadaan['tanggal_pengadaan'])) . '</p>';
        $html .= '<p><strong>Nama Supplier:</strong> ' . $supplier['nama_supplier'] . '</p>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="padding-top: 20px;"></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Detail pengadaan
        $html .= '<table border="1" cellspacing="0" cellpadding="8">';
        $html .= '<tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jumlah Barang</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>';

        $totalBarang = 0;
        $totalHarga = 0;

        foreach ($detail_pengadaan as $detail) {
            $html .= '<tr>';
            $html .= '<td>' . $detail['kode_barang'] . '</td>';
            $html .= '<td>' . $detail['nama_barang'] . '</td>';
            $html .= '<td>' . $detail['satuan'] . '</td>';
            $html .= '<td>' . $detail['jumlah_barang'] . '</td>';
            $html .= '<td>' . 'Rp. ' . number_format($detail['harga_satuan'], 2, ',', '.') . '</td>';
            
            // Hitung total harga untuk item saat ini
            $totalItem = $detail['jumlah_barang'] * $detail['harga_satuan'];
            $html .= '<td>' . 'Rp. ' . number_format($totalItem, 2, ',', '.') . '</td>';

            $html .= '</tr>';

            // Akumulasi total barang dan total harga
            $totalBarang += $detail['jumlah_barang'];
            $totalHarga += $totalItem;
        }

        // Tambahkan baris total di luar loop
        $html .= '<tr>';
        $html .= '<td colspan="3" align="right"><strong>Total Barang :</strong></td>';
        $html .= '<td align="right"><strong>' . $totalBarang . '</strong></td>';
        $html .= '<td colspan="2"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="5" align="right"><strong>Total Harga :</strong></td>';
        $html .= '<td align="right"><strong>' . 'Rp. ' . number_format($totalHarga, 2, ',', '.') . '</strong></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<div style="margin-top: 20px;"></div>';
        $html .= '<div style="margin-top: 20px;">';
        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td style="width: 33.33%; text-align: center;">';
        $html .= '<p style="margin-bottom: 5px;">Kepala Gudang</p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;">Budi Setiawan</p>';
        $html .= '</td>';
        $html .= '<td style="width: 33.33%;"></td>'; // Kolom kosong
        $html .= '<td style="text-align: center; width: 33.33%;">';
        $html .= '<p style="margin-bottom: 5px;">Kepala Produksi</p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;">Agus Ipan Herdiawan</p>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 33.33%;"></td>'; // Kolom kosong
        $html .= '<td style="text-align: center; width: 33.33%;">';
        $html .= '<p style="margin-bottom: 5px;">Manager Accounting</p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;"></p>';
        $html .= '<p style="font-weight: bold; margin-bottom: 10px;">M. Nuryansyah</p>';
        $html .= '</td>';
        $html .= '<td style="width: 33.33%;"></td>'; // Kolom kosong
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Purchase_Order_' . $pengadaan['kode_po'] . '.pdf', 'D');
    }

    public function filter()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaPembelian = $this->isKepalaPembelian();
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $data['pengadaan'] = $this->ModelPengadaan->filterByDate($start_date, $end_date);
        $data['isAdmin'] = $isAdmin;
        $data['isKepalaPembelian'] = $isKepalaPembelian;
        foreach ($data['pengadaan'] as $pengadaan) {
            $kode_po = $pengadaan['kode_po'];
            $data['detail_pengadaan'][$kode_po] = $this->ModelPengadaan->getDetailPengadaan($kode_po);
        }

        return view('pengadaan/get', $data);
    }

    public function cetakLaporan()
    {
        date_default_timezone_set('Asia/Jakarta');

        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $pengadaan = $this->ModelPengadaan->getLaporan($start_date, $end_date);
        $detail_pengadaan = [];

        foreach ($pengadaan as $peng) {
            $detail_pengadaan[$peng['kode_po']] = $this->ModelDetailPengadaan->AllData($peng['kode_po']);
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
                            Laporan Pengadaan Barang - ' . date('d F Y H:i') . ' WIB
                        </td>
                    </tr>
                </table>
            </div>';

        // Konten laporan]
        $html = '<strong><p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p></strong>';

        foreach ($pengadaan as $peng) {
            $html .= '<table border="1" cellspacing="0" cellpadding="8">';
            $html .= '<tr>
                        <th>Kode Permintaan</th>
                        <th>Kode PO</th>
                        <th>Tanggal Pengadaan</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jumlah Barang</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                    </tr>';

            $totalBarang = 0;
            $totalHarga = 0;
            
            // Iterasi untuk setiap detail pengadaan dari pengadaan saat ini
            foreach ($detail_pengadaan[$peng['kode_po']] as $index => $detail) {
                $html .= '<tr>';
            
                if ($index === 0) { // Hanya untuk baris pertama dari setiap pengadaan
                    $html .= '<td rowspan="' . count($detail_pengadaan[$peng['kode_po']]) . '">' . $peng['kode_permintaan'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_pengadaan[$peng['kode_po']]) . '">' . $peng['kode_po'] . '</td>';
                    $html .= '<td rowspan="' . count($detail_pengadaan[$peng['kode_po']]) . '">' . date('d-m-Y', strtotime($peng['tanggal_pengadaan'])) . '</td>';
                }
            
                // Kolom untuk detail barang
                $html .= '<td>' . $detail['kode_barang'] . '</td>';
                $html .= '<td>' . $detail['nama_barang'] . '</td>';
                $html .= '<td>' . $detail['satuan'] . '</td>';
                $html .= '<td align="right">' . $detail['jumlah_barang'] . '</td>';
                $html .= '<td align="right">' . 'Rp. ' . number_format($detail['harga_satuan'], 2, ',', '.') . '</td>';
                $html .= '<td align="right">' . 'Rp. ' . number_format($detail['jumlah_barang'] * $detail['harga_satuan'], 2, ',', '.') . '</td>';
                $html .= '</tr>';
            
                // Akumulasi total barang dan harga
                $totalBarang += $detail['jumlah_barang'];
                $totalHarga += $detail['jumlah_barang'] * $detail['harga_satuan'];
            }
            
            // Baris total barang dan harga untuk pengadaan ini
            $html .= '<tr>';
            $html .= '<td colspan="6" align="right"><strong>Total Barang :</strong></td>';
            $html .= '<td align="right"><strong>' . $totalBarang . '</strong></td>';
            $html .= '<td colspan="3"></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="7" align="right"><strong>Total Harga :</strong></td>';
            $html .= '<td colspan="3" align="right"><strong>' . 'Rp. ' . number_format($totalHarga, 2, ',', '.') . '</strong></td>';
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
        $pdf->Output('Laporan_Pengadaan_Barang.pdf', 'D');
    }
    
}
