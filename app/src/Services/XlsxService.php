<?php


namespace App\Services;

use App\Repository\UsersRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class XlsxService extends AbstractController
{

    public function generate($users)
    {
        $headers = [
            "Фамилия",
            "Имя",
            "Отчество",
            "Место работы",
            "Должность",
            "Телефон",
            "Электронная почта",
            "Город",
            "Страна",
            "Пришел",
        ];

        $date = new \DateTime();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Пользователь xslx");
        foreach($headers as $k=>$v){
            $sheet->setCellValueByColumnAndRow($k, 1, $v);
        }
        $i=2;
        $key=0;
        foreach ($users as $user) {
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getLastname());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getFirstname());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getMiddlename());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getJob());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getPosition());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getPhone());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getEmail());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getCity());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getCountry());
            $sheet->setCellValueByColumnAndRow($key++, $i, $user->getActive());
            $i++;
            $key=0;
        }
//        $sheet->setCellValue('A1', 'Содержимое ячейки А1');
        $writer = new Xlsx($spreadsheet);
        $fileName = $date->format('Y-m-d\TH:i:s').'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file(
            $temp_file,
            $fileName,
            ResponseHeaderBag::DISPOSITION_INLINE
        );
    }

}