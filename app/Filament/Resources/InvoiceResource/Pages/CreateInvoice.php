<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('invoices.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record !== null),
        ];
    }
}
