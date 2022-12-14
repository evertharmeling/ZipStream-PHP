<?php

declare(strict_types=1);

namespace ZipStream\Test;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class EndlessCycleStream implements StreamInterface
{
    private int $offset = 0;

    public function __construct(private readonly string $toRepeat = '0')
    {
    }

    public function __toString(): string
    {
        throw new RuntimeException('Infinite Stream!');
    }

    public function close(): void
    {
        $this->detach();
    }

    /**
     * @return null
     */
    public function detach()
    {
        return;
    }

    /**
     * @return null
     */
    public function getSize()
    {
        return;
    }

    public function tell(): int
    {
        return $this->offset;
    }

    public function eof(): bool
    {
        return false;
    }

    public function isSeekable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        switch($whence) {
            case SEEK_SET:
                $this->offset = $offset;
                break;
            case SEEK_CUR:
                $this->offset += $offset;
                break;
            case SEEK_END:
                throw new RuntimeException('Infinite Stream!');
                break;
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write($string): int
    {
        throw new RuntimeException('Not writeable');
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read($length): string
    {
        $this->offset += $length;
        return substr(str_repeat($this->toRepeat, (int) ceil($length / strlen($this->toRepeat))), 0, $length);
    }

    public function getContents(): string
    {
        throw new RuntimeException('Infinite Stream!');
    }

    public function getMetadata($key = null): array|null
    {
        return $key !== null ? null : [];
    }
}
