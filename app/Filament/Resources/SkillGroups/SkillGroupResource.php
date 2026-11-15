<?php

namespace App\Filament\Resources\SkillGroups;

use App\Filament\Resources\SkillGroups\Pages\CreateSkillGroup;
use App\Filament\Resources\SkillGroups\Pages\EditSkillGroup;
use App\Filament\Resources\SkillGroups\Pages\ListSkillGroups;
use App\Models\SkillGroup;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SkillGroupResource extends Resource
{
    protected static ?string $model = SkillGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'skill group';

    /** Must match the group title keys used on the About page. */
    public const GROUPS = ['frontend' => 'Front-End', 'backend' => 'Back-End', 'tools' => 'Tools & DevOps'];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('key')->label('Group')->options(self::GROUPS)->required()->unique(ignoreRecord: true),
                TextInput::make('icon')->required()->helperText('Font Awesome icon, e.g. fa-display'),
            ]),
            Section::make('Skills')->schema([
                Repeater::make('skills')
                    ->relationship()
                    ->hiddenLabel()
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('level')->label('Level (%)')->numeric()->minValue(0)->maxValue(100)->required(),
                    ])
                    ->columns(2)
                    ->orderColumn('sort_order')
                    ->defaultItems(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Group')->formatStateUsing(fn (string $state) => self::GROUPS[$state] ?? $state),
                TextColumn::make('skills_count')->counts('skills')->label('Skills'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkillGroups::route('/'),
            'create' => CreateSkillGroup::route('/create'),
            'edit' => EditSkillGroup::route('/{record}/edit'),
        ];
    }
}
