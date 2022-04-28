<?php

declare(strict_types=1);

namespace App\Dto;

class WayBackResponseDto
{
    private null|string $url;
    private null|string $title = 'Not found';
    private null|string $snapshotUrl;
    private null|int $snapshotYear;

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSnapshotUrl(?string $snapshotUrl): self
    {
        $this->snapshotUrl = $snapshotUrl;

        return $this;
    }

    public function getSnapshotUrl(): ?string
    {
        return $this->snapshotUrl;
    }

    public function setSnapshotYear(?int $snapshotYear): self
    {
        $this->snapshotYear = $snapshotYear;

        return $this;
    }

    public function getSnapshotYear(): ?int
    {
        return $this->snapshotYear;
    }
}