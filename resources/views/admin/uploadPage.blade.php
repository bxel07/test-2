<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Upload Files</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="form-group">
                <label for="excel_file" class="block text-sm font-medium text-gray-700">Excel File:</label>
                <input type="file" name="excel_file" id="excel_file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('excel_file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="zip_file" class="block text-sm font-medium text-gray-700">ZIP File (Optional):</label>
                <input type="file" name="zip_file" id="zip_file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                @error('zip_file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full px-4 py-2 border border-blue-600 text-blue-600 font-semibold rounded-md shadow-sm bg-white hover:bg-blue-600 hover:text-white hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                Upload
            </button>        
        </form>
    </div>
</x-app-layout>
