<x-app-layout>
    <h2>Upload Files</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="excel_file">Excel File:</label>
            <input type="file" name="excel_file" id="excel_file">
        </div>
        <div class="form-group">
            <label for="zip_file">ZIP File:</label>
            <input type="file" name="zip_file" id="zip_file">
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</x-app-layout>
