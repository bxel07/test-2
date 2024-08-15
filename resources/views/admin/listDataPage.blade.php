<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Documents List</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <a href="{{ route('batch.download') }}" class="btn btn-primary">Download All as ZIP</a>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No Bukti
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Bukti
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            NPWP Pemotong
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Pemotong
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Identitas Penerima
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Penerima
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penghasilan Bruto
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            PPH
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode Objek Pajak
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pasal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Masa Pajak
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Periode
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tahun Pajak
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rev No
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posting
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Sistem
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($documents as $document)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->no_bukti }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->tanggal_bukti }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->npwp_pemotong }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->nama_pemotong }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->identitas_penerima }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->nama_penerima }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->penghasilan_bruto }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->pph }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->kode_objek_pajak }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->pasal }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->masa_pajak }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->periode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->tahun_pajak }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->rev_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->posting }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $document->id_sistem }}</td>
                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2 items-center">
                                <!-- View Button -->
                                <button onclick="showDetail({{ $document->id }})" class="text-blue-500 hover:text-blue-700 text-sm">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>

                                <!-- Export to Excel Button -->
                                <a href="{{ route('documents.export.excel', ['id' => $document->id]) }}" class="text-blue-500 hover:text-blue-700 text-sm" title="Export to Excel">
                                    <i class="fas fa-file-excel text-lg"></i>
                                </a>

                                <!-- Export to PDF Button -->
                                <a href="{{ route('documents.export.pdf', ['id' => $document->id]) }}" class="text-green-500 hover:text-green-700 text-sm" title="Export to PDF">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Detail Section -->
        <div id="detail-section" class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg hidden">
            <h3 class="text-lg font-semibold mb-2">Document Details</h3>
            <div id="detail-content" class="text-gray-700">
                <!-- Document details will be loaded here -->
            </div>
            <button onclick="closeDetail()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-gray-400">
                Close
            </button>
        </div>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>

    <!-- JavaScript to Handle Detail Display -->
    <script>
        function showDetail(id) {
            const detailSection = document.getElementById('detail-section');
            const detailContent = document.getElementById('detail-content');

            // Fetch document details
            fetch('/documents/' + id)
                .then(response => response.text())
                .then(html => {
                    detailContent.innerHTML = html;
                    detailSection.classList.remove('hidden');
                });
        }

        function closeDetail() {
            const detailSection = document.getElementById('detail-section');
            detailSection.classList.add('hidden');
        }
    </script>
</x-app-layout>
