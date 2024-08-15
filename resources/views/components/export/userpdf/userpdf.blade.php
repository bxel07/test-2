<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f8f9fa; 
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .list-group-item {
            font-size: 1.1em;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="header-title">Document Details</h2>
    <ul class="list-group">
        <li class="list-group-item"><span class="label">ID_DIPOTONG	:</span> {{ $document->identitas_penerima }}</li>
        <li class="list-group-item"><span class="label">NAMA :</span> {{ $document->nama_penerima }}</li>
        <li class="list-group-item"><span class="label">PASAL:</span> {{ $document->tanggal_bukti }}</li>
        <li class="list-group-item"><span class="label">KODE OBJEK PAJAK :</span> {{ $document->kode_objek_pajak }}</li>
        <li class="list-group-item"><span class="label">NO BUKTI :</span> {{ $document->no_bukti }}</li>
        <li class="list-group-item"><span class="label">TANGGAL BUPOT :</span> {{ $document->tanggal_bukti }}</li>
        <li class="list-group-item"><span class="label">PPH DIPOTONG  :</span> {{ $document->pph }}</li>
        <li class="list-group-item"><span class="label">JUMLAH BRUTO  :</span> Rp{{ number_format($document->penghasilan_bruto, 0, ',', '.') }}</li>
        <li class="list-group-item"><span class="label">KETERANGAN  :</span> {{ '-' }}</li>

    </ul>
</div>

</body>
</html>
