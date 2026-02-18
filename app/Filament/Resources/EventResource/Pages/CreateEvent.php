<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->current_organization_id;
        $data['created_by_user_id'] = auth()->id();
        $data['type'] = $data['type'] ?? request()->query('type', 'booking');

        // Remove attachments — handled in afterCreate
        unset($data['attachments']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $uploadedPaths = $this->data['attachments'] ?? [];

        foreach ($uploadedPaths as $path) {
            $this->record->files()->create([
                'filename' => basename($path),
                'path' => $path,
                'mime_type' => Storage::disk('public')->mimeType($path) ?? 'application/octet-stream',
                'size' => Storage::disk('public')->size($path),
                'uploaded_by_user_id' => auth()->id(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
