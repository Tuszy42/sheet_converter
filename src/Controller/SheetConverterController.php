<?php


namespace App\Controller;


use App\Form\UploadFileType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SheetConverterController extends AbstractController
{
    public function indexAction(): Response
    {
        $uploadLimit = $this->getParameter('upload_size_limit');

        $xlsxForm = $this->createForm(UploadFileType::class, null, [
            'maxSize' => $uploadLimit,
            'mimeTypes' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '.xlsx'],
            'actionUrl' => '/xlsx_to_csv'
        ]);

        $csvForm = $this->createForm(UploadFileType::class, null, [
            'maxSize' => $uploadLimit,
            'mimeTypes' => ['text/csv', '.csv'],
            'actionUrl' => '/csv_to_xlsx'
        ]);

        return $this->render('index.html.twig', [
            'xlsxForm' => $xlsxForm->createView(),
            'csvForm' => $csvForm->createView(),
            'config' => [
                'UPLOAD_FILESIZE_LIMIT' => $uploadLimit
            ]
        ]);
    }

    public function convertXlsxToCsvAction(Request $request): Response|string
    {
        try {
            $form = $this->createForm(UploadFileType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('file')->getData();

                $options = [
                    'type' => 'xlsx',
                    'filename' => 'new.csv',
                ];

                return $this->convertFileToResponse($file, XlsxReader::class, CsvWriter::class, $options);
            }
            return $this->json(['success' => 'not great']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function convertCsvToXlsxAction(Request $request): Response|string
    {
        try {
            $form = $this->createForm(UploadFileType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('file')->getData();

                $options = [
                    'sourceType' => 'csv',
                    'destType' => 'xlsx',
                    'filename' => 'new.xlsx',
                ];

                return $this->convertFileToResponse($file, CsvReader::class, XlsxWriter::class, $options);
            }
            return $this->json(['success' => 'not great']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function convertFileToResponse(UploadedFile $file, $readerClass, $writerClass, array $options = []): Response
    {
        $reader = new $readerClass();

        if ($options['sourceType'] === 'csv') {
            $reader->setDelimiter($options['delimiter'] ?? ',');
            $reader->setEnclosure($opions['enclosure'] ?? '"');
        }

        $csvObj = $reader->load($file->getPathname());

        $fileName = 'tmp/temp_'.date('YmdHis').'.'.$options['destType'];

        $writer = new $writerClass($csvObj);
        $writer->save($fileName);

        $response  = new StreamedResponse(function() use ($fileName){
            $outputStream = fopen('php://output', 'wb');
            $inputStream = fopen($fileName,'r');
            stream_copy_to_stream($inputStream, $outputStream);
            fclose($outputStream);
            fclose($inputStream);
        });

        $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}