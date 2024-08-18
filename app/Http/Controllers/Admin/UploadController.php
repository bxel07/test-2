<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ZipArchive;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Str;


class UploadController extends Controller
{
    public function showUploadForm()
    {
        return view('admin.uploadPage');
    }
    
    public function upload(Request $request){
        $request->validate([
            'excel_file' => 'nullable|mimes:xlsx,xls',
            'zip_file' => 'nullable|mimes:zip',
        ]);

        // check mapped post file 
        if( $request->hasFile('excel_file') ){
            return $this->mapExcel($request);
        }else{
           return  $this->mapZip($request);
        }
    }


    private function mapExcel(Request $request){
        try {
            // Handle Excel file
            $excelFile = $request->file('excel_file');
            $excelFileName = time() . '_' . $excelFile->getClientOriginalName();
            $excelPath = $excelFile->storeAs('uploads/excel', $excelFileName, 'public');

            // Read Excel file
            try {
                $spreadsheet = IOFactory::load(storage_path('app/public/' . $excelPath));
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            } catch (\Exception $e) {
                Log::error('Error reading Excel file: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to read the Excel file.');
            }
    
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header row
                $tanggalBupot = isset($row[1]) ? Carbon::createFromFormat('d/m/Y', $row[1])->format('Y-m-d') : null;
    
                // Find the user based on ID_DIPOTONG
                $user = User::where('npwp', $row[4])->first();
    
                if (!$user) {
                    Log::warning('No user found for NPWP: ' . ($row[4] ?? ''));
                    continue; // Skip this row if user is not found
                }
    
                // Save to documents table
                try {
                    Document::create([
                        'no_bukti' => $row[0] ?? '' ,
                        'tanggal_bukti' => $tanggalBupot,
                        'npwp_pemotong' => $row[2] ?? '',
                        'nama_pemotong'=> $row[3] ?? '',

                        'identitas_penerima' => $row[4] ?? '',
                        'nama_penerima' => $row[5] ?? '',
                        'penghasilan_bruto' => $row[6] ?? '',
                        'pph' => $row[7] ?? '',

                        'kode_objek_pajak' => $row[8] ?? '',
                        'pasal' => $row[9] ?? '',
                        'masa_pajak' => $row[10] ?? '',
                        'periode' => $row[11] ?? '',
                        'tahun_pajak' => $row[12] ?? '',

                        'status' => $row[13] ?? '',
                        'rev_no' => $row[14] ?? '',
                        'posting' => $row[15] ?? '',
                        'id_sistem'  => $row[16] ?? '',
                        'user_id' => $user->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error saving document to database: ' . $e->getMessage());
                    continue; // Skip this row if saving to database fails
                }
            }
            return redirect()->route('admin.upload.form')->with('success','An upload file success');
        } catch (\Exception $e) {
            Log::error('Error during upload process: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the upload process.');
        }
    }

    public function mapZip(Request $request)
    {
        try {
            // Handle ZIP file upload and extraction
            $extractPath = $this->handleZipFile($request->file('zip_file'));

            // Process extracted PDF files
            $this->processExtractedDirectories($extractPath);

            return redirect()->route('admin.upload.form')->with('success', 'An upload file success');
        } catch (\Exception $e) {
            Log::error('Error in mapZip function: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the ZIP file.'], 500);
        }
    }

    private function handleZipFile($zipFile)
    {
        if (!$zipFile) {
            throw new \Exception('No ZIP file uploaded.');
        }

        $zipFileName = time() . '_' . $zipFile->getClientOriginalName();
        $zipPath = $zipFile->storeAs('uploads/zip', $zipFileName, 'public');

        // Create a unique directory for extraction
        $uniqueDir = 'extracted_' . Str::random(10) . '_' . time();
        $extractPath = storage_path('app/public/uploads/' . $uniqueDir);

        // Extract the ZIP file
        $this->extractZip($zipPath, $extractPath);

        return $extractPath;
    }

