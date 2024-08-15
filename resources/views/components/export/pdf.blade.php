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
        <li class="list-group-item"><span class="label">ID:</span> {{ $document->id }}</li>
        <li class="list-group-item"><span class="label">No Bukti:</span> {{ $document->no_bukti }}</li>
        <li class="list-group-item"><span class="label">Tanggal Bukti:</span> {{ $document->tanggal_bukti }}</li>
        <li class="list-group-item"><span class="label">NPWP Pemotong:</span> {{ $document->npwp_pemotong }}</li>
        <li class="list-group-item"><span class="label">Nama Pemotong:</span> {{ $document->nama_pemotong }}</li>
        <li class="list-group-item"><span class="label">Identitas Penerima:</span> {{ $document->identitas_penerima }}</li>
        <li class="list-group-item"><span class="label">Nama Penerima:</span> {{ $document->nama_penerima }}</li>
        <li class="list-group-item"><span class="label">Penghasilan Bruto:</span> Rp{{ number_format($document->penghasilan_bruto, 0, ',', '.') }}</li>
        <li class="list-group-item"><span class="label">PPH:</span> Rp{{ number_format($document->pph, 0, ',', '.') }}</li>
        <li class="list-group-item"><span class="label">Kode Objek Pajak:</span> {{ $document->kode_objek_pajak }}</li>
        <li class="list-group-item"><span class="label">Pasal:</span> {{ $document->pasal }}</li>
        <li class="list-group-item"><span class="label">Masa Pajak:</span> {{ $document->masa_pajak }}</li>
        <li class="list-group-item"><span class="label">Periode:</span> {{ $document->periode }}</li>
        <li class="list-group-item"><span class="label">Tahun Pajak:</span> {{ $document->tahun_pajak }}</li>
        <li class="list-group-item"><span class="label">Status:</span> {{ $document->status }}</li>
        <li class="list-group-item"><span class="label">Rev No:</span> {{ $document->rev_no }}</li>
        <li class="list-group-item"><span class="label">Posting:</span> {{ $document->posting }}</li>
        <li class="list-group-item"><span class="label">ID Sistem:</span> {{ $document->id_sistem }}</li>
        <li class="list-group-item"><span class="label">User ID:</span> {{ $document->user_id }}</li>
    </ul>
</div>

</body>
</html>
