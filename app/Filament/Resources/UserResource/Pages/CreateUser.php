<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract role before creating the user
        $this->role = $data['role'] ?? 'member';
        unset($data['role']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach the new user to the current organization with the selected role
        $currentOrgId = auth()->user()->current_organization_id;

        if ($currentOrgId) {
            $this->record->organizations()->attach($currentOrgId, [
                'role' => $this->role,
            ]);

            $this->record->update([
                'current_organization_id' => $currentOrgId,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private string $role = 'member';
}
