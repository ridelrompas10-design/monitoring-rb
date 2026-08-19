<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Models\Training;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Pelatihan & Jadwal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pelatihan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Pelatihan')
                            ->placeholder('Contoh: Bela Diri, Renang')
                            ->required()
                            ->maxLength(255),
                            
                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pengaturan Jadwal')
                    ->description('Tambahkan jadwal hari dan jam untuk pelatihan ini.')
                    ->schema([
                        Repeater::make('schedules')
                            ->relationship() // Otomatis tersimpan ke tabel schedules
                            ->label('')
                            ->addActionLabel('Tambah Jadwal')
                            ->schema([
                                Select::make('day_of_week')
                                    ->label('Hari')
                                    ->options([
                                        'Senin' => 'Senin',
                                        'Selasa' => 'Selasa',
                                        'Rabu' => 'Rabu',
                                        'Kamis' => 'Kamis',
                                        'Jumat' => 'Jumat',
                                        'Sabtu' => 'Sabtu',
                                        'Minggu' => 'Minggu',
                                        'TBD' => 'Belum Ditentukan (TBD)',
                                    ])
                                    ->required(),
                                    
                                TimePicker::make('start_time')
                                    ->label('Jam Mulai (Bisa dikosongkan)')
                                    ->datalist([
                                        '07:00', '08:00', '15:00', '16:00'
                                    ]),
                                    
                                TimePicker::make('end_time')
                                    ->label('Jam Selesai (Bisa dikosongkan)')
                                    ->datalist([
                                        '09:00', '10:00', '17:00', '19:00'
                                    ]),
                            ])->columns(3)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pelatihan')
                    ->searchable(),
                    
                TextColumn::make('schedules_count')
                    ->counts('schedules')
                    ->label('Jumlah Jadwal')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}