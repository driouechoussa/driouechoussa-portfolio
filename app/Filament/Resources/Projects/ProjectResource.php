<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Support\Translatable;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    /** Must match the "cat.*" keys in lang/{locale}/ui.php (used for filter chips). */
    public const CATEGORIES = [
        'fullstack' => 'Full-Stack',
        'frontend' => 'Front-End',
        'backend' => 'Back-End',
        'ecommerce' => 'E-commerce',
        'webapp' => 'Web App',
    ];

    public const PREVIEWS = ['shop' => 'Shop', 'dash' => 'Dashboard', 'landing' => 'Landing page', 'code' => 'Code'];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project')->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('slug')
                    ->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true)
                    ->helperText('Used in the URL: /projects/{slug}'),
                Select::make('category')->options(self::CATEGORIES)->required(),
                TextInput::make('year')->required()->length(4)->numeric(),
                TextInput::make('client')->maxLength(255),
                TagsInput::make('stack')->required()->placeholder('Add a technology'),
                TextInput::make('live_url')->label('Live site URL')->url()->maxLength(255),
                TextInput::make('source_url')->label('Source code URL')->url()->maxLength(255),
            ]),

            Section::make('Image')->columns(3)->schema([
                FileUpload::make('image')
                    ->label('Screenshot')
                    ->image()->disk('public')->directory('projects')->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Optional. Replaces the drawn mock-up. Use a 16:10 image, max 2 MB (WebP or JPG).')
                    ->columnSpan(2),
                Grid::make(1)->columnSpan(1)->schema([
                    Select::make('preview')->label('Mock-up style (when no image)')->options(self::PREVIEWS)->required(),
                    TextInput::make('icon')->required()->default('fa-code')->helperText('Font Awesome icon, e.g. fa-bag-shopping'),
                    ColorPicker::make('accent')->required()->default('#e6a514'),
                ]),
            ]),

            Translatable::tabs('Content', fn (string $locale) => [
                TextInput::make("role.$locale")->label('Your role')->required(Translatable::required($locale)),
                TextInput::make("duration.$locale")->label('Duration')->required(Translatable::required($locale)),
                Textarea::make("description.$locale")->label('Short description (cards and search engines)')->rows(2)->required(Translatable::required($locale)),
                Textarea::make("overview.$locale")->label('Overview')->rows(4)->required(Translatable::required($locale)),
                Textarea::make("challenge.$locale")->label('The challenge')->rows(4)->required(Translatable::required($locale)),
                Textarea::make("solution.$locale")->label('The solution')->rows(4)->required(Translatable::required($locale)),
            ]),

            Section::make('Results')->schema([
                Repeater::make('results')
                    ->hiddenLabel()
                    ->schema([
                        TextInput::make('value')->required()->placeholder('+180%'),
                        TextInput::make('label.en')->label('Label (English)')->required(),
                        TextInput::make('label.fr')->label('Label (Français)'),
                        TextInput::make('label.ar')->label('Label (العربية)'),
                    ])
                    ->columns(4)
                    ->defaultItems(0)
                    ->reorderable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public')->label('')->square(),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->badge()->formatStateUsing(fn (string $state) => self::CATEGORIES[$state] ?? $state),
                TextColumn::make('year')->sortable(),
                TextColumn::make('client')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(self::CATEGORIES),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
