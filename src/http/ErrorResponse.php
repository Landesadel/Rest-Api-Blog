<?php


namespace Landesadel\easyBlog\http;


class ErrorResponse extends Response
{

    protected const SUCCESS = false;

    public function __construct(
        private string $reason = 'Something goes wrong'
    ) {
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }

}