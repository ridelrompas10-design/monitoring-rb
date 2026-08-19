<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhysicalRecordResource\Pages;
use App\Models\PhysicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Get;

class PhysicalRecordResource extends Resource
{
    protected static ?string $model = PhysicalRecord::class;

    // Menampilkan ikon dan label di sidebar
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Rekam Fisik';
    protected static ?string $slug = 'physical-records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kotak 1: Informasi Dasar
                Section::make('Informasi Dasar')
                    ->description('Pilih nama siswa dan tentukan tanggal pelaksanaan tes fisik.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('student_id')
                            ->relationship('student', 'name')
                            ->label('Nama Siswa')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        DatePicker::make('record_date')
                            ->label('Tanggal Pelaksanaan')
                            ->default(now())
                            ->required()
                            ->unique(
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('student_id', $get('student_id')),
                                ignoreRecord: true
                            )
                            ->validationMessages([
                                'unique' => 'Data fisik siswa ini pada tanggal tersebut sudah ada! Silakan edit data yang sudah ada.',
                            ]),
                    ])->columns(2),

                // Kotak 2: Pengukuran Fisik (Lengkap 4 Item)
                Section::make('Hasil Tes Fisik')
                    ->description('Masukkan angka hasil tes. Anda bisa mengosongkan kolom jika siswa tidak mengikuti tes tertentu.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('run_12_min_dist')
                                    ->label('Lari 12 Menit')
                                    ->helperText('Masukkan jarak dalam satuan Meter (M)')
                                    ->numeric()
                                    ->suffix('Meter'),
                                    
                                TextInput::make('push_up_reps')
                                    ->label('Push Up')
                                    ->helperText('Jumlah repetisi yang benar')
                                    ->numeric()
                                    ->suffix('Kali'),

                                TextInput::make('sit_up_reps')
                                    ->label('Sit Up')
                                    ->helperText('Jumlah repetisi yang benar')
                                    ->numeric()
                                    ->suffix('Kali'),
                                    
                                TextInput::make('pull_up_reps')
                                    ->label('Pull Up / Chin Up')
                                    ->helperText('Jumlah repetisi yang benar')
                                    ->numeric()
                                    ->suffix('Kali'),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('record_date')
                    ->label('Tanggal Tes')
                    ->date('d M Y')
                    ->sortable(),
                    
                TextColumn::make('run_12_min_dist')
                    ->label('Lari (M)')
                    ->sortable(),
                    
                TextColumn::make('push_up_reps')
                    ->label('Push Up')
                    ->sortable(),

                TextColumn::make('sit_up_reps')
                    ->label('Sit Up')
                    ->sortable(),

                TextColumn::make('pull_up_reps')
                    ->label('Pull Up')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPhysicalRecords::route('/'),
            'create' => Pages\CreatePhysicalRecord::route('/create'),
            'edit' => Pages\EditPhysicalRecord::route('/{record}/edit'),
        ];
    }
}