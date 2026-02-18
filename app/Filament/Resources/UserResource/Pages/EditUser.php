<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the current role from the pivot
        $currentOrgId = auth()->user()->current_organization_id;
        $pivot = $this->record->organizations()
            ->where('organizations.id', $currentOrgId)
            ->first()?->pivot;

        $data['role'] = $pivot?->role ?? 'member';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract and update role separately
        $role = $data['role'] ?? 'member';
        unset($data['role']);

        $currentOrgId = auth()->user()->current_organization_id;
        $this->record->organizations()->updateExistingPivot($currentOrgId, [
            'role' => $role,
        ]);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
