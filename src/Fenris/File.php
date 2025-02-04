<?php

namespace Fenris;

class File
{
    protected string $filepath;
    protected bool $isCache;
    protected bool $duplicate = false;
    protected Directory $directory;

    public function __construct(Directory $fileDiretory, string $filepath, bool $isCache)
    {
        $this->directory = $fileDiretory;
        $this->filepath = $filepath;
        $this->isCache = $isCache;
        $this->saveCache();
    }

    protected function saveCache(): bool
    {
        if ($this->isCache === false) {
            $cacheFile = $this->getDirectory()->getAnalyzer()->getCacheDirectory() . $this->getModificationTime() . '_' . $this->getMd5() . '.cache';
            if (file_exists($cacheFile) === false) {
                copy($this->filepath, $cacheFile);
                return true;
            } else {
                $this->duplicate = true;
            }
        }
        return false;
    }

    public function getModificationTime(): int
    {
        return filemtime($this->filepath);
    }

    protected function getMd5(): string
    {
        return md5_file($this->filepath);
    }

    public function isDuplicate(): bool
    {
        return $this->duplicate;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return array_filter(explode("\n", file_get_contents($this->filepath)), fn($line) => trim($line) !== '');
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        $lastMessage = null;
        $return = [];
        foreach ($this->getLines() as $line) {
            if (Message::isValid($line)) {
                $fenris_message = new Message($line, $this);
                $return[] = $lastMessage = $fenris_message;
            } else {
                if ($lastMessage !== null) {
                    $lastMessage->appendLine($line);
                }
            }
        }
        return $return;
    }

    /**
     * @return Message[]
     */
    public function getErrors(): array
    {
        return array_filter($this->getMessages(), function (Message $message) {
            return $message->isError();
        });
    }

    public function getDirectory(): Directory
    {
        return $this->directory;
    }
}