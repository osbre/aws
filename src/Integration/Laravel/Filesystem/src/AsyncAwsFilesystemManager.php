<?php

namespace AsyncAws\Illuminate\Filesystem;

use AsyncAws\SimpleS3\SimpleS3Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\AsyncAwsS3\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class AsyncAwsFilesystemManager extends FilesystemManager
{
    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @return FilesystemAdapter
     */
    public function createAsyncAwsS3Driver(array $config)
    {
        $s3Config = [];
        if (!empty($config['key']) && !empty($config['secret'])) {
            $s3Config['accessKeyId'] = $config['key'];
            $s3Config['accessKeySecret'] = $config['secret'];
            $s3Config['sessionToken'] = $config['token'] ?? null;
        }

        if (!empty($config['endpoint'])) {
            $s3Config['endpoint'] = $config['endpoint'];
        }

        if (!empty($config['region'])) {
            $s3Config['region'] = $config['region'];
        }

        $root = $config['root'] ?? '';
        $options = $config['options'] ?? [];

        $visibility = new PortableVisibilityConverter(
            $config['visibility'] ?? Visibility::PUBLIC
        );

        $s3Client = new SimpleS3Client($s3Config);
        $flysystemAdapter = new AsyncAwsS3Adapter($s3Client, $config['bucket'], $root, $visibility, null, $options);

        return new AsyncAwsFilesystemAdapter($this->createFlysystem($flysystemAdapter, $config), $flysystemAdapter);
    }
}
