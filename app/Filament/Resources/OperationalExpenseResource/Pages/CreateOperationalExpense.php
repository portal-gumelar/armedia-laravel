<?php

namespace App\Filament\Resources\OperationalExpenseResource\Pages;

use App\Filament\Resources\OperationalExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOperationalExpense extends CreateRecord
{
    protected static string $resource = OperationalExpenseResource::class;
}
