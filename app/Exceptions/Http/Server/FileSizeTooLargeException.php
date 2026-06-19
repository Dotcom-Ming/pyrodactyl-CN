<?php

namespace Pterodactyl\Exceptions\Http\Server;

use Pterodactyl\Exceptions\DisplayException;

class FileSizeTooLargeException extends DisplayException
{
    /**
     * FileSizeTooLargeException constructor.
     */
    public function __construct()
    {
        parent::__construct(trans('strings.file_too_large'));
    }
}
