<?php
// app/Exports/DataExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
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
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'No. IDO',
                    'Tanggal Datang',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Serial Number',
                    'MAC Address',
                    'STB ID',
                    'Scan Time',
                    'Scan By',
                    'Status Proses'
                ];

            case 'uji_fungsi':
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'Serial Number',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Result',
                    'Uji Fungsi Time',
                    'User'
                ];

            case 'repair':
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'Serial Number',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Jenis Kerusakan',
                    'Result',
                    'Repair Time',
                    'User',
                    'Catatan'
                ];

            case 'rekondisi':
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'Serial Number',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Rekondisi Time',
                    'User'
                ];

            case 'service_handling':
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'Serial Number',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Service Time',
                    'User',
                    'Catatan'
                ];

            case 'packing':
                return [
                    'No',
                    'Pemilik',
                    'Wilayah',
                    'Serial Number',
                    'Jenis',
                    'Merk',
                    'Type',
                    'Packing Time',
                    'Kondisi Box',
                    'User',
                    'Catatan'
                ];

            default:
                return ['Data'];
        }
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        switch ($this->modul) {
            case 'igi':
                return [
                    $no,
                    $row->bapb->pemilik ?? '-',
                    $row->bapb->wilayah ?? '-',
                    $row->bapb->no_ido ?? '-',
                    $row->bapb->tanggal_datang->format('d-m-Y') ?? '-',
                    $row->jenis,
                    $row->merk,
                    $row->type,
                    $row->serial_number,
                    $row->mac_address,
                    $row->stb_id ?? '-',
                    $row->scan_time->format('d-m-Y H:i:s'),
                    $row->scanner->name ?? '-',
                    $row->status_proses
                ];

            case 'uji_fungsi':
                return [
                    $no,
                    $row->igiDetail->bapb->pemilik ?? '-',
                    $row->igiDetail->bapb->wilayah ?? '-',
                    $row->igiDetail->serial_number ?? '-',
                    $row->igiDetail->jenis ?? '-',
                    $row->igiDetail->merk ?? '-',
                    $row->igiDetail->type ?? '-',
                    $row->result,
                    $row->uji_fungsi_time->format('d-m-Y H:i:s'),
                    $row->user->name ?? '-'
                ];

            case 'repair':
                return [
                    $no,
                    $row->igiDetail->bapb->pemilik ?? '-',
                    $row->igiDetail->bapb->wilayah ?? '-',
                    $row->igiDetail->serial_number ?? '-',
                    $row->igiDetail->jenis ?? '-',
                    $row->igiDetail->merk ?? '-',
                    $row->igiDetail->type ?? '-',
                    $row->jenis_kerusakan,
                    $row->result,
                    $row->repair_time->format('d-m-Y H:i:s'),
                    $row->user->name ?? '-',
                    $row->catatan ?? '-'
                ];

            case 'rekondisi':
                return [
                    $no,
                    $row->igiDetail->bapb->pemilik ?? '-',
                    $row->igiDetail->bapb->wilayah ?? '-',
                    $row->igiDetail->serial_number ?? '-',
                    $row->igiDetail->jenis ?? '-',
                    $row->igiDetail->merk ?? '-',
                    $row->igiDetail->type ?? '-',
                    $row->rekondisi_time->format('d-m-Y H:i:s'),
                    $row->user->name ?? '-'
                ];

            case 'service_handling':
                return [
                    $no,
                    $row->igiDetail->bapb->pemilik ?? '-',
                    $row->igiDetail->bapb->wilayah ?? '-',
                    $row->igiDetail->serial_number ?? '-',
                    $row->igiDetail->jenis ?? '-',
                    $row->igiDetail->merk ?? '-',
                    $row->igiDetail->type ?? '-',
                    $row->service_time->format('d-m-Y H:i:s'),
                    $row->user->name ?? '-',
                    $row->catatan ?? '-'
                ];

            case 'packing':
                return [
                    $no,
                    $row->igiDetail->bapb->pemilik ?? '-',
                    $row->igiDetail->bapb->wilayah ?? '-',
                    $row->igiDetail->serial_number ?? '-',
                    $row->igiDetail->jenis ?? '-',
                    $row->igiDetail->merk ?? '-',
                    $row->igiDetail->type ?? '-',
                    $row->packing_time->format('d-m-Y H:i:s'),
                    $row->kondisi_box ?? '-',
                    $row->user->name ?? '-',
                    $row->catatan ?? '-'
                ];

            default:
                return [$row];
        }
    }

    public function title(): string
    {
        return ucfirst(str_replace('_', ' ', $this->modul));
    }
}