<?php


namespace App\Controller;


use App\Form\UploadFileType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function convertXlsxToCsvAction(Request $request): Response
    {
        try {
            $form = $this->createForm(UploadFileType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('file')->getData();

                $options = [
                    'sourceType' => 'xlsx',
                    'destType' => 'csv',
                ];

                $this->convertFileToResponse($file, XlsxReader::class, CsvWriter::class, $options);
            }
            $errors = [];
            foreach($form->getErrors(true) as $error){
                $errors[] = $error->getMessage();
            }
            return $this->json(['messages' => $errors], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function convertCsvToXlsxAction(Request $request): Response
    {
        try {
            $form = $this->createForm(UploadFileType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('file')->getData();

                $options = [
                    'sourceType' => 'csv',
                    'destType' => 'xlsx',
                ];

                $this->convertFileToResponse($file, CsvReader::class, XlsxWriter::class, $options);
            }
            $errors = [];
            foreach($form->getErrors(true) as $error){
                $errors[] = $error->getMessage();
            }
            return $this->json(['messages' => $errors], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function convertFileToResponse(UploadedFile $file, $readerClass, $writerClass, array $options = []) : void
    {
        $reader = new $readerClass();

        if ($options['sourceType'] === 'csv') {
            $reader->setDelimiter($options['delimiter'] ?? ',');
            $reader->setEnclosure($opions['enclosure'] ?? '"');
        }

        $csvObj = $reader->load($file->getPathname());

        $fileName = 'export_'.date('YmdHis').'.'.$options['destType'];
        $writer = new $writerClass($csvObj);
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}