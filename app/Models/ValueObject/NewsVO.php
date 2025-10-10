<?php

namespace App\Models\ValueObject;

class NewsVO
{
    private string $title;
    private string $content;
    private string $author;
    private \DateTime $publishedAt;
    private string $url;
    private string $source;
    private string $remoteSource;
    private string $description;
    private string $remoteId;
    private string $imageUrl;
    private string $category;
    private string $language;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->author = $data['author'] ?? 'Unknown';
        $this->publishedAt = isset($data['publishedAt']) ? new \DateTime($data['publishedAt']) : new \DateTime();
        $this->url = $data['url'] ?? '';
        $this->source = $data['source']['name'] ?? 'Unknown';
        $this->remoteSource = $data['remoteSource'] ?? 'Unknown';
        $this->description = $data['description'] ?? '';
        $this->remoteId = $data['remoteId'] ?? '';
        $this->imageUrl = $data['imageUrl'] ?? '';
        $this->category = $data['category'] ?? 'general';
        $this->language = $data['language'] ?? 'en';
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getAuthor(): string
    {
        return $this->author;
    }
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
    public function getSource(): string
    {
        return $this->source;
    }
    public function getRemoteSource(): string
    {
        return $this->remoteSource;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
    public function getCategory(): string
    {
        return $this->category;
    }
    public function getLanguage(): string
    {
        return $this->language;
    }
}
