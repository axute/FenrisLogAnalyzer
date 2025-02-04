<?php

namespace Fenris;

class Directory
{

    protected const FENRIS_DEBUG_D_TXT = '/_*FenrisDebug[-\d]*\.txt$/';

    protected array $debugFiles = [];
    protected bool $isCache;
    protected string $dirpath;
    protected Analyzer $analyzer;

    public function __construct(Analyzer $analyzer, string $dirpath, bool $isCache = false)
    {
        $this->analyzer = $analyzer;
        $this->isCache = $isCache;
        $this->dirpath = $dirpath;
        $this->debugFiles = $this->getFilesByPattern();
    }

    /**
     * @return File[]
     */
    public function getDebugFiles(): array
    {
        $fenris_files = array_map(function (string $filepath) {
            return new File($this, $filepath, $this->isCache);
        }, $this->debugFiles);
        return array_filter($fenris_files, function ($fenrisFile) {
            return $fenrisFile->isDuplicate() == false;
        });
    }


    protected function getFilesByPattern(): array
    {
        $filePaths = [];
        $files = scandir($this->dirpath);

        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (preg_match(self::FENRIS_DEBUG_D_TXT, $file) || $this->isCache) {
                    $filePaths[] = $this->dirpath . '/' . $file;
                }
            }
        }

        return $filePaths;
    }

    public function getAnalyzer(): Analyzer
    {
        return $this->analyzer;
    }
}