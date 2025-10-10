<?php

namespace App\Models\ValueObject;

class NewsVO
{
    public string $title;
    public string $content;
    public string $author;
    public \DateTime $publishedAt;
    public string $url;
    public string $source;
    public string $remoteSource;
    public string $description;
    public string $remoteId;
    public string $imageUrl;
    public string $category;
    public string $language;


    public function __set(string $property, $value): void
    {
        throw new \BadMethodCallException("Cannot set property {$property} directly. Use setter methods instead.");
    }

    // Setter methods (chainable)
    public function setTitle(?string $title): self
    {
        $this->title = $title ?? '';
        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?? '';
        return $this;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author ?? 'Unknown';
        return $this;
    }

    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt ?? new \DateTime();
        return $this;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url ?? '';
        return $this;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source ?? 'Unknown';
        return $this;
    }

    public function setRemoteSource(?string $remoteSource): self
    {
        $this->remoteSource = $remoteSource ?? 'Unknown';
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description ?? '';
        return $this;
    }

    public function setRemoteId(?string $remoteId): self
    {
        $this->remoteId = $remoteId ?? '';
        return $this;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl ?? '';
        return $this;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category ?? 'general';
        return $this;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language ?? 'en';
        return $this;
    }
}
