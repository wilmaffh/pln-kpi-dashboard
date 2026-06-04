<?php

namespace App\Filament\Exports;

use App\Models\KpiSubmission;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class KpiSiaranPersExporter extends Exporter
{
    protected static ?string $model = KpiSubmission::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('semester.nama_semester')
                ->label('Semester'),
            ExportColumn::make('user.name')
                ->label('Diisi Oleh'),
            ExportColumn::make('metode_input')
                ->label('Metode Input'),
            ExportColumn::make('siaranPers.judul_draft')
                ->label('Judul Draft'),
            ExportColumn::make('siaranPers.teks_draft_release')
                ->label('Teks Draft Release'),
            ExportColumn::make('siaranPers.platform_pengiriman')
                ->label('Platform'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('created_at')
                ->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Data Siaran Pers berhasil diekspor.';
    }
}
