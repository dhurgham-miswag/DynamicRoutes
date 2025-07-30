<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiRouteResource\Pages;
use App\Models\ApiRoute;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiRouteResource extends Resource
{
    protected static ?string $model = ApiRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'API';

    protected static ?string $navigationLabel = 'API Routes';

    protected static ?string $title = 'API Routes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('middleware')
                    ->required()
                    ->label('Middleware')
                    ->searchable()
                    ->options([
                        "api" => "API",
                        "web" => "WEB",
                    ])
                    ->live(onBlur: true)
                    ->placeholder('Select a middleware first...')
                    ->afterStateUpdated(function ($state, $set) {
                        $set('service_group', '');
                        $set('controller_name', '');
                        $set('method_name', '');
                    }),

                Forms\Components\TextInput::make('service_group')
                    ->required()
                    ->disabled(fn($get) => !$get('middleware'))
                    ->maxLength(255)
                    ->label('Service Group (e.g., youtube, spotify)')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('controller_name', '');
                        $set('method_name', '');
                    }),

                Forms\Components\Select::make('controller_name')
                    ->required()
                    ->disabled(fn($get) => !$get('service_group'))
                    ->label('Controller Name')
                    ->options(function ($get) {
                        $service_group = $get('service_group');
                        if (!$service_group) {
                            return [];
                        }

                        $controllers = [];
                        $middleware = $get('middleware') ?? 'api';
                        $base_path = $service_group == "/"
                            ? app_path("Http/Controllers/" . ucfirst($middleware))
                            : app_path("Http/Controllers/" . ucfirst($middleware) . "/" . ucfirst($service_group));

                        if (is_dir($base_path)) {
                            $files = glob($base_path . "/*.php");
                            foreach ($files as $file) {
                                $filename = basename($file, '.php');
                                $controllers[ucfirst($filename)] = ucfirst($filename);
                            }
                        }

                        return $controllers;
                    })
                    ->searchable()
                    ->placeholder('Select a controller...')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('method_name', '');
                    }),

                Forms\Components\TextInput::make('route_name')
                    ->required()
                    ->disabled(fn($get) => !$get('controller_name'))
                    ->maxLength(255)
                    ->label('Route Name (e.g., search, convert-to-mp3)'),

                Forms\Components\Select::make('method_name')
                    ->required()
                    ->disabled(fn($get) => !$get('controller_name'))
                    ->label('Method Name')
                    ->options(function ($get) {
                        $middleware = $get('middleware') ?? 'api';
                        $controller_name = $get('controller_name');
                        $service_group = $get('service_group');
                        $controller_class = $service_group == "/" ? "App\\Http\\Controllers\\" . ucfirst($middleware) . "\\{$controller_name}" : "App\\Http\\Controllers\\" . ucfirst($middleware) ."\\" . ucfirst($service_group) . "\\{$controller_name}";
                        $controller_name_parts = explode('\\', $controller_class);
                        $last_part = end($controller_name_parts);
                        $controller_class = str_replace($last_part, ucfirst($last_part), $controller_class);
                        $possible_classes = [
                            $controller_class,
                            str_replace(ucfirst($last_part), $last_part, $controller_class), // original case
                            str_replace(ucfirst($last_part), strtolower($last_part), $controller_class), // lowercase
                            str_replace(ucfirst($last_part), strtoupper($last_part), $controller_class), // uppercase
                        ];

                        $found_class = null;
                        foreach ($possible_classes as $class) {
                            if (class_exists($class)) {
                                $found_class = $class;
                                break;
                            }
                        }

                        if (!$found_class) {
                            return [];
                        }

                        $controller_class = $found_class;

                        try {
                            $reflection = new \ReflectionClass($controller_class);

                            $public_methods = collect($reflection->getMethods(\ReflectionMethod::IS_PUBLIC))
                                ->filter(function ($method) use ($controller_class) {
                                    $isValid = strtolower($method->class) === strtolower($controller_class) && !$method->isConstructor();
                                    \Log::info("Method {$method->getName()} from {$method->class} - Valid: " . ($isValid ? 'true' : 'false'));
                                    return $isValid;
                                })
                                ->mapWithKeys(fn($method) => [$method->getName() => $method->getName()]);

                            return $public_methods->toArray();
                        } catch (\ReflectionException $e) {
                            \Log::error("Reflection failed for {$controller_class}: " . $e->getMessage());
                            return [];
                        }
                    })
                    ->searchable()
                    ->placeholder('Select a controller first...'),

        Forms\Components\Select::make('http_method')
                    ->required()
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->default('POST'),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('middleware')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'api' => 'warning',
                        'web' => 'success',
                        default => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_group')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('route_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('controller_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('method_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('http_method')
                    ->badge()
                    ->color('success')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiRoutes::route('/'),
        ];
    }
}
