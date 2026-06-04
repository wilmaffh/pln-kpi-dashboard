<?php
// FILE: app/Filament/Resources/KpiSiaranPersResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\KpiSiaranPersResource\Pages;
use App\Models\{KpiSubmission, KpiSiaranPers, Semester, JenisKpi};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;


class KpiSiaranPersResource extends FilamentResource
{
    protected static ?string $model           = KpiSubmission::class;
    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Input Data KPI';
    protected static ?string $navigationLabel = '1 · Siaran Pers';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $modelLabel      = 'Siaran Pers';
    protected static ?string $pluralModelLabel = 'Data Siaran Pers';

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()
            ->whereHas('jenisKpi', fn($q) => $q->where('kode_kpi', 'siaran_pers'))
            ->with(['semester', 'user', 'siaranPers']);

        if (auth()->user()?->isUser()) {
            $q->where('user_id', auth()->id());
        }

        return $q;
    }

    public static function form(Form $form): Form
    {
        $semAktif = Semester::getActive();

        return $form->schema([
            // ── INFO PENGISIAN ─────────────────────────────────────────────
            Forms\Components\Section::make('Informasi Pengisian')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('semester_id')
                        ->label('Semester')
                        ->options(Semester::where('is_active', true)->pluck('nama_semester', 'id'))
                        ->default($semAktif?->id)
                        ->required()
                        ->disabled(fn(string $context) => $context === 'edit'),

                    Forms\Components\Select::make('bulan_header')
                        ->label('Bulan Data')
                        ->options([
                            1=>'Januari', 2=>'Februari', 3=>'Maret',
                            4=>'April',   5=>'Mei',      6=>'Juni',
                            7=>'Juli',    8=>'Agustus',  9=>'September',
                            10=>'Oktober',11=>'November',12=>'Desember',
                        ])
                        ->required(),

                    Forms\Components\Hidden::make('jenis_kpi_id')
                        ->default(fn() => JenisKpi::where('kode_kpi', 'siaran_pers')->value('id')),
                    Forms\Components\Hidden::make('user_id')
                        ->default(fn() => auth()->id()),
                    Forms\Components\Hidden::make('status')
                        ->default('draft'),
                ]),

            // ── PILIHAN METODE INPUT ───────────────────────────────────────
            Forms\Components\Tabs::make('Metode Input')
                ->columnSpanFull()
                ->tabs([

                    // ═══ OPSI 1: FORM DETAIL ══════════════════════════════
                    Forms\Components\Tabs\Tab::make('📝 Opsi 1 — Form Detail')
                        ->schema([
                            Forms\Components\Hidden::make('metode_input')->default('detail_form'),

                            Forms\Components\Section::make('Detail Siaran Pers')
                                ->relationship('siaranPers')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Select::make('bulan')
                                        ->label('Bulan')
                                        ->options([
                                            1=>'Januari',2=>'Februari',3=>'Maret',
                                            4=>'April',5=>'Mei',6=>'Juni',
                                            7=>'Juli',8=>'Agustus',9=>'September',
                                            10=>'Oktober',11=>'November',12=>'Desember',
                                        ])->required(),

                                    Forms\Components\Select::make('platform_pengiriman')
                                        ->label('Platform Pengiriman')
                                        ->options(['WhatsApp'=>'WhatsApp','Email'=>'Email','Lainnya'=>'Lainnya'])
                                        ->default('WhatsApp'),

                                    Forms\Components\TextInput::make('judul_draft')
                                        ->label('Judul Draft')
                                        ->required()
                                        ->columnSpanFull()
                                        ->placeholder('Contoh: PLN UP3 Garut Sukseskan Program...'),

                                    Forms\Components\RichEditor::make('teks_draft_release')
                                        ->label('Teks Draft Release')
                                        ->required()
                                        ->columnSpanFull()
                                        ->toolbarButtons(['bold','italic','bulletList','orderedList','undo','redo']),

                                    Forms\Components\DatePicker::make('tanggal_kirim')
                                        ->label('Tanggal Kirim')
                                        ->displayFormat('d/m/Y')
                                        ->nullable(),

                                    // ── Upload Eviden Screenshot WA ─────────
                                    \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('eviden_pengiriman')
                                        ->label('📸 Eviden Pengiriman (Screenshot WA)')
                                        ->collection('eviden_pengiriman')
                                        ->multiple()
                                        ->image()
                                        ->imagePreviewHeight('120')
                                        ->maxFiles(10)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->helperText('Upload screenshot WhatsApp bukti siaran pers dikirim.')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ═══ OPSI 2: UPLOAD FILE BYPASS ═══════════════════════
                    Forms\Components\Tabs\Tab::make('📁 Opsi 2 — Upload File Rekap')
                        ->schema([
                            Forms\Components\Hidden::make('metode_input')->default('file_upload'),

                            Forms\Components\Placeholder::make('info_bypass')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString(')
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                        <strong>Gunakan opsi ini jika:</strong> data sudah ada di file Excel/PDF/ZIP rekap dan tidak ingin mengisi form satu per satu.
                                    </div>')),

                            \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('bypass_files')
                                ->label('Upload File Rekap')
                                ->collection('bypass_files')
                                ->multiple()
                                ->maxFiles(5)
                                ->acceptedFileTypes(['application/pdf','application/zip',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'image/jpeg','image/png']),

                            Forms\Components\Textarea::make('catatan_bypass')
                                ->label('Catatan')
                                ->rows(3)
                                ->nullable(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester.nama_semester')
                    ->label('Semester')->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diisi Oleh')
                    ->visible(fn() => auth()->user()?->isAdmin()),

                Tables\Columns\BadgeColumn::make('metode_input')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state) => $state === 'detail_form' ? 'Form' : 'File')
                    ->color(fn(string $state) => $state === 'detail_form' ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('siaranPers.judul_draft')
                    ->label('Judul Draft')
                    ->limit(50)
                    ->placeholder('(via file upload)'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'draft', 'success' => 'submitted'])
                    ->formatStateUsing(fn(string $state) => $state === 'draft' ? 'Draft' : 'Submitted'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'nama_semester'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'submitted' => 'Submitted']),
            ])
            ->actions([
                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(KpiSubmission $r) => $r->canEdit())
                    ->action(function (KpiSubmission $r) {
                        $r->submit();
                        Notification::make()->title('Data berhasil disubmit!')->success()->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn(KpiSubmission $r) => $r->canEdit()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(KpiSubmission $r) => $r->canEdit()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function () {
                        $submissions = KpiSubmission::with('semester', 'user', 'siaranPers')
                            ->orderByDesc('created_at')
                            ->get();
                        
                        $data = $submissions->map(function ($submission) {
                            return [
                                'ID' => $submission->id,
                                'Semester' => $submission->semester?->nama_semester,
                                'Diisi Oleh' => $submission->user?->name,
                                'Metode Input' => $submission->metode_input,
                                'Judul Draft' => $submission->siaranPers?->judul_draft,
                                'Teks Draft Release' => $submission->siaranPers?->teks_draft_release,
                                'Platform' => $submission->siaranPers?->platform_pengiriman,
                                'Status' => $submission->status,
                                'Dibuat' => $submission->created_at?->format('Y-m-d H:i:s'),
                            ];
                        });
                        
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\KpiSiaranPersCollectionExport($data),
                            'kpi-submissions-' . now()->format('Y-m-d-His') . '.xlsx'
                        );
                    })
                    ->visible(fn() => auth()->user()?->isAdmin()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKpiSiaranPers::route('/'),
            'create' => Pages\CreateKpiSiaranPers::route('/create'),
            'edit'   => Pages\EditKpiSiaranPers::route('/{record}/edit'),
            'view'   => Pages\ViewKpiSiaranPers::route('/{record}'),
        ];
    }
}

namespace App\Filament\Resources\KpiSiaranPersResource\Pages;
use App\Filament\Resources\KpiSiaranPersResource;
use App\Models\{KpiSubmission, Semester, JenisKpi};
use Filament\Actions;
use Filament\Resources\Pages\{ListRecords, CreateRecord, EditRecord, ViewRecord};
class ListKpiSiaranPers extends ListRecords {
    protected static string $resource = KpiSiaranPersResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label('Input Siaran Pers')]; }
}
class CreateKpiSiaranPers extends CreateRecord {
    protected static string $resource = KpiSiaranPersResource::class;

    public function mount(): void
    {
        parent::mount();
        
        $existing = KpiSubmission::where([
            'user_id' => auth()->id(),
            'semester_id' => Semester::getActive()?->id,
            'jenis_kpi_id' => JenisKpi::where('kode_kpi', 'siaran_pers')->value('id'),
        ])->first();
        
        if ($existing) {
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $existing]));
        }
    }

    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
class EditKpiSiaranPers extends EditRecord {
    protected static string $resource = KpiSiaranPersResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
class ViewKpiSiaranPers extends ViewRecord {
    protected static string $resource = KpiSiaranPersResource::class;
}