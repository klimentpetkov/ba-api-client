<?php

declare(strict_types = 1);
namespace App;


class FileCache
{
    private $dataFile = '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data.json';
    /**
     * Check if there is already cached data
     * @return bool
     */
    public function hasCachedData() : bool {
        return file_exists($this->dataFile);
    }

    /**
     * Write data to file as a cache storage
     * @param string $json
     * @return false|int
     * @throws \Exception
     */
    public function writeCachedData($json) {
        $result = file_put_contents($this->dataFile, $json);

        if ($result == false)
            throw new \Exception('Data cannot be written to file!');

        return $result;
    }

    /**
     * Read a json from cache file and convert it to array
     * @return array
     * @throws \Exception
     */
    public function readCachedData()
    {
        $result = file_get_contents($this->dataFile);

        if ($result == false)
            throw new \Exception('Data cannot be read from file!');

        return json_decode($result, true);
    }
}