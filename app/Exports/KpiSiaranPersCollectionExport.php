<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class KpiSiaranPersCollectionExport implements FromCollection, WithHeadings
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Semester',
            'Diisi Oleh',
            'Metode Input',
            'Judul Draft',
            'Teks Draft Release',
            'Platform',
            'Status',
            'Dibuat',
        ];
    }
}
