<?php

namespace App\Exceptions;

use Exception;
use Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions;

class CustomException extends Exception implements RendersErrorsExtensions
{
    /**
    * @var @string 
    */
    private $reason;

    /**
    * @var @string 
    */
    private $category;

    /**
    * CustomException constructor.
    * 
    * @param  string  $message
    * @param  string  $reason
    * @return void
    */
    public function __construct(string $message, string $reason, string $category)
    {
        parent::__construct($message);

        $this->reason = $reason;
        $this->category = $category;
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @api
     * @return bool
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     *
     * @api
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Return the content that is put in the "extensions" part
     * of the returned error.
     *
     * @return array
     */
    public function extensionsContent(): array
    {
        return [
            'reason' => $this->reason,
        ];
    }
}