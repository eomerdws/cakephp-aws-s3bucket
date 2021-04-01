<?php
declare(strict_types=1);

namespace S3Bucket\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
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
    public function doesObjectExist(string $key, array $options = []): bool {
        return $this->_bucket->doesObjectExist($key, $options);
    }


    /**
     * @param string $key
     * @param $content
     * @param array $options
     * @return bool
     */
    public function multipartS3Upload(EntityInterface $entity): bool {
        $config = $this->getConfig();
        $key  = $entity->get($config['keyField']);
        $content = $entity->get($config['content']);
        $options = $entity->get($config['options']);

        $result = $this->_bucket->multipartUpload($key, $content, $options);
        return $result["@metadata"]["statusCode"] == '200';
    }


    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
        return $this->multipartS3Upload($entity);
    }
}