<?php

namespace App\Http\Controllers\Admin\Pendapatan;

use App\Http\Controllers\Controller;
use App\Models\Pendapatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    public function index(Request $request) {
        $where = '1=1';
        
        if(isset($request->tanggal_mulai)) {
            $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') >= '$request->tanggal_mulai'";
        }else {
            $tanggal_mulai = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') >= '$tanggal_mulai'";
        }
        if(isset($request->tanggal_akhir)) {
            $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') <= '$request->tanggal_akhir'";
        }else {
            $tanggal_akhir = Carbon::now()->lastOfMonth()->format('Y-m-d');
            $where .= " and DATE_FORMAT(created_at, '%Y-%m-%d') <= '$tanggal_akhir'";
        }

        if(isset($request->type)) {
            $where .= " and type = '$request->type'";
        }

        $pendapatan = Pendapatan::whereRaw($where)->orderBy('created_at', 'desc')->get();
        $group_pd = array();

        foreach($pendapatan as $pd) {
            if(empty($group_pd[$pd->type])) {
                $group_pd[$pd->type] = array();
                array_push($group_pd[$pd->type], $pd);
            }else {
                array_push($group_pd[$pd->type], $pd);
            }
        }
        // dd($request->tanggal_akhir);
        return view('admin.pendapatan.index', compact('group_pd'));
    }
}
