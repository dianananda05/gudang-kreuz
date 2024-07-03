<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPengeluaran extends Model
{
    protected $table = 'detail_pengeluaran';
    protected $primarykey = 'id_pengeluaran';
    protected $allowedfields = ['kode_pengeluaran', 'kode_barang', 'jumlah_yang_diserahkan', 'keterangan'];

    public function AllData($kode_pengeluaran = null)
    {
        $builder = $this->db->table('detail_pengeluaran')
            ->join('barang', 'barang.kode_barang=detail_pengeluaran.kode_barang')
            ->orderBy('detail_pengeluaran.kode_barang', 'DESC');

        if (!empty($kode_pengeluaran)) {
            $builder->where('detail_pengeluaran.kode_pengeluaran', $kode_pengeluaran);
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    public function Tambah($dataDetail)
    {
        $this->db->table('detail_pengeluaran')->insert($dataDetail);
        $this->updateStokBarang($dataDetail['kode_barang'], $dataDetail['jumlah_yang_diserahkan']);
    }

    private function updateStokBarang($kode_barang, $jumlah_yang_diserahkan)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('barang');
        $builder->set('stok', 'stok - ' . $jumlah_yang_diserahkan, false);
        $builder->where('kode_barang', $kode_barang);
        $builder->update();
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_pengeluaran')->where('kode_pengeluaran', $dataDetail['kode_pengeluaran'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_pengeluaran')->where('id_pengeluaran', $dataDetail['id_pengeluaran'])->delete($dataDetail);
    }
}
