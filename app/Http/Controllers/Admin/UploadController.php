<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadFile;
use App\Models\Document;
use App\Models\User;


use Illuminate\Support\Facades\Storage;
use ZipArchive;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Carbon\Carbon;

class UploadController extends Controller
{
    public function showUploadForm()
    {
        return view('admin.uploadPage');
    }public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
            'zip_file' => 'nullable|mimes:zip',
        ]);
    
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
            }
        }
    
        // Read Excel file
        $spreadsheet = IOFactory::load(storage_path('app/public/' . $excelPath));
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
    
        // Ensure directories exist
        $generatedExcelPath = storage_path('app/public/uploads/generated_excel');
        $generatedPdfPath = storage_path('app/public/uploads/generated_pdf');
        if (!is_dir($generatedExcelPath)) {
            mkdir($generatedExcelPath, 0755, true);
        }
        if (!is_dir($generatedPdfPath)) {
            mkdir($generatedPdfPath, 0755, true);
        }
    
        // Create new Excel file
        $newSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('Formatted Data');
    
        // Define header row
        $header = [
            'ID_DIPOTONG', 'NAMA', 'PASAL', 'KODE_OBJEK_PAJAK', 'NO_BUKTI_POTONG', 'TANGGAL_BUPOT', 'PPH_DIPOTONG', 'JUMLAH_BRUTO', 'KETERANGAN'
        ];
        $newSheet->fromArray($header, NULL, 'A1');
    
        // Add rows with data
        $rowNumber = 2; // Start after header
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row
            $newSheet->fromArray($row, NULL, "A$rowNumber");
            $rowNumber++;
        }
    
        // Save the formatted Excel file
        $formattedExcelFileName = 'formatted_data.xlsx';
        $formattedExcelFilePath = $generatedExcelPath . '/' . $formattedExcelFileName;
        $excelWriter = new Xlsx($newSpreadsheet);
        $excelWriter->save($formattedExcelFilePath);
    
        // PDF and per-row Excel generation
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            // Extract necessary fields
            $idDipotong = $row[0] ?? ''; // ID_DIPOTONG
            $npwpPemotong = $row[2] ?? ''; // Example column for NPWP_PEMOTONG
            $identitasPenerima = $row[3] ?? ''; // Example column for IDENTITAS_PENERIMA
            $idSystem = \Str::uuid()->toString(); // Generate a random UUID
            dd($row);

            // Convert date format from DD/MM/YYYY to YYYY-MM-DD
            $tanggalBupot = isset($row[5]) ? Carbon::createFromFormat('d/m/Y', $row[5])->format('Y-m-d') : null;
            
            // Find the user based on ID_DIPOTONG
            $user = User::where('id_dipotong', $idDipotong)->first();
            $userId = $user ? $user->id : null; // Get user ID if user exists
        
            // Format filenames
            $formattedNpwp = substr($npwpPemotong, 0, 12);
            $formattedIdentitas = substr($identitasPenerima, 0, 12);
            $randomId = \Str::uuid()->toString(); // Generate random UUID
        
            // Generate PDF file
            $pdfFileName = "{$formattedNpwp}_{$formattedIdentitas}_{$randomId}.pdf";
            $pdfFilePath = storage_path('app/public/uploads/generated_pdf/' . $pdfFileName);
        
            $pdfTemplateHtml = '
            <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        ul { list-style-type: none; padding: 0; }
                        li { margin-bottom: 10px; }
                        .label { font-weight: bold; }
                    </style>
                </head>
                <body>
                    <h1>Document for Row</h1>
                    <ul>
                        <li><span class="label">ID_DIPOTONG:</span> ' . ($row[0] ?? '') . '</li>
                        <li><span class="label">NAMA:</span> ' . ($row[1] ?? '') . '</li>
                        <li><span class="label">PASAL:</span> ' . ($row[2] ?? '') . '</li>
                        <li><span class="label">KODE_OBJEK_PAJAK:</span> ' . ($row[3] ?? '') . '</li>
                        <li><span class="label">NO_BUKTI_POTONG:</span> ' . ($row[4] ?? '') . '</li>
                        <li><span class="label">TANGGAL_BUPOT:</span> ' . ($row[5] ?? '') . '</li>
                        <li><span class="label">PPH_DIPOTONG:</span> ' . ($row[6] ?? '') . '</li>
                        <li><span class="label">JUMLAH_BRUTO:</span> ' . ($row[7] ?? '') . '</li>
                        <li><span class="label">KETERANGAN:</span> ' . ($row[8] ?? '') . '</li>
                    </ul>
                </body>
            </html>';
        
            $dompdf = new Dompdf();
            $dompdf->loadHtml($pdfTemplateHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($pdfFilePath, $dompdf->output());
        
            // Generate Excel file for each row
            $rowSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $rowSheet = $rowSpreadsheet->getActiveSheet();
            $rowSheet->fromArray([$header], NULL, 'A1'); // Add header
            $rowSheet->fromArray($row, NULL, 'A2'); // Add data row
        
            $excelFileName = "{$formattedNpwp}_{$formattedIdentitas}_{$randomId}.xlsx";
            $excelFilePath = storage_path('app/public/uploads/generated_excel/' . $excelFileName);
            $rowWriter = new Xlsx($rowSpreadsheet);
            $rowWriter->save($excelFilePath);
        
            // Save to documents table
            Document::create([
                'id_dipotong' => $row[0] ?? '',
                'nama' => $row[1] ?? '',
                'pasal' => $row[2] ?? '',
                'kode_objek_pajak' => $row[3] ?? '',
                'no_bukti_potong' => $row[4] ?? '',
                'tanggal_bupot' => $tanggalBupot, // Save formatted date
                'pph_dipotong' => $row[6] ?? 0,
                'jumlah_bruto' => $row[7] ?? 0,
                'keterangan' => $row[8] ?? '',
                'user_id' => $userId, // Set user ID based on ID_DIPOTONG
            ]);

             // Save upload information
            UploadFile::create([
                'excel_filename' => $excelFileName,
                'zip_filename' => $zipFileName ?? null,
                'npwp_pemotong' => $rows[1][2] ?? null, // Assuming the first row after header contains these values
                'nama_pemotong' => $rows[1][3] ?? null,
                'total_documents' => count($rows) - 1, // Subtract 1 for header row
            ]);
        }
    
        return redirect()->back()->with('success', 'Files uploaded and processed successfully.');
    }
    
    
    
    public function index()
    {
        $uploads = UploadFile::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.uploads_list', compact('uploads'));
    }
}
