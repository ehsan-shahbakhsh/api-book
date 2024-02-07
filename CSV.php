<?php

namespace Jordan;

class CSV
{
    private string $filename;
    private $fileResource;
    private ?array $fileTitles;

    public function __construct(string $filename)
    {
        if (! file_exists($filename)) throw new \Error("$filename not exist");

        $this->filename = $filename;
        $this->fileResource = fopen($filename, "r+");
        $this->fileTitles = $this->titles();
    }

    public function content(): ?array
    {
        $data = [];
        while (! feof($this->fileResource)) {
            $content = fgetcsv($this->fileResource);
            if (is_array($content)) {
                $data[] = array_combine($this->fileTitles, $content);
            }
        }
        $this->resetFileGenerator();
        return $data;
    }

    public function load()
    {
        while (! feof($this->fileResource))
            yield array_combine($this->fileTitles, fgetcsv($this->fileResource));
    }

    public function getTitles(): ?array
    {
        return $this->fileTitles;
    }

    public function getLine(int $line): string|array
    {
        for ($i = 0; $i < $line - 1; $i++) {
            fgetcsv($this->fileResource);
        }
        $line = fgetcsv($this->fileResource);
        $this->resetFileGenerator();
        return array_combine($this->fileTitles, $line);
    }

    public function getRandomLine(): ?array
    {
        if (! feof($this->fileResource)) {
            $random = rand(1, $this->fileLen() - 1);
            for ($i = 0; $i < $random; $i++) fgetcsv($this->fileResource);
            $content = fgetcsv($this->fileResource);
            $this->resetFileGenerator();
            return array_combine($this->fileTitles, $content);
        }
        return [];
    }

    private function titles(): ?array
    {
        return fgetcsv($this->fileResource);
    }

    private function resetFileGenerator(): void
    {
        rewind($this->fileResource);
        fgetcsv($this->fileResource);
    }

    public function fileLen(): ?int
    {
        $line = 0;
        $fileResource = fopen($this->filename, "r+");
        while (! feof($fileResource)) {
            fgetcsv($fileResource);
            $line++;
        }
        return $line - 1;
    }

    public function __destruct()
    {
        fclose($this->fileResource);
    }
}