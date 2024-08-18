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
    
        try {
            // Handle Excel file
            $excelFile = $request->file('excel_file');
            $excelFileName = time() . '_' . $excelFile->getClientOriginalName();
            $excelPath = $excelFile->storeAs('uploads/excel', $excelFileName, 'public');
    
            // Handle ZIP file (optional)
            if ($request->hasFile('zip_file')) {
                $zipFile = $request->file('zip_file');
                $zipFileName = time() . '_' . $zipFile->getClientOriginalName();
                $zipPath = $zipFile->storeAs('uploads/zip', $zipFileName, 'public');
    
                // Extract ZIP file
                $zip = new ZipArchive;
                $zipFullPath = storage_path('app/public/' . $zipPath);
                if ($zip->open($zipFullPath) === TRUE) {
                    $zip->extractTo(storage_path('app/public/uploads/extracted/' . pathinfo($zipFileName, PATHINFO_FILENAME)));
                    $zip->close();
                } else {
                    throw new \Exception('Failed to open ZIP file.');
                }
            }
    
            // Read Excel file
            try {
                $spreadsheet = IOFactory::load(storage_path('app/public/' . $excelPath));
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            } catch (\Exception $e) {
                Log::error('Error reading Excel file: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to read the Excel file.');
            }
    
            // PDF and per-row Excel generation
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
            return redirect()->back()->with('success', 'Files uploaded and processed successfully.');
        } catch (\Exception $e) {
            Log::error('Error during upload process: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the upload process.');
        }
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
