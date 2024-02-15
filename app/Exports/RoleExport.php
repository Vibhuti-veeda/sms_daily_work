<?php

namespace App\Exports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;


class RoleExport implements FromCollection, WithHeadings, WithStyles, WithEvents 
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {

        $roles = Role::with(['defined_module.module_name'])
                    ->where('is_delete', 0)
                    ->get();
        /*echo "<pre>";
        print_r($roles->toArray());
        exit; */  
            
        $data = $roles->map(function ($role) {
            return [
                'ID' => $role->id,
                'Name' => $role->name,
                'Modules' => $role->defined_module->implode('module_name.name', ' | '),
                'Status' => $role->is_active
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Modules', // Assuming this is the name of the module
            'Status',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        // Define custom styles for the first row (heading row)
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

     /**
     * @param Worksheet $sheet
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Set the title
                $event->sheet->setCellValue('A1', 'All Roles | Study Management System');
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add the headings
                $event->sheet->setCellValue('A2', 'ID');
                $event->sheet->setCellValue('B2', 'Name');
                $event->sheet->setCellValue('C2', 'Modules');
                $event->sheet->setCellValue('D2', 'Status');

                // Apply styles to the headings row
                $event->sheet->getStyle('A2:D2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd9d9d9']],
                ]);

                // Add a border around the headings row
                $event->sheet->getStyle('A2:D2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Start writing data from row 3
                $startRow = 3;
                foreach ($this->collection() as $row) {
                    $event->sheet->setCellValue('A' . $startRow, $row['ID']);
                    $event->sheet->setCellValue('B' . $startRow, $row['Name']);
                    $event->sheet->setCellValue('C' . $startRow, $row['Modules']);
                    $event->sheet->setCellValue('D' . $startRow, $row['Status']);
                    $startRow++;
                }
            },
        ];
    }
}
