<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Opening a message marks it as read.
        if ($this->record instanceof ContactMessage && $this->record->read_at === null) {
            $this->record->update(['read_at' => now()]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->url(fn () => 'mailto:'.$this->record->email.'?subject='.rawurlencode('Re: your message'))
                ->openUrlInNewTab(),
            Action::make('markUnread')
                ->label('Mark as unread')
                ->color('gray')
                ->action(function () {
                    $this->record->update(['read_at' => null]);
                    $this->redirect(ContactMessageResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}
