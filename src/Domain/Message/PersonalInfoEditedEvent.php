<?php

declare(strict_types=1);

namespace App\Domain\Message;

class PersonalInfoEditedEvent implements AsyncMessageInterface
{
    public string $type = 'user.personal_info_edited';

    public function __construct(
        public readonly int $userId,
        public readonly ?string $name,
        public readonly ?string $oldName,
    ) {
    }

    public function getPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'old_name' => $this->oldName,
        ];
    }
}
