<!-- resources/views/admin/partials/document-details.blade.php -->

<div>
    <p><strong>ID Dipotong:</strong> {{ $document->id_dipotong }}</p>
    <p><strong>Nama:</strong> {{ $document->nama }}</p>
    <p><strong>Pasal:</strong> {{ $document->pasal }}</p>
    <p><strong>Kode Objek Pajak:</strong> {{ $document->kode_objek_pajak }}</p>
    <p><strong>No Bukti Potong:</strong> {{ $document->no_bukti_potong }}</p>
    <p><strong>Tanggal Bupot:</strong> {{ $document->tanggal_bupot }}</p>
    <p><strong>PPH Dipotong:</strong> {{ $document->pph_dipotong }}</p>
    <p><strong>Jumlah Bruto:</strong> {{ $document->jumlah_bruto }}</p>
    <p><strong>Keterangan:</strong> {{ $document->keterangan }}</p>
    <p><strong>User ID:</strong> {{ $document->user_id }}</p>
    <p><strong>No Bukti:</strong> {{ $document->no_bukti }}</p>
    <p><strong>Tanggal Bukti:</strong> {{ $document->tanggal_bukti }}</p>
    <p><strong>NPWP Pemotong:</strong> {{ $document->npwp_pemotong }}</p>
    <p><strong>Nama Pemotong:</strong> {{ $document->nama_pemotong }}</p>
    <p><strong>Identitas Penerima:</strong> {{ $document->identitas_penerima }}</p>
    <p><strong>Nama Penerima:</strong> {{ $document->nama_penerima }}</p>
    <p><strong>Penghasilan Bruto:</strong> {{ $document->penghasilan_bruto }}</p>
    <p><strong>PPH:</strong> {{ $document->pph }}</p>
    <p><strong>Masa Pajak:</strong> {{ $document->masa_pajak }}</p>
    <p><strong>Periode:</strong> {{ $document->periode }}</p>
    <p><strong>Tahun Pajak:</strong> {{ $document->tahun_pajak }}</p>
    <p><strong>Status:</strong> {{ $document->status }}</p>
    <p><strong>Rev No:</strong> {{ $document->rev_no }}</p>
    <p><strong>Posting:</strong> {{ $document->posting }}</p>
    <p><strong>ID Sistem:</strong> {{ $document->id_sistem }}</p>
</div>
