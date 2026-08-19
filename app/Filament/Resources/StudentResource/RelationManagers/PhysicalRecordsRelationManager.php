<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;

class PhysicalRecordsRelationManager extends RelationManager
{
    // Pastikan nama relasi ini sesuai dengan yang ada di model Student Anda
    protected static string $relationship = 'physicalRecords'; 

    protected static ?string $recordTitleAttribute = 'record_date';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hasil Tes Fisik')
                    ->description('Masukkan tanggal dan angka hasil tes fisik. Kosongkan jika tidak ada.')
                    ->schema([
                        DatePicker::make('record_date')
                            ->label('Tanggal Pelaksanaan')
                            ->default(now())
                            ->required()
                            // Validasi agar tidak ada tanggal ganda untuk siswa ini
                            ->unique(
                                modifyRuleUsing: fn (Unique $rule, \Filament\Resources\RelationManagers\RelationManager $livewire) => 
                                    $rule->where('student_id', $livewire->getOwnerRecord()->id),
                                ignoreRecord: true
                            )
                            ->validationMessages([
                                'unique' => 'Data fisik pada tanggal tersebut sudah ada! Silakan edit data di tabel.',
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('run_12_min_dist')
                                    ->label('Lari 12 Menit')
                                    ->helperText('Jarak dalam Meter')
                                    ->numeric()
                                    ->suffix('M'),

                                TextInput::make('push_up_reps')
                                    ->label('Push Up')
                                    ->helperText('Jumlah repetisi')
                                    ->numeric()
                                    ->suffix('Kali'),

                                TextInput::make('sit_up_reps')
                                    ->label('Sit Up')
                                    ->helperText('Jumlah repetisi')
                                    ->numeric()
                                    ->suffix('Kali'),

                                TextInput::make('pull_up_reps')
                                    ->label('Pull Up / Chin Up')
                                    ->helperText('  Jumlah repetisi')
                                    ->numeric()
                                    ->suffix('Kali'),
                            ])
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('record_date')
            ->columns([
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Input Hasil Fisik Baru')
                    ->icon('heroicon-o-plus-circle'),
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
}