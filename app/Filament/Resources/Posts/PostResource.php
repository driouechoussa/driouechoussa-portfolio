<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Support\Translatable;
use App\Models\Post;
use App\Support\Locale;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'blog post';

    /** Must match the "blogcat.*" keys in lang/{locale}/ui.php. */
    public const CATEGORIES = [
        'frontend' => 'Front-End',
        'backend' => 'Back-End',
        'performance' => 'Performance',
        'career' => 'Career',
    ];

    public const BLOCKS = [
        'p' => 'Paragraph',
        'h2' => 'Heading',
        'quote' => 'Quote',
        'ul' => 'List (one item per line)',
        'code' => 'Code',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article')->columns(2)->schema([
                TextInput::make('slug')
                    ->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true)
                    ->helperText('Used in the URL: /blog/{slug}'),
                Select::make('category')->options(self::CATEGORIES)->required(),
                DatePicker::make('published_at')
                    ->label('Publish date')
                    ->native(false)
                    ->helperText('Leave empty to keep it as a draft. A future date publishes it on that day.'),
                TextInput::make('read_time')->label('Reading time (minutes)')->numeric()->minValue(1)->maxValue(120)->required()->default(5),
                FileUpload::make('image')
                    ->label('Cover image')
                    ->image()->disk('public')->directory('posts')->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]),

            Translatable::tabs('Title and summary', fn (string $locale) => [
                TextInput::make("title.$locale")->label('Title')->maxLength(255)->required(Translatable::required($locale)),
                Textarea::make("excerpt.$locale")->label('Summary (cards and search engines)')->rows(2)->required(Translatable::required($locale)),
            ]),

            Section::make('Body')->schema([
                Repeater::make('body')
                    ->hiddenLabel()
                    ->schema([
                        Select::make('type')->options(self::BLOCKS)->default('p')->required()->live(),

                        // Code is the same in every language. It is stored in "text" like the
                        // other blocks, but edited as "code" so it doesn't share a state path
                        // with the translated "text.{locale}" fields (see the repeater hooks below).
                        Textarea::make('code')
                            ->label('Code')
                            ->rows(8)
                            ->extraInputAttributes(['class' => 'font-mono', 'dir' => 'ltr'])
                            ->visible(fn (Get $get) => $get('type') === 'code')
                            ->required(),

                        ...array_map(fn (string $locale) => Textarea::make("text.$locale")
                            ->label(match ($locale) { 'en' => 'English', 'fr' => 'Français', default => 'العربية' })
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('type') !== 'code')
                            ->required(Translatable::required($locale))
                            // Lists are stored as arrays; edit them one item per line.
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                            ->dehydrateStateUsing(fn (?string $state, Get $get) => $get('type') === 'ul'
                                ? array_values(array_filter(array_map('trim', explode("\n", (string) $state))))
                                : $state),
                            Locale::SUPPORTED),
                    ])
                    ->formatStateUsing(fn (?array $state) => array_map(
                        fn (array $block) => ($block['type'] ?? 'p') === 'code'
                            ? ['type' => 'code', 'code' => is_string($block['text'] ?? null) ? $block['text'] : '']
                            : $block,
                        $state ?? [],
                    ))
                    ->mutateDehydratedStateUsing(fn (?array $state) => array_values(array_map(
                        fn (array $block) => ($block['type'] ?? 'p') === 'code'
                            ? ['type' => 'code', 'text' => (string) ($block['code'] ?? '')]
                            : ['type' => $block['type'] ?? 'p', 'text' => $block['text'] ?? []],
                        $state ?? [],
                    )))
                    ->itemLabel(fn (array $state) => self::BLOCKS[$state['type'] ?? 'p'] ?? null)
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title.en')->label('Title')->searchable(query: fn ($query, string $search) => $query->where('title', 'like', "%{$search}%"))->limit(60),
                TextColumn::make('category')->badge()->formatStateUsing(fn (string $state) => self::CATEGORIES[$state] ?? $state),
                TextColumn::make('published_at')->label('Published')->date()->sortable()->placeholder('Draft'),
                TextColumn::make('read_time')->label('Min')->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(self::CATEGORIES),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
