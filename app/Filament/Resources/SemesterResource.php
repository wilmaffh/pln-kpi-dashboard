<?php
// ══════════════════════════════════════════════════════════════════════
// FILE: app/Filament/Resources/SemesterResource.php
// ══════════════════════════════════════════════════════════════════════
namespace App\Filament\Resources;

use App\Filament\Resources\SemesterResource\Pages;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;


class SemesterResource extends FilamentResource
{
    protected static ?string $model             = Semester::class;
    protected static ?string $navigationIcon    = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup   = 'Master Data';
    protected static ?string $navigationLabel   = 'Semester';
    protected static ?int    $navigationSort    = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Semester')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nama_semester')
                        ->label('Nama Semester')
                        ->required()
                        ->placeholder('Semester 1 Tahun 2025')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('periode')
                        ->label('Periode')
                        ->options([
                            'JAN_JUN' => 'Januari - Juni',
                            'JUL_DES' => 'Juli - Desember',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->required()
                        ->minValue(2020)
                        ->maxValue(2099)
                        ->default(now()->year),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Jadikan Semester Aktif?')
                        ->helperText('Hanya 1 semester yang bisa aktif. Semester lain otomatis dinonaktifkan.')
                        ->default(false)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_semester')
                    ->label('Semester')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\BadgeColumn::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(fn(string $state) => $state === 'JAN_JUN' ? 'Jan – Jun' : 'Jul – Des')
                    ->color(fn(string $state) => $state === 'JAN_JUN' ? 'info' : 'warning'),

                Tables\Columns\TextColumn::make('tahun')->label('Tahun')->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('kpiSubmissions_count')
                    ->label('Submission')
                    ->counts('kpiSubmissions')
                    ->badge()
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Semester $r) => !$r->is_active)
                    ->action(function (Semester $r) {
                        $r->activate();
                        Notification::make()->title('Semester diaktifkan!')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Semester $r) {
                        if ($r->is_active) {
                            Notification::make()->title('Tidak bisa hapus semester aktif!')->danger()->send();
                            $this->halt();
                        }
                    }),
            ])
            ->defaultSort('tahun', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit'   => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}

// Pages (taruh masing-masing di subfolder SemesterResource/Pages/)
namespace App\Filament\Resources\SemesterResource\Pages;
use App\Filament\Resources\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\{ListRecords, CreateRecord, EditRecord};

class ListSemesters extends ListRecords {
    protected static string $resource = SemesterResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
class CreateSemester extends CreateRecord {
    protected static string $resource = SemesterResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
class EditSemester extends EditRecord {
    protected static string $resource = SemesterResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}