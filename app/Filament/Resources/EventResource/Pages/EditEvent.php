<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\File;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing file paths into the attachments field
        $data['attachments'] = $this->record->files->pluck('path')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove attachments — handled in afterSave
        unset($data['attachments']);

        return $data;
    }

    protected function afterSave(): void
    {
        $uploadedPaths = $this->data['attachments'] ?? [];

        $existingFiles = $this->record->files;
        $existingPaths = $existingFiles->pluck('path')->toArray();

        // Delete removed files
        foreach ($existingFiles as $file) {
            if (!in_array($file->path, $uploadedPaths)) {
                Storage::disk('public')->delete($file->path);
                $file->delete();
            }
        }

        // Create records for new files
        foreach ($uploadedPaths as $path) {
            if (!in_array($path, $existingPaths)) {
                $this->record->files()->create([
                    'filename' => basename($path),
                    'path' => $path,
                    'mime_type' => Storage::disk('public')->mimeType($path) ?? 'application/octet-stream',
                    'size' => Storage::disk('public')->size($path),
                    'uploaded_by_user_id' => auth()->id(),
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
