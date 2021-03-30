<?php
declare(strict_types=1);

namespace S3Bucket\Model\Behavior;

use S3Bucket\Datasource\S3Bucket;
use Cake\ORM\Behavior;
use S3Bucket\Datasource\S3BucketRegistry;


class S3MultipartUploadBehavior extends Behavior
{
    protected $_bucket;

    public function initialize(array $config): void
    {
        $this->_bucket = S3BucketRegistry::init()->get($config['bucketName']);
        parent::initialize($config);
    }


    public function fileExists(string $key, array $options = []): bool {
        return $this->_bucket->doesObjectExist($key, $options);
    }


    public function multipartS3Upload(string $key, $content, array $options = []): bool {
        return $this->_bucket->multipartUpload($key, $content, $options);
    }
}