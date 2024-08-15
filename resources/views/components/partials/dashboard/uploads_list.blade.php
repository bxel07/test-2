<div>
    <h2>Uploaded Files</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Excel Filename</th>
                <th>ZIP Filename</th>
                <th>NPWP Pemotong</th>
                <th>Nama Pemotong</th>
                <th>Total Documents</th>
                <th>Upload Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uploads as $upload)
                <tr>
                    <td>{{ $upload->excel_filename }}</td>
                    <td>{{ $upload->zip_filename }}</td>
                    <td>{{ $upload->npwp_pemotong }}</td>
                    <td>{{ $upload->nama_pemotong }}</td>
                    <td>{{ $upload->total_documents }}</td>
                    <td>{{ $upload->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $uploads->links() }}
</div>