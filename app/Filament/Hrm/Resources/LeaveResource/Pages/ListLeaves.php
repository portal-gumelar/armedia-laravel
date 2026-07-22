<?php
namespace App\Filament\Hrm\Resources\LeaveResource\Pages;
use App\Filament\Hrm\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListLeaves extends ListRecords {
    protected static string $resource = LeaveResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
