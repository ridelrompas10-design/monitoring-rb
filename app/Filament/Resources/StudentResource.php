<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use App\Filament\Exports\StudentExporter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Data Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('registration_number')
                    ->label('Nomor Pendaftaran')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                    
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-Laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                    
                Select::make('status')
                    ->label('Status Siswa')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Lulus' => 'Lulus',
                        'Keluar' => 'Keluar',
                    ])
                    ->default('Aktif')
                    ->required(),

                Toggle::make('is_watchlist')
                    ->label('Butuh Perhatian Khusus?')
                    ->reactive(),

                Textarea::make('watchlist_notes')
                    ->label('Catatan Perhatian Khusus')
                    ->hidden(fn (\Filament\Forms\Get $get): bool => ! $get('is_watchlist'))
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Informasi Profil')
                ->columns(['default' => 1, 'sm' => 2]) // 1 baris di HP, 2 baris di Laptop
                ->schema([
                    TextEntry::make('registration_number')->label('No. Pendaftaran'),
                    TextEntry::make('name')->label('Nama Lengkap')->weight('bold'),
                    TextEntry::make('gender')->label('Jenis Kelamin'),
                    TextEntry::make('status')
                        ->badge() // Membuatnya jadi label berwarna (hijau untuk aktif)
                        ->color('success'),
                    IconEntry::make('is_watchlist')
                        ->label('Perhatian Khusus')
                        ->boolean(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')
                    ->label('No. Daftar')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('gender')
                    ->label('L/P'),
                    
                TextColumn::make('status')
                    ->badge()
                    ->visibleFrom('md')
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Lulus' => 'info',
                        'Keluar' => 'danger',
                        default => 'secondary',
                    }),
                    
                IconColumn::make('is_watchlist')
                    ->label('Perhatian Khusus')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(StudentExporter::class), 
            ]),
]);
    }

   public static function getRelations(): array
{
    return [
        \app\Filament\Resources\StudentResource\RelationManagers\PhysicalRecordsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}