<?php
declare(strict_types = 1);

namespace Meetup\Domain\Model;

use Assert\Assertion;

final class Name
{
    /**
     * @var string
     */
    private $text;

    public static function fromString($text): Name
    {
        $name = new self();

        Assertion::notEmpty($text);
        $name->text = $text;

        return $name;
    }

    public function __toString(): string
    {
        return $this->text;
    }

    public function equals(Name $name): bool
    {
        return $this->text === $name->text;
    }
}
