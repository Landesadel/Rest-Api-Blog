<?php

namespace Landesadel\easyBlog\Author;

class Name
{

    public function __construct(
        private string $firstName,
        private string $lastName
    ){
    }

    public function __toString() {
        return $this->firstName . " " . $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

}