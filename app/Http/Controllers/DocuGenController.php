<?php

namespace App\Http\Controllers;

use App\Models\ClientInformations;
use App\Models\Documents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Homeful\Contacts\Data\FlatData;
use Homeful\Contacts\Models\Contact;

class DocuGenController extends Controller
{

    /**
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function contacts_download_document($id, $document,$isView,$name){
        if (!File::exists(storage_path('app/public/converted_documents/'))) {
            File::makeDirectory(storage_path('app/public/converted_documents/'), 0755, true);
        }
        if (!File::exists(storage_path('app/public/converted_pdf/'))) {
            File::makeDirectory(storage_path('app/public/converted_pdf/'), 0755, true);
        }
        $contacts = new Contact();
        $information = $contacts->find($id);

        $document_template = Documents::find($document);
        $filePath = storage_path('app/public/' . $document_template->file_attachment);

        $templateProcessor = new TemplateProcessor($filePath);

        $ci = FlatData::fromModel($information);
        // dd($ci);
        //set values
        foreach ($ci as $key => $value) {

            $templateProcessor->setValue($key, htmlspecialchars($value??''));
//            if($key=='deputy_chief_seller_officer' || $key=='chief_seller_officer'){
//                dd(htmlspecialchars($value??''),$value, $templateProcessor);
//            }
        }

        //set image
//        $imagePath = storage_path('app/public/test_image.png');
//        $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '',now()->format('Ymd_His')."_{$document_template->name}_{$information->last_name}");
        $docx_file =storage_path("app/public/converted_documents/{$filename}_templated.docx");

        $templateProcessor->saveAs($docx_file);

        $outputFile = storage_path('app/public/converted_pdf/');
        $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($docx_file);
        exec($command, $outputFile, $return_var);
        // dd($command);
        $pdfFile = storage_path("app/public/converted_pdf/{$filename}_templated.pdf");
        // dd($pdfFile);
        if (file_exists($pdfFile)) {
//            if($isView){
//               return response()->file($pdfFile, [
//                    'Content-Type' => 'application/pdf',
//                    'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
//                ]);
//            }else{
//               return response()->download($pdfFile);
//            }
            return $isView? response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
            ]):response()->download($pdfFile);
        } else {
            return response()->json(['error' => 'An error occurred during the file conversion'], 500);
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function es_file($id){
        if (!File::exists(storage_path('app/public/converted_es/'))) {
            File::makeDirectory(storage_path('app/public/converted_es/'), 0755, true);
        }

        $contacts = new Contact();
        $information = $contacts->find($id);
        if (File::copy(base_path().'/resources/documents/es_sheets/es-pasinaya.xlsx', storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_copied.xlsx'))) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_copied.xlsx'));
            $worksheet = $spreadsheet->getActiveSheet();
            $spreadsheet->getSecurity()->setLockStructure(false);
            if($spreadsheet->getActiveSheet()->getTitle()){
//                $worksheet->setCellValue('L18','');
//                $worksheet->getCell('L18')->setCalculatedValue('');
//                dd($worksheet->getCell('L18'));
//                $worksheet->unmergeCells('B6:D6');
//                $worksheet->getCell('B6')->setValue($information->last_name.', '.$information->first_name.' '.$information->middle_name);
//                dd($worksheet->getCell('G8'));
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
//                dd($writer->save(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls')));
                $writer->save(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls'));
                dd(storage_path('app/public/converted_es/'.$information->created_at->format('Y-m-d_H-i-s').'_templated.xls'));
            }
            return "File copied successfully to temp directory.";
        } else {
            return "Failed to copy file.";
        }
    }


}
