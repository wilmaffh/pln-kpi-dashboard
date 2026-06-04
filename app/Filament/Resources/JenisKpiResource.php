<?php

namespace App\Filament\Resources;
 
use App\Filament\Resources\JenisKpiResource\Pages;
use App\Models\JenisKpi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
 
class JenisKpiResource extends Resource
{
    protected static ?string $model           = JenisKpi::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Jenis KPI';
    protected static ?int    $navigationSort  = 2;
 
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
 
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('nama_kpi')->label('Nama KPI')->required(),
                Forms\Components\TextInput::make('kode_kpi')
                    ->label('Kode KPI')->required()->unique(ignoreRecord: true)
                    ->helperText('Huruf kecil, gunakan underscore. Contoh: laporan_csr')
                    ->regex('/^[a-z_]+$/'),
                Forms\Components\Select::make('tipe_input')
                    ->label('Tipe Input')
                    ->options(['detail_form' => 'Form Detail', 'file_only' => 'Upload File Saja'])
                    ->required(),
                Forms\Components\TextInput::make('urutan')->numeric()->default(10),
                Forms\Components\Toggle::make('is_active')->label('Aktif?')->default(true),
            ]),
        ]);
    }
 
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('urutan')->label('#')->sortable()->width(50),
            Tables\Columns\TextColumn::make('nama_kpi')->label('Nama KPI')->searchable(),
            Tables\Columns\TextColumn::make('kode_kpi')->label('Kode')->badge()->color('gray'),
            Tables\Columns\BadgeColumn::make('tipe_input')
                ->formatStateUsing(fn($s) => $s === 'detail_form' ? 'Form Detail' : 'File Only')
                ->colors(['success' => 'detail_form', 'warning' => 'file_only']),
            Tables\Columns\IconColumn::make('is_default')->label('Default?')->boolean(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif?')->boolean(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->before(function (JenisKpi $r) {
                    if ($r->is_default) {
                        \Filament\Notifications\Notification::make()
                            ->title('KPI default tidak bisa dihapus!')->danger()->send();
                        $this->halt();
                    }
                }),
        ])
        ->defaultSort('urutan');
    }
 
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJenisKpis::route('/'),
            'create' => Pages\CreateJenisKpi::route('/create'),
            'edit'   => Pages\EditJenisKpi::route('/{record}/edit'),
        ];
    }
}
 
namespace App\Filament\Resources\JenisKpiResource\Pages;
use App\Filament\Resources\JenisKpiResource;
use Filament\Actions;
use Filament\Resources\Pages\{ListRecords, CreateRecord, EditRecord};
class ListJenisKpis extends ListRecords {
    protected static string $resource = JenisKpiResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
class CreateJenisKpi extends CreateRecord {
    protected static string $resource = JenisKpiResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
class EditJenisKpi extends EditRecord {
    protected static string $resource = JenisKpiResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}