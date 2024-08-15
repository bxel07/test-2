<!-- resources/views/admin/partials/document-details.blade.php -->
<div>
    <p><strong>ID Dipotong:</strong> {{ $document->identitas_penerima }}</p>
    <p><strong>Nama:</strong> {{ $document->nama_penerima }}</p>
    <p><strong>Pasal:</strong> {{ $document->pasal }}</p>
    <p><strong>Kode Objek Pajak:</strong> {{ $document->kode_objek_pajak }}</p>
    <p><strong>No Bukti Potong:</strong> {{ $document->no_bukti }}</p>
    <p><strong>Tanggal Bupot:</strong> {{ $document->tanggal_bukti }}</p>
    <p><strong>PPH Dipotong:</strong> {{ $document->pph }}</p>
    <p><strong>Jumlah Bruto:</strong> {{ $document->penghasilan_bruto }}</p>
    <p><strong>Keterangan:</strong> {{ "-" }}</p>
</div>
