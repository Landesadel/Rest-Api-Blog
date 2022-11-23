<?php


namespace Landesadel\easyBlog;




use Landesadel\easyBlog\Exceptions\InvalidArgumentException;

class Uuid
{

    public function __construct(
        private string $uuidString
    ){
        if(!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malformed Uuid: $this->uuidString"
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }
    public function __toString(): string
    {
        return $this->uuidString;
    }

}