<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $data;
    protected $modul;

    public function __construct($data, $modul)
    {
        $this->data = $data;
        $this->modul = $modul;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        switch ($this->modul) {
            case 'igi':
            case 'igi_master':
                return ['No DO', 'Tanggal Datang', 'Nama Barang', 'Type', 'Serial Number', 'Total Scan', 'Status Proses'];
            case 'uji_fungsi':
                return ['Serial Number', 'Nama Barang', 'Type', 'Status', 'Keterangan', 'Waktu Uji'];
            case 'repair':
                return ['Serial Number', 'Nama Barang', 'Type', 'Status', 'Jenis Kerusakan', 'Tindakan', 'Waktu Repair'];
            case 'rekondisi':
                return ['Serial Number', 'Nama Barang', 'Type', 'Tindakan', 'Waktu Rekondisi'];
            case 'service_handling':
                return ['Serial Number', 'Nama Barang', 'Type', 'Sumber', 'Status', 'Keterangan', 'Waktu Service'];
            case 'packing':
                return ['Serial Number', 'Nama Barang', 'Type', 'Kondisi Box', 'Catatan', 'Waktu Packing'];
            default:
                return ['Data'];
        }
    }

    public function map($row): array
    {
        switch ($this->modul) {
            case 'igi':
            case 'igi_master':
                return [
                    $row->no_do,
                    $row->tanggal_datang->format('d-m-Y H:i:s'),
                    $row->nama_barang,
                    $row->type,
                    $row->serial_number,
                    $row->total_scan,
                    $row->status_proses
                ];
            case 'uji_fungsi':
                return [
                    $row->igi->serial_number ?? '-',
                    $row->igi->nama_barang ?? '-',
                    $row->igi->type ?? '-',
                    $row->status,
                    $row->keterangan ?? '-',
                    $row->waktu_uji->format('d-m-Y H:i:s')
                ];
            case 'repair':
                return [
                    $row->igi->serial_number ?? '-',
                    $row->igi->nama_barang ?? '-',
                    $row->igi->type ?? '-',
                    $row->status,
                    $row->jenis_kerusakan,
                    $row->tindakan ?? '-',
                    $row->waktu_repair->format('d-m-Y H:i:s')
                ];
            case 'rekondisi':
                return [
                    $row->igi->serial_number ?? '-',
                    $row->igi->nama_barang ?? '-',
                    $row->igi->type ?? '-',
                    $row->tindakan ?? '-',
                    $row->waktu_rekondisi->format('d-m-Y H:i:s')
                ];
            case 'service_handling':
                return [
                    $row->igi->serial_number ?? '-',
                    $row->igi->nama_barang ?? '-',
                    $row->igi->type ?? '-',
                    $row->sumber,
                    $row->status,
                    $row->keterangan ?? '-',
                    $row->waktu_service->format('d-m-Y H:i:s')
                ];
            case 'packing':
                return [
                    $row->igi->serial_number ?? '-',
                    $row->igi->nama_barang ?? '-',
                    $row->igi->type ?? '-',
                    $row->kondisi_box ?? '-',
                    $row->catatan ?? '-',
                    $row->waktu_packing->format('d-m-Y H:i:s')
                ];
            default:
                return [$row];
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Get last column letter
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Style untuk header (row 1)
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'] // Biru
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style untuk data rows
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        // Return empty array karena kita pakai ShouldAutoSize
        // Tapi tetap set minimum width untuk kolom tertentu
        return [];
    }
}