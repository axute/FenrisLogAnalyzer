<?php
namespace Fenris;

class Analyzer
{
    /**
     * @var Directory[]
     */
    protected array $fenrisDirs = [];
    protected string $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $cacheDirectory = rtrim($cacheDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (is_dir($cacheDirectory) === false) {
            mkdir($cacheDirectory);
        }
        $this->cacheDirectory = $cacheDirectory;
        $this->analyze($this->getCacheDirectory(), true);
    }

    public function analyze(string $directory, bool $isCache = false): self
    {
        $this->fenrisDirs[] = new Directory($this, $directory, $isCache);
        return $this;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function writeFiles(string $outputDiretory): self
    {
        if (is_dir($outputDiretory) === false) {
            mkdir($outputDiretory);
        }
        $outputDiretory = rtrim($outputDiretory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->writeTypes($outputDiretory);
        $this->writeCronic($outputDiretory);
        return $this;
    }

    protected function writeTypes(string $outputDiretory): self
    {
        $errors = [];
        $this->runErrors(function (Message $message) use (&$errors) {
            $errors[$message->getErrorType()][$message->getUtime()] = $message;
        });
        ksort($errors);
        array_walk($errors, function (&$value) {
            ksort($value);
        });
        foreach ($errors as $type => $times) {
            $outputFile = $outputDiretory . $type . '.txt';
            file_put_contents($outputFile, implode("\n", $times));
        }
        return $this;
    }

    protected function runErrors(callable $callback): self
    {
        foreach ($this->fenrisDirs as $fenrisDir) {
            foreach ($fenrisDir->getDebugFiles() as $debugFile) {
                foreach ($debugFile->getErrors() as $message) {
                    $callback($message);
                }
            }
        }
        return $this;
    }

    protected function writeCronic(string $outputDiretory)
    {
        $dieErrors = [];
        $cronic = $result = [];
        $this->runErrors(function (Message $message) use (&$cronic) {
            $cronic[$message->getUtime()] = $message;
        });
        ksort($cronic);

        $lastMessage = null;
        $addDieFile = false;
        /** @var Message $message */
        foreach ($cronic as $time => $message) {
            //  first message, nothing to handle
            if ($lastMessage === null) {
                $message->firstLine = true;
                $result[$time] = $message;
                $lastMessage = $message;
                continue;
            }
            //  another message with same content as last
            if ($lastMessage->getMessageHash() === $message->getMessageHash()) {
                $lastMessage->similarity++;
                continue;
            }
            //  file is changed, add separator
            if ($lastMessage->getFile()->getModificationTime() !== $message->getFile()->getModificationTime()) {
                $message->firstLine = true;
                $addDieFile = true;
            } else if ($lastMessage->getMemoryOrCoutner() > $message->getMemoryOrCoutner()) {
                $message->firstLine = true;
                $addDieFile = true;
            }

            $result[$time] = $message;
            if ($addDieFile === true) {
                $dieErrors[] = $lastMessage . "\n" . $message;
                $addDieFile = false;
            }
            $lastMessage = $message;
        }
        file_put_contents($outputDiretory . 'Summary_cronic.txt', implode("\n", $result) . "\n");
        file_put_contents($outputDiretory . 'Summary_game_died.txt', implode("\n", $dieErrors) . "\n");

    }
}

