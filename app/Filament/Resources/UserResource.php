<?php
// ══════════════════════════════════════════════════════════════════════
// FILE: app/Filament/Resources/UserResource.php
// ══════════════════════════════════════════════════════════════════════
namespace App\Filament\Resources;
 
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
 
class UserResource extends Resource
{
    protected static ?string $model           = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?int    $navigationSort  = 3;
 
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
 
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nama Lengkap')->required(),
                Forms\Components\TextInput::make('username')->label('Username')
                    ->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('email')->email()->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')->options(['admin' => 'Admin', 'user' => 'User'])
                    ->required()->default('user'),
                Forms\Components\TextInput::make('password')->password()
                    ->dehydrateStateUsing(fn($v) => filled($v) ? Hash::make($v) : null)
                    ->dehydrated(fn($v) => filled($v))
                    ->required(fn(string $op) => $op === 'create')
                    ->helperText('Kosongkan untuk tidak mengubah password')
                    ->columnSpanFull(),
            ]),
        ]);
    }
 
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('username')->label('Username')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email'),
            Tables\Columns\BadgeColumn::make('role')
                ->colors(['danger' => 'admin', 'info' => 'user'])
                ->formatStateUsing(fn($s) => ucfirst($s)),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->date('d/m/Y'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->before(fn(User $r) => $r->id === auth()->id() ? $this->halt() : null),
        ]);
    }
 
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
 
namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\{ListRecords, CreateRecord, EditRecord};
class ListUsers extends ListRecords {
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
class CreateUser extends CreateRecord {
    protected static string $resource = UserResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
class EditUser extends EditRecord {
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}