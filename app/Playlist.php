<?php

namespace App;

use Illuminate\Contracts\Support\Jsonable;

class Playlist implements Jsonable
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
     * @var string
     */
    private $ownerId;

    /**
     * @var string
     */
    private $imageUrl;

    public function __construct(string $id, string $name, string $ownerId, string $imageUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->imageUrl = $imageUrl;
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
    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
    
    /**
     * @param array $data
     *
     * @return Playlist|null
     */
    public static function createFromSpotifyData(array $data): ?Playlist
    {
        if (null === $data['name'] || empty($data['images'])) {
            return null;
        }
        
        return new self(
            $data['id'],
            $data['name'],
            $data['owner']['id'],
            $data['images'][0]['url']
        );
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
        ], $options);
    }
}
