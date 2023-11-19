@extends('layouts.main', ['title' => 'Daftar Semua Transaksi'])
<style>
  thead tr th {
    font-size: 0.9em;
    white-space: nowrap;
  }
  tbody tr td {
    font-size: 0.8em;
  }
</style>
@section('content')
<div id="content" class="py-4">
  <div class="container-fluid">
    <h1 style="font-size: 1.5em">Daftar Semua Transaksi</h1>
    <div class="card">
      <div class="card-body">
        <h5>Filter</h5>
        <div class="row mb-3">
          <div class="col-md-3">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <input name="no_order" id="no_order" type="text" class="form-control form-control-sm" placeholder="No Order">
          </div>
          <div class="col-md-3">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="type_pembayaran" id="type_pembayaran" class="form-control form-control-sm">
              <option value="">-- Pilih Type Pembayaran --</option>
              <option value="manual">Manual Transfer</option>
              <option value="gateway">Gateway</option>
              <option value="iorpay">LinggaPay</option>
            </select>
          </div>
          <div class="col-md-3">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="bank" id="bank" class="form-control form-control-sm">
              <option value="">-- Pilih Bank --</option>
              @foreach(App\Models\PaymentMethod::where('status_payment', 1)->get() as $method)
              <option value="{{ $method->kode_payment }}">{{ $method->payment_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            {{-- <label for="" class="form-label" style="font-size: 0.8em">Nama Produk</label> --}}
            <select name="status_order" id="status_order" class="form-control form-control-sm">
              <option value="">-- Pilih Satatus Order --</option>
              <option value="PENDING">PENDING</option>
              <option value="SUCCESS">SUCCESS</option>
              <option value="CANCEL">CANCEL</option>
              <option value="0">BELUM BAYAR</option>
            </select>
          </div>
          <div class="col-md-3 mt-3">
            <label for="" class="form-label" style="font-size: 0.8em">Dari Tanggal</label>
            <input type="date" value="{{ \Carbon\carbon::now()->addDay(-7)->format('Y-m-d') }}" id="tanggal_mulai" class="form-control form-control-sm" placeholder="Tanggal">
          </div>
          <div class="col-md-3 mt-3">
            <label for="" class="form-label" style="font-size: 0.8em">Sampai Tanggal</label>
            <input type="date" value="{{ date('Y-m-d') }}" id="tanggal_akhir" class="form-control form-control-sm" placeholder="Tanggal">
          </div>
          <div class="col-md-12 mt-2 d-flex align-items-center" style="gap: 7px;">
            <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-warning px-4">Refresh</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped w-100" id="table">
            <thead>
              <tr>
                <th>#</th>
                <th>No Order</th>
                <th>Nama Pembeli</th>
                <th>Type Pembayaran</th>
                <th>Quantity</th>
                <th>Total Biaya</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-danger">
              <tr>
                <td class="text-nowrap text-light">Total :</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      scrollCollapse: true,
      paginate: true,
      ajax: {
        method: 'POST',
        url: '{{ route("admin.transaksi.data-transaksi") }}',
        data: function(d) {
          d._token = '{{ csrf_token() }}';
          d.no_order = $('#no_order').val();
          d.type_pembayaran = $('#type_pembayaran').val();
          d.bank = $('#bank').val();
          d.status_order = $('#status_order').val();
          d.tanggal_mulai = $('#tanggal_mulai').val();
          d.tanggal_akhir = $('#tanggal_akhir').val();
        }
      },
      columns: [
        { data: '#', 
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
        }},
        { data: 'no_order', search: true, name: 'no_order'},
        { data: 'nama_pembeli', search: true, name: 'nama_pembeli'},
        { data: 'type_pembayaran', search: true, name: 'type_pembayaran'},
        { data: 'quantity', search: true, name: 'quantity'},
        { data: 'biaya', search: true, name: 'total_biaya'},
        { data: 'status', search: true, name: 'status'},
        { data: 'tanggal', search: true, name: 'tanggal'},
        { data: 'action', name: 'action'},
      ],
      fnFooterCallback: function(nRow, aaData, iStart, iEnd, aiDisplay) {
        let total = 0;
        $.each(aaData, function (index, value) { 
           total += Math.floor(value.total_biaya)
        });
        $(this.api().column(5).footer()).addClass('text-right text-light');
        $(this.api().column(5).footer()).html('Rp. '+total.toLocaleString())
        console.log(this.api().column(5).footer())
      }
    });
    console.log(table.api)
    $('#no_order').keyup(function(event) {
      table.ajax.reload();
    });
    $('#type_pembayaran').change(function(event) {
      table.ajax.reload();
    });
    $('#bank').change(function(event) {
      table.ajax.reload();
    });
    $('#status_order').change(function(event) {
      table.ajax.reload();
    });
    $('#tanggal_mulai').change(function(event) {
      table.ajax.reload();
    });
    $('#tanggal_akhir').change(function(event) {
      table.ajax.reload();
    });
  })

  function konfirmasi_toko(kode_toko) {
    $.post(`{{ route('admin.toko.konfirmasi') }}`, {
      _token: '{{ csrf_token() }}',
      kode_toko: kode_toko
    }, function(response) {
      console.log(response)
      if(response.status && !response.error) {
        $.toast({
            heading: 'Success',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'success'
        });
        table.ajax.reload();
      }else {
        $.toast({
            heading: 'Error',
            text: response.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error'
        });
      }
    })
  }
</script>
@endsection