    private function extractZip($zipPath, $extractPath)
    {
        $zip = new ZipArchive;
        $zipFullPath = storage_path('app/public/' . $zipPath);

        if ($zip->open($zipFullPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new \Exception('Failed to open ZIP file.');
        }
    }

    private function processExtractedDirectories($extractPath)
    {
        $parser = new \Smalot\PdfParser\Parser();
        $extractedDirs = array_diff(scandir($extractPath), ['.', '..']);

        foreach ($extractedDirs as $dir) {
            $dirPath = $extractPath . '/' . $dir;

            if (is_dir($dirPath)) {
                $this->processDirectory($dirPath, $parser);
            } else {
                Log::warning('Expected a directory but found: ' . $dirPath);
            }
        }
    }

    private function processDirectory($dirPath, $parser)
    {
        $pdfFiles = glob($dirPath . '/*.pdf');

        if (empty($pdfFiles)) {
            Log::warning('No PDF files found in directory: ' . $dirPath);
            return;
        }

        foreach ($pdfFiles as $pdfPath) {
            $this->processPdfFile($pdfPath, $parser);
        }
    }

    private function processPdfFile($pdfPath, $parser)
    {
        try {
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();
            Log::info('Extracted text from PDF: ' . $text);

            $data = $this->extractDataFromText($text);
            $mappedData = $this->mapDataToFields($data);

            if (!empty($mappedData['no_bukti'])) {
                Document::updateOrCreate(
                    ['no_bukti' => $mappedData['no_bukti']],
                    $mappedData
                );
            } else {
                Log::warning('No valid data found in PDF: ' . $pdfPath);
            }
        } catch (\Exception $e) {
            Log::error('Error processing PDF file: ' . $pdfPath . ' - ' . $e->getMessage());
        }
    }

    private function extractDataFromText($text)
    {
        $lines = explode("\n", $text);
        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Convert currency strings to numeric values
                if (strpos($value, 'Rp') !== false) {
                    $value = str_replace(['Rp', '.', ','], '', $value);
                    $value = (int) $value;
                }

                $data[$key] = $value;
            }
        }

