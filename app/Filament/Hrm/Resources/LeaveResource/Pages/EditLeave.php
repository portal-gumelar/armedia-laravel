<?php
namespace App\Filament\Hrm\Resources\LeaveResource\Pages;
use App\Filament\Hrm\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditLeave extends EditRecord {
    protected static string $resource = LeaveResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
