<?php

namespace Fenris;

use DateTime;

class Message
{
    const DETAILS_SEPARATOR = "|";
    const INFO_INTERN_SEPARATOR = " ";
    const INFO_SEPARATOR = "\t";
    public int $similarity = 0;
    public bool $firstLine = false;
    public ?string $modifytime;
    public File $file;
    protected string $line;

    public function __construct(string $line, File $file)
    {
        $this->line = trim($line);
        $this->file = $file;
    }

    public static function isValid(string $line): bool
    {
        $parts = explode(self::INFO_INTERN_SEPARATOR, $line);
        if (count($parts) < 5) {
            return false;
        }
        if (stripos($line, self::INFO_SEPARATOR) === false) {
            return false;
        }
        $parts = explode(self::INFO_INTERN_SEPARATOR, explode(self::INFO_SEPARATOR, $line)[0]);
        if (is_numeric($parts[0]) || count(explode('.', $parts[1])) !== 3 || count(explode(':', $parts[2])) !== 3 || count(explode('.', $parts[2])) !== 2 || is_numeric($parts[3]) === false) {
            return false;
        }

        return true;
    }

    public function getMemoryOrCoutner(): string
    {
        return $this->getInfo()[3];
    }

    protected function getInfo(): array
    {
        return explode(self::INFO_INTERN_SEPARATOR, explode(self::INFO_SEPARATOR, $this->line)[0]);
    }

    public function getDetails(): string
    {
        return explode(self::DETAILS_SEPARATOR, $this->getMessage(), 2)[1] ?? '';
    }

    public function getMessage(): string
    {
        return explode(self::INFO_SEPARATOR, $this->line)[1];
    }

    public function getMessageHash(): string
    {
        return md5($this->getMessage());
    }

    public function getUtime(): string
    {
        $time = DateTime::createFromFormat('Y.m.d H:i:s.u', $this->getDate() . ' ' . $this->getTime());
        return $time->format('U.u');
    }

    public function getDate(): string
    {
        return $this->getInfo()[1];
    }

    public function getTime(): string
    {
        return $this->getInfo()[2];
    }

    public function __toString()
    {
        $suffix = ($this->similarity > 0) ? ' (x' . ($this->similarity + 1) . ')' : '';
        $prefix = ($this->firstLine) ? "\n" : '';
        return $prefix . $this->getFile()->getModificationTime() . ' ' . $this->line . $suffix;
    }

    public function getErrorType(): string
    {
        if ($this->isError()) {
            $subs = explode(']', $this->getMessage(), 2);
            return trim($subs[0], '][ ');
        }
        return 'unknown';
    }

    public function isError(): bool
    {
        return $this->getType() === 'E';
    }

    public function getType(): string
    {
        return $this->getInfo()[0];
    }

    public function appendLine(string $line)
    {
        $this->line .= ' ' . $line;
    }

    public function getFile(): File
    {
        return $this->file;
    }

}