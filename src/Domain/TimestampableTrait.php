<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected ?\DateTimeImmutable $createdAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    protected function updateTimestampFields(): void
    {
        $date = DateTimeProvider::current();

        if (null === $this->createdAt) {
            $this->createdAt = $date;

            return;
        }

        $this->updatedAt = $date;
    }
}
