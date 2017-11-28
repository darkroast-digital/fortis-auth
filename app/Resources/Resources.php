<?php

namespace App\Resources;

class Resources
{
    protected $path;
    protected $files;
    protected $category;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function upload($category, $files)
    {
        $category = $category;

        $files = $files;
        $total = count($files['name']);

        $basePath = __DIR__ . '/../../assets/uploads/resources/';

        for ($i=0; $i < $total; $i++) { 
            if ($category == 'uncategorized') {
                $filePath = $basePath . $files['name'][$i];

                move_uploaded_file($files['tmp_name'][$i], $filePath);
            } else {
                if (!file_exists($basePath . $category)) {
                    mkdir($basePath . $category);
                }

                $filePath = $basePath . $category . '/' . $files['name'][$i];

                move_uploaded_file($files['tmp_name'][$i], $filePath);
            }
        }

        return true;
    }

    public function categories()
    {
        $categories = [];

        foreach ($this->listItems() as $item) {
            $path = $this->path . $item;

            if (is_dir($path)) {
                array_push($categories, $item);
            }
        }

        return $categories;
    }

    public function uncategorized()
    {
        $uncategorized = [];

        foreach ($this->listItems() as $item) {
            $path = $this->path . $item;

            if (!is_dir($path)) {
                array_push($uncategorized, $item);
            }
        }

        return $uncategorized;
    }

    public function index()
    {
        $uncategorized = $this->uncategorized();
        $categories = $this->categories();

        $resources = [];
        array_push($resources, [
            'uncategorized' => $uncategorized,
            'categories' => $categories
        ]);

        return $resources;
    }

    public function delete($file)
    {
        $path = __DIR__ . '/../../assets/uploads/resources/' . $file;

        if (is_dir($path)) {
            $this->removeDirectory($path);
        } else {
            unlink($path);
        }
    }

    public static function removeDirectory($dirPath) {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException('$dirPath must be a directory');
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function listItems()
    {
        $resource = preg_grep('/^([^.])/', scandir($this->path));
        unset($resource[0]);
        unset($resource[1]);

        return $resource;
    }
}
