<?php

namespace App\Filament\Resources\Education;

use App\Filament\Resources\Education\Pages\CreateEducation;
use App\Filament\Resources\Education\Pages\EditEducation;
use App\Filament\Resources\Education\Pages\ListEducation;
use App\Filament\Support\Translatable;
use App\Models\Education;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EducationResource extends Resource
{
    protected static ?string $model = Education::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'education';

    protected static ?string $pluralModelLabel = 'education';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('start_year')->required()->length(4)->numeric(),
                TextInput::make('end_year')->length(4)->numeric()->helperText('Leave empty if ongoing.'),
            ]),
            Translatable::tabs('Content', fn (string $locale) => [
                TextInput::make("school.$locale")->label('School')->required(Translatable::required($locale)),
                TextInput::make("degree.$locale")->label('Degree')->required(Translatable::required($locale)),
                TextInput::make("field.$locale")->label('Field of study')->required(Translatable::required($locale)),
                Textarea::make("description.$locale")->label('Description')->rows(3)->required(Translatable::required($locale)),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('degree.en')->label('Degree'),
                TextColumn::make('school.en')->label('School')->limit(50),
                TextColumn::make('start_year')->label('From'),
                TextColumn::make('end_year')->label('To')->placeholder('Now'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEducation::route('/'),
            'create' => CreateEducation::route('/create'),
            'edit' => EditEducation::route('/{record}/edit'),
        ];
    }
}
