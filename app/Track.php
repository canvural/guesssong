<?php

namespace App;

use Illuminate\Contracts\Support\Jsonable;

class Track implements Jsonable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $artists;

    /**
     * @var string
     */
    private $previewUrl;

    public function __construct(string $id, string $name, array $artists, string $previewUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->artists = $artists;
        $this->previewUrl = $previewUrl;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getArtistNames(): string
    {
        return \collect($this->artists)->implode('name', ', ');
    }

    /**
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return $this->previewUrl;
    }

    /**
     * @param array $data
     *
     * @return Track|null
     */
    public static function createFromSpotifyData(array $data): ?Track
    {
        if (self::isInvalidTrackData($data)) {
            return null;
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['artists'],
            $data['preview_url']
        );
    }

    private static function isInvalidTrackData(array $data): bool
    {
        return empty($data['artists']) || empty($data['preview_url']) || ('track' !== $data['type']);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return \json_encode([
            'id' => $this->getId(),
            'name' => $this->getName(),
            'artists' => $this->getArtistNames(),
            'preview_url' => $this->getPreviewUrl(),
        ], $options);
    }
}
