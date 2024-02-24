<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(1024),
                Forms\Components\TextInput::make('path')
                    ->maxLength(1024),
                Forms\Components\TextInput::make('storage_path')
                    ->maxLength(1024),
                Forms\Components\TextInput::make('_lft')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('_rgt')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('parent_id')
                    ->numeric(),
                Forms\Components\Toggle::make('is_folder')
                    ->required(),
                Forms\Components\TextInput::make('mime')
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->numeric(),
                Forms\Components\Toggle::make('uploaded_on_cloud')
                    ->required(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('storage_path'),
                Tables\Columns\IconColumn::make('is_folder')
                    ->boolean(),
                Tables\Columns\TextColumn::make('mime')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric(),
                Tables\Columns\IconColumn::make('uploaded_on_cloud')
                    ->boolean(),
                Tables\Columns\IconColumn::make('product.name')
               ,
                Tables\Columns\IconColumn::make('owner')
              ,
                    // ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}
