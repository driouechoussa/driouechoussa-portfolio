<?php

namespace App\Filament\Resources\Experiences;

use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Support\MonthPicker;
use App\Filament\Support\Translatable;
use App\Models\Experience;
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

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextInput::make('company')->required()->maxLength(255),
                MonthPicker::make('start_date', 'Start (month/year)')->required(),
                MonthPicker::make('end_date', 'End (month/year)')
                    ->afterOrEqual('start_date')
                    ->helperText('Leave empty for your current position.'),
            ]),
            Translatable::tabs('Content', fn (string $locale) => [
                TextInput::make("role.$locale")->label('Role')->required(Translatable::required($locale)),
                TextInput::make("location.$locale")->label('Location')->required(Translatable::required($locale)),
                Textarea::make("description.$locale")->label('Description')->rows(3)->required(Translatable::required($locale)),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role.en')->label('Role'),
                TextColumn::make('company'),
                TextColumn::make('start_date')->label('From')->date(Experience::MONTH_FORMAT),
                TextColumn::make('end_date')->label('To')->date(Experience::MONTH_FORMAT)->placeholder('Now'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperiences::route('/'),
            'create' => CreateExperience::route('/create'),
            'edit' => EditExperience::route('/{record}/edit'),
        ];
    }
}
