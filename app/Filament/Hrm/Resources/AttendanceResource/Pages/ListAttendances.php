<?php
namespace App\Filament\Hrm\Resources\AttendanceResource\Pages;
use App\Filament\Hrm\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListAttendances extends ListRecords {
    protected static string $resource = AttendanceResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