        return array_change_key_case($data, CASE_LOWER);
    }

    private function mapDataToFields($data)
    {
        return [
            'no_bukti' => $data['no bukti'] ?? null,
            'tanggal_bukti' => $data['tanggal bukti'] ?? null,
            'npwp_pemotong' => $data['npwp pemotong'] ?? null,
            'nama_pemotong' => $data['nama pemotong'] ?? null,
            'identitas_penerima' => $data['identitas penerima'] ?? null,
            'nama_penerima' => $data['nama penerima'] ?? null,
            'penghasilan_bruto' => $data['penghasilan bruto'] ?? null,
            'pph' => $data['pph'] ?? null,
            'kode_objek_pajak' => $data['kode objek pajak'] ?? null,
            'pasal' => $data['pasal'] ?? null,
            'masa_pajak' => $data['masa pajak'] ?? null,
            'periode' => $data['periode'] ?? null,
            'tahun_pajak' => $data['tahun pajak'] ?? null,
            'status' => $data['status'] ?? null,
            'rev_no' => $data['rev no'] ?? null,
            'posting' => $data['posting'] ?? null,
            'id_sistem' => $data['id sistem'] ?? null,
            'user_id' => $data['user id'] ?? null,
        ];
    }

    // Optional cleanup function
    private function cleanupExtractedFiles($path)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
    
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
    
        rmdir($path);
    }


    
    public function index()
    {
        $documents = Document::orderBy('created_at', 'asc')->paginate(10);
        return view('admin.listDataPage', compact('documents'));       
    }


    public function exportPdf($id)
    {
        $document = Document::findOrFail($id);
        $data = [
            'document' => $document
        ];

        // Convert integers to strings and then use substr
        $npwpPemotong = substr((string) $document->npwp_pemotong, 0, 12);
        $identitasPenerima = substr((string) $document->identitas_penerima, 0, 12);
        $idSistem = $document->id_sistem;     
        $uuidSuffix = $this->generateUuidSuffix();

        $pdfFileName = "{$npwpPemotong}_{$identitasPenerima}_{$idSistem}{$uuidSuffix}.pdf";

        $pdf = Pdf::loadView('components/export/pdf', $data);
        return $pdf->download($pdfFileName);        
    }

    private function generateUuidSuffix()
    {
        Str::uuid();
        return '-' . substr((string) Str::uuid(), 9); // Returns the last part of a UUID
    }

    public function exportExcel($id)
    {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = [
            'ID', 'No Bukti', 'Tanggal Bukti', 'NPWP Pemotong', 'Nama Pemotong',
            'Identitas Penerima', 'Nama Penerima', 'Penghasilan Bruto', 'PPH',
            'Kode Objek Pajak', 'Pasal', 'Masa Pajak', 'Periode', 'Tahun Pajak',
            'Status', 'Rev No', 'Posting', 'ID Sistem', 'User ID', 'Created At', 'Updated At'
        ];

        foreach (range('A', 'U') as $index => $column) {
            $sheet->setCellValue($column . '1', $headers[$index]);
        }

        // Fetch the document by ID
        $document = Document::findOrFail($id);

        // Convert integers to strings and then use substr
        $npwpPemotong = substr((string) $document->npwp_pemotong, 0, 12);
        $identitasPenerima = substr((string) $document->identitas_penerima, 0, 12);
        $idSistem = $document->id_sistem;     
        $uuidSuffix = $this->generateUuidSuffix();

        // Generate file name
        $xelFileName = "{$npwpPemotong}_{$identitasPenerima}_{$idSistem}{$uuidSuffix}.xlsx";

        // Prepare the data array
        $data = [
            $document->id,
            $document->no_bukti,
            $document->tanggal_bukti,
            $document->npwp_pemotong,
            $document->nama_pemotong,
            $document->identitas_penerima,
            $document->nama_penerima,
            $document->penghasilan_bruto,
            $document->pph,
            $document->kode_objek_pajak,
            $document->pasal,
            $document->masa_pajak,
            $document->periode,
            $document->tahun_pajak,
            $document->status,
            $document->rev_no,
            $document->posting,
            $document->id_sistem,
            $document->user_id,
            $document->created_at,
            $document->updated_at,
        ];

        // Add the data to the spreadsheet
        $sheet->fromArray($data, null, 'A2');

        // Auto-size columns for better readability
        foreach (range('A', 'U') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save the spreadsheet to a file
        $filePath = storage_path('app/public/' . $xelFileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Return the file as a download response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function batchDownload()
    {
        // Define a unique filename for the ZIP
        $zipFileName = 'batch_download_' . time() . '.zip';

        // Path where the ZIP file will be stored
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // Ensure the directory exists
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        // Create a new ZIP file
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Fetch your data
            $data = $this->getDataForBatch(); // This method should return the data you need

            foreach ($data as $document) {
                // Convert integers to strings and then use substr
                $npwpPemotong = substr((string) $document->npwp_pemotong, 0, 12);
                $identitasPenerima = substr((string) $document->identitas_penerima, 0, 12);
                $idSistem = $document->id_sistem;
                $uuidSuffix = $this->generateUuidSuffix();

                // Generate file base name
                $fileName = "{$npwpPemotong}_{$identitasPenerima}_{$idSistem}{$uuidSuffix}";

                // Generate PDF
                $pdf = Pdf::loadView('components/export/pdf', compact('document'));
                $pdfFileName = "{$fileName}.pdf";
                $zip->addFromString($pdfFileName, $pdf->output());

                // Generate Excel using PHPSpreadsheet
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $headers = [
                    'ID', 'No Bukti', 'Tanggal Bukti', 'NPWP Pemotong', 'Nama Pemotong',
                    'Identitas Penerima', 'Nama Penerima', 'Penghasilan Bruto', 'PPH',
                    'Kode Objek Pajak', 'Pasal', 'Masa Pajak', 'Periode', 'Tahun Pajak',
                    'Status', 'Rev No', 'Posting', 'ID Sistem', 'User ID', 'Created At', 'Updated At'
                ];

                // Set headers
                foreach (range('A', 'U') as $index => $column) {
                    $sheet->setCellValue($column . '1', $headers[$index]);
                }

                // Add data
                $dataArray = [
                    $document->id,
                    $document->no_bukti,
                    $document->tanggal_bukti,
                    $document->npwp_pemotong,
                    $document->nama_pemotong,
                    $document->identitas_penerima,
                    $document->nama_penerima,
                    $document->penghasilan_bruto,
                    $document->pph,
                    $document->kode_objek_pajak,
                    $document->pasal,
                    $document->masa_pajak,
                    $document->periode,
                    $document->tahun_pajak,
                    $document->status,
                    $document->rev_no,
                    $document->posting,
                    $document->id_sistem,
                    $document->user_id,
                    $document->created_at,
                    $document->updated_at,
                ];
                $sheet->fromArray($dataArray, null, 'A2');

                // Auto-size columns for better readability
                foreach (range('A', 'U') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Write Excel file to a temporary location
                $excelFileName = "{$fileName}.xlsx";
                $tempExcelPath = storage_path('app/temp/' . $excelFileName);

                // Ensure the temp directory exists
                if (!file_exists(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save($tempExcelPath);

                // Add the Excel file to the ZIP
                $zip->addFile($tempExcelPath, $excelFileName);

                // Clean up the spreadsheet object to free up memory
                $spreadsheet->disconnectWorksheets();
                
            }

            // Close the ZIP file
            $zip->close();

            // delete temporary file 
            unset($spreadsheet);
            unlink($tempExcelPath);

            // Return the ZIP file for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create ZIP file.');
    }

    private function getDataForBatch()
    {
        // Fetch the data you need
        return Document::all();
    }

    public function show($id)
    {
        // Find the document by ID
        $document = Document::findOrFail($id);

        // Return the document data as JSON
        return view('components.partials.dashboard.document-details', compact('document'));
    }
}
