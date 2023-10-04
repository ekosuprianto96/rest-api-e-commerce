<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    * {
      font-family: sans-serif;
    }
    thead tr th {
      padding: 8px;
      text-align: center;
      background-color: rgb(207, 207, 207);
    }
    tbody tr td {
      padding: 8px;
      text-align: center;
    }
  </style>
</head>
<body style="padding: 20px;">
  <div style="padding: 10px 0px;">
    <h3 style="margin: 5px 0px;">Detail Transaksi </h3>
    <h3>ID : {{ $data['order'][0]->no_order }}</h3>
    <p>Tanggal : {{ $data['order'][0]->created_at->format('d/M/Y H:i') }}</p>
  </div>
  <div style="padding: 10px 0px;">
    <p><span style="color: rgb(255, 58, 58);">*</span> Perlu diperhatikan bahwa setiap transaksi kami berikan potongan biaya platform sebesar 10% untuk pengembangan aplikasi iorsel.com</p>
  </div>
  <table border="1" style="width: 100%;" cellspacing="0">
    <thead>
      <tr>
        <th>No.</th>
        <th>Produk</th>
        <th>Harga</th>
        <th>Potongan</th>
        <th>Customer</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data['order'] as $key => $value)
        <tr>
          <td>{{ $key + 1 }}</td>
          <td>{{ $value->produk->nm_produk }}</td>
          <td>Rp. {{ number_format($value->produk->harga, 2) }}</td>
          <td>{{ $value->produk->potongan_harga > 0 ? 'Rp. '.number_format($value->produk->potongan_harga, 2) : ($value->produk->potongan_persen > 0 ? $value->produk->potongan_persen.'%' : 0) }}</td>
          <td>{{ $value->user->full_name }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" align="center" style="padding: 10px;">
          <strong>Total :</strong>
        </td>
        <td colspan="3" align="center" style="padding: 10px;">
          Rp. <strong>{{ number_format($data['total_biaya'], 2) }}</strong>
        </td>
      </tr>
    </tfoot>
  </table>
  <div style="margin: 10px 0px;padding: 10px 8px;text-align: center;background-color: rgb(207, 207, 207);width: 100%;">
    <center>
      <p style="width: 100%;text-align: center;">&copy;Copy Right <a href="{{ env('URL_WEBSITE') }}">iorsel.com</a> {{ date('Y') }}</p>
    </center>
  </div>
</body>
</html>