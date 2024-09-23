<?php

namespace App\Livewire;

use App\Models\ClientInformations;
use App\Models\Documents;
use ConvertApi\ConvertApi;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\TemplateProcessor;

use PhpOffice\PhpWord\Settings;
use setasign\Fpdi\TcpdfFpdi;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;

class DocumentPreviewComponent extends Component
{

    public ?Model $record = null;
    public ?string $html_code = null;


    public function mount($record)
    {
        $this->record = $record;
    }

    public function render()
    {
        if ($this->record) {
            if (!File::exists(storage_path('app/public/converted_documents/'))) {
                File::makeDirectory(storage_path('app/public/converted_documents/'), 0755, true);
            }
            if (!File::exists(storage_path('app/public/converted_pdf/'))) {
                File::makeDirectory(storage_path('app/public/converted_pdf/'), 0755, true);
            }

            $filePath = storage_path('app/public/' . $this->record->file_attachment);
            $templateProcessor = new TemplateProcessor($filePath);
            if($this->record->data ){
                foreach ($this->record->data as $key => $value) {
                    $templateProcessor->setValue($key, $value);
                }
            }
//            $imagePath = storage_path('app/public/test_image.png');
//            $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));

            $docx_file =storage_path('app/public/converted_documents/'.$this->record->created_at->format('Y-m-d_H-i-s').'_'.$this->record->id.'_preview.docx');
            $templateProcessor->saveAs($docx_file);
            $outputFile = storage_path('app/public/converted_pdf/');
            $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($docx_file);
            exec($command, $output, $return_var);

        }
        return view('livewire.document-preview-component');
    }

    public function refreshPdf()
    {
        if ($this->record) {
            $filePath = storage_path('app/public/' . $this->record->file_attachment);
            $templateProcessor = new TemplateProcessor($filePath);
            if ($this->record->data) {
                foreach ($this->record->data as $key => $value) {
                    $templateProcessor->setValue($key, $value);
                }
            }
            $imagePath = storage_path('app/public/test_image.png');
            $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));
            $docx_file = storage_path('app/public/converted_documents/'.$this->record->created_at->format('Y-m-d_H-i-s').'_'.$this->record->id.'_preview.docx');
            $templateProcessor->saveAs($docx_file);
            $outputFile = storage_path('app/public/converted_pdf/');
            $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($docx_file);
            exec($command, $output, $return_var);

            // Emit an event to reload the PDF iframe
            $this->emit('reloadPdfIframe');
        }
    }

    public function streamPdf()
    {
        if ($this->record){
            $pdfFile = storage_path('app/public/converted_pdf/'.$this->record->created_at->format('Y-m-d_H-i-s').'_'.$this->record->id.'_preview.pdf');
            return response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
            ]);
        }
    }
}
