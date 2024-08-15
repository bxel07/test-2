<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ZipArchive;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;



class DashboardController extends Controller
{
    public function index(){
        $documents = Document::where("user_id", auth()->user()->id)
        ->select('id',
        'identitas_penerima', 
        'nama_penerima', 
        'pasal', 
        'kode_objek_pajak', 
        'no_bukti',
        'tanggal_bukti',
        'pph', 
        'penghasilan_bruto')
        ->orderBy("created_at")
        ->paginate(10); 


        
        return view("user.listDataPage",compact("documents"));
    }

    public function show($id){
        $document = Document::where("id", $id)
        ->select('id','identitas_penerima', 'nama_penerima', 'pasal', 'kode_objek_pajak', 'no_bukti','tanggal_bukti', 'pph', 'penghasilan_bruto')->first();
    
        return view('components.partials.user.document-details', compact('document'));

    }

    public function exportPDF($id){
        $document = Document::where("id", $id)
        ->select('id','npwp_pemotong','identitas_penerima', 'nama_penerima', 'pasal', 'kode_objek_pajak', 'no_bukti','tanggal_bukti', 'pph', 'penghasilan_bruto', 'id_sistem')->first();        $data = [
            'document' => $document
        ];

        // Convert integers to strings and then use substr
        $npwpPemotong = substr((string) $document->npwp_pemotong, 0, 12);
        $identitasPenerima = substr((string) $document->identitas_penerima, 0, 12);
        $idSistem = $document->id_sistem;     
        $uuidSuffix = $this->generateUuidSuffix();

        $pdfFileName = "{$npwpPemotong}_{$identitasPenerima}_{$idSistem}{$uuidSuffix}.pdf";

        $pdf = Pdf::loadView('components/export/userpdf/userpdf', $data);
        return $pdf->download($pdfFileName);
    }

    private function generateUuidSuffix()
    {
        Str::uuid();
        return '-' . substr((string) Str::uuid(), 9); // Returns the last part of a UUID
    }

    public function exportExcel($id) {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Add headers
        $headers = [
            'ID_DIPOTONG', 'NAMA', 'PASAL', 'KODE OBJEK PAJAK', 'NO BUKTI', 
            'TANGGAL BUPOT', 'PPH DIPOTONG', 'JUMLAH BRUTO', 'KETERANGAN'
        ];
    
        foreach (range('A', 'I') as $index => $column) {
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
            $document->identitas_penerima,
            $document->nama_penerima,
            $document->tanggal_bukti,
            $document->kode_objek_pajak,
            $document->no_bukti,
            $document->tanggal_bukti,
            $document->pph,
            $document->penghasilan_bruto,
            '-'
        ];
    
        // Add the data to the spreadsheet
        $sheet->fromArray($data, null, 'A2');
    
        // Auto-size columns for better readability
        foreach (range('A', 'I') as $column) {
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
                $pdf = Pdf::loadView('components/export/userpdf/userpdf', compact('document'));
                $pdfFileName = "{$fileName}.pdf";
                $zip->addFromString($pdfFileName, $pdf->output());

                // Generate Excel using PHPSpreadsheet
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $headers = [
                    'ID_DIPOTONG', 'NAMA', 'PASAL', 'KODE OBJEK PAJAK', 'NO BUKTI', 
                    'TANGGAL BUPOT', 'PPH DIPOTONG', 'JUMLAH BRUTO', 'KETERANGAN'
                ];

                // Set headers
                foreach (range('A', 'I') as $index => $column) {
                    $sheet->setCellValue($column . '1', $headers[$index]);
                }

                // Add data
                $dataArray = [
                    $document->identitas_penerima,
                    $document->nama_penerima,
                    $document->tanggal_bukti,
                    $document->kode_objek_pajak,
                    $document->no_bukti,
                    $document->tanggal_bukti,
                    $document->pph,
                    $document->penghasilan_bruto,
                    '-'
                ];
                $sheet->fromArray($dataArray, null, 'A2');

                // Auto-size columns for better readability
                foreach (range('A', 'I') as $column) {
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
        return Document::where("user_id", auth()->user()->id)->get();

    }
}
