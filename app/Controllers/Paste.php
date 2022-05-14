<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\TempelanModel;
use CodeIgniter\I18n\Time;

class Paste extends ResourceController
{
    use ResponseTrait;
    // const $this->m = new TempelanModel();

    public function __construct() {
        $this->m = new TempelanModel();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function create()
    {
        helper('text');
        $durasi = $this->request->getPost('duration');
        $sekarang = date('Y-m-d H:i:s');
        $this->m->where(['kadaluarsa <' => $sekarang])->delete();

        $semen = strtotime($sekarang) + 5*60;
        if ($durasi == 1) {
            $semen = strtotime($sekarang) + 60*60;
        } else if ($durasi == 2) {
            $semen = strtotime($sekarang) + 24*60*60;
        } elseif ($durasi == 3) {
            $semen = strtotime($sekarang) + 7*24*60*60;
        }
        $kadaluarsa = date('Y-m-d H:i:s', $semen);

        $notuniq = true;
        $kode = "";

        while ($notuniq) {
            $kode = strtoupper(random_string('alpha', 3));
            $paste = $this->m->where(['kode' => $kode])->first();

            if ($paste == null) $notuniq = false;
        }

        $data = [
            'teks' => $this->request->getPost('text'),
            'kadaluarsa' => $kadaluarsa,
            'kode' => $kode,
        ];
        $this->m->insert($data);

        $response = [
            'kode' => $kode
        ];

        return $this->respondCreated($response, 201);
    }

    public function show($kode = null)
    {
        $sekarang = date('Y-m-d H:i:s');
        $this->m->where(['kadaluarsa <' => $sekarang])->delete();

        $paste = $this->m->where(['kode' => strtoupper($kode)])->first();
        $data = [
            'text' => 'Data not found',
            'expired' => '-',
            'accessed' => 0
        ];
        if ($paste == null) return $this->respond($data, 201);
        $this->m->update(['id' => $paste['id']], ['diakses' => $paste['diakses']+1]);

        $now = Time::parse(date('Y-m-d H:i:s'), 'Asia/Jakarta');
        $tujuan = Time::parse(date('Y-m-d H:i:s', strtotime($paste['kadaluarsa'])), 'Asia/Jakarta');
        $kapan = $now->difference($tujuan)->humanize();

        $data = [
            'text' => $paste['teks'],
            'expired' => $kapan,
            'accessed' => $paste['diakses']+1
        ];

        return $this->respond($data, 201);
    }
}
