<?php
declare(strict_types=1);

namespace S3Bucket\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use S3Bucket\Datasource\S3Bucket;
use Cake\ORM\Behavior;
use S3Bucket\Datasource\S3BucketRegistry;


class S3MultipartUploadBehavior extends Behavior
{
    protected $_bucket;
    // protected $_defaultConfig = [
    //     'keyField' => null,
    //     'content' => null,
    //     'modelName' => null,
    //     'options' => []
    // ];

    /**
     * @param array $config
     */
    public function initialize(array $config): void
    {
        $this->_bucket = S3BucketRegistry::init()->get($config['modelName']);
        parent::initialize($config);
    }

    /**
     * @param string $key
     * @param array $options
     * @return bool
     */
    public function doesObjectExist(string $key, array $options = []): bool
    {
        return $this->_bucket->doesObjectExist($key, $options);
    }


    /**
     * @param string $key
     * @param $content
     * @param array $options
     * @return bool
     */
    public function multipartS3Upload(ArrayObject $data): bool
    {
        $config = $this->getConfig();
        $key = $config['keyField'];
        $content = $config['content'];
        $options = $config['options'];

        $result = $this->_bucket->multipartUpload($key, $content, $options);
        return $result["@metadata"]["statusCode"] == '200';
    }


    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): bool
    {
        Log::warning("beforeSave on S3MultipartUploadBehavior called.");
        $result = $this->multipartS3Upload($data);
        if($result) {
            if(is_a($data->location, 'UploadedFile')) {
                $data->location = $data->location->getClientFilename;
            }
        }
        return $result;
    }
}