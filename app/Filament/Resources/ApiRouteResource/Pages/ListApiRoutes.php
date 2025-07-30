<?php

namespace App\Filament\Resources\ApiRouteResource\Pages;

use App\Filament\Resources\ApiRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiRoutes extends ListRecords
{
    protected static string $resource = ApiRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 