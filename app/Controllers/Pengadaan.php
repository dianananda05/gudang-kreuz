<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPengadaan;
use App\Models\ModelPermintaan;
use App\Models\ModelDetailPengadaan;
use App\Models\ModelBarang;
use TCPDF;

class Pengadaan extends BaseController
{
    public function __construct()
    {
        $this->ModelPengadaan = new ModelPengadaan();
        $this->ModelPermintaan = new ModelPermintaan();
        $this->ModelDetailPengadaan = new ModelDetailPengadaan();
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
    
    public function index()
    {
        $isAdmin = $this->isAdmin();
        $isKepalaPembelian = $this->isKepalaPembelian();

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
        $permintaan = $this->ModelPermintaan->select('permintaan.*')->join('pengadaan', 'pengadaan.kode_permintaan = permintaan.kode_permintaan', 'left')->where('pengadaan.kode_permintaan IS NULL')->like('permintaan.kode_permintaan', 'PO%', 'after')->findAll();
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
        $data = [
            'kode_permintaan' => $this->request->getPost('kode_permintaan'),
            'tanggal_pengadaan' => $this->request->getPost('tanggal_pengadaan'),
            'nama_supplier' => $this->request->getPost('nama_supplier'), 
        ];
        $this->ModelPengadaan->ubahdata($kode_po, $data);

        // Update data detail pengadaan
        $kode_barang = $this->request->getPost('kode_barang');
        $jumlah_barang = $this->request->getPost('jumlah_barang');

        for ($i = 0; $i < count($kode_barang); $i++) {
            $dataDetail = [
                'kode_barang' => $kode_barang[$i],
                'jumlah_barang' => $jumlah_barang[$i],
            ];
            $this->ModelDetailPengadaan->ubahdata($kode_po, $kode_barang[$i], $dataDetail);
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
        $detail_pengadaan = $this->ModelDetailPengadaan->AllData($kode_po);

        // Membuat objek TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        // Header invoice
        $html = '<table width="100%" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<h1>Invoice</h1>';
        $html .= '<td width="50%">';
        $html .= '<h3>Pembeli</h3>';
        $html .= '<p>Nama: ' . 'PT. Kreuz Bike Indonesia' . '</p>';
        $html .= '<p>Alamat: ' . 'Jl. Rereng Adumanis No.47, Sukaluyu, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40123' . '</p>';
        $html .= '<p>Telepon: ' . '+62 819-1500-2786' . '</p>';
        $html .= '<p>Email: ' . 'Kreuzbikeindonesia@gmail.com' . '</p>';
        $html .= '</td>';
        $html .= '<td width="50%">';
        $html .= '<h3>Supplier</h3>';
        $html .= '<p>Nama: ' . 'RodaLink' . '</p>';
        $html .= '<p>Alamat: ' . 'Jl. Ligar Resik No. 26' . '</p>';
        $html .= '<p>Telepon: ' . '022 2514238' . '</p>';
        $html .= '<p>Email: ' . 'rodalink@gmail.com' . '</p>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2"><hr style="margin-top: 20px; margin-bottom: 20px;">' . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2">';
        $html .= '<p><strong>Kode PO:</strong> ' . $pengadaan['kode_po'] . '</p>';
        $html .= '<p><strong>Tanggal Pengadaan:</strong> ' . date('d/m/Y', strtotime($pengadaan['tanggal_pengadaan'])) . '</p>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="padding-top: 20px;">' . '</td>';
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
            $html .= '<td>' . number_format($detail['harga_satuan'], 2, ',', '.') . '</td>';
            
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
        $html .= '<td colspan="4" align="right"><strong>Total Barang :</strong></td>';
        $html .= '<td align="right"><strong>' . $totalBarang . '</strong></td>';
        $html .= '<td colspan="2"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="5" align="right"><strong>Total Harga :</strong></td>';
        $html .= '<td align="right"><strong>' . 'Rp. ' . number_format($totalHarga, 2, ',', '.') . '</strong></td>';
        $html .= '</tr>';

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Invoice_Pengadaan_' . $pengadaan['kode_po'] . '.pdf', 'D');
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
        $pdf->SetFont('helvetica', '', 8);

        // Konten laporan
        $html = '<h1>Laporan Pengadaan Barang</h1>';
        $html .= '<p>Periode: ' . date('d-m-Y', strtotime($start_date)) . ' s/d ' . date('d-m-Y', strtotime($end_date)) . '</p>';

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

        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Laporan_Pengadaan_Barang.pdf', 'D');
    }
    
}
