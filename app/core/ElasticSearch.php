<?php

use Elasticsearch\ClientBuilder;

/**
 * ElasticSearch
 * @author ROC <i@rocs.me>
 */
class ElasticSearch
{
    private $client;
    
    /**
     * 构造函数
     *
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->client = ClientBuilder::create()->setHosts($configs)->build();
    }

    /**
     * 创建索引（只能创建一次）
     *
     * @param string $indexName
     * @return mixed
     */
    public function createIndex($indexName)
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 5,
                    'number_of_replicas' => 0
                ]
            ]
        ];

        try {
            return $this->client->indices()->create($params);
        } catch (Elasticsearch\Common\Exceptions\BadRequest400Exception $e) {
            $msg = $e->getMessage();
            $msg = json_decode($msg, true);

            return $msg;
        }
    }
    
    /**
     * 检测索引是否存在
     *
     * @param string $indexName
     * @return boolean
     */
    public function existsIndex($indexName)
    {
        try {
            $this->client->indices()->getSettings([
                'index' => $indexName
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * 删除索引
     *
     * @param string $indexName
     * @return mixed
     */
    public function deleteIndex($indexName)
    {
        $params = ['index' => $indexName];
        $response = $this->client->indices()->delete($params);

        return $response;
    }

    /**
     * 创建文档映射
     *
     * @param array $properties
     * @param string $indexName
     * @param string $typeName
     * @return mixed
     */
    public function createMappings(array $properties, $indexName, $typeName)
    {
        $response = $this->client->indices()->putMapping([
            'index' => $indexName,
            'type' => $typeName,
            'body' => [
                $typeName => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => $properties,
                ]
            ]
        ]);

        return $response;
    }

    /**
     * 查看文档映射
     *
     * @param string $indexName
     * @param string $typeName
     * @return mixed
     */
    public function getMappings($indexName, $typeName)
    {
        $response = $this->client->indices()->getMapping([
            'index' => $indexName,
            'type' => $typeName
        ]);

        return $response;
    }

    /**
     * 添加文档
     *
     * @param string $id
     * @param array $doc
     * @param string $indexName
     * @param string $typeName
     * @return mixed
     */
    public function addDoc($id, array $doc, $indexName, $typeName)
    {
        $response = $this->client->index([
            'index' => $indexName,
            'type' => $typeName,
            'id' => $id,
            'body' => $doc
        ]);

        return $response;
    }

    /**
     * 判断文档是否存在
     *
     * @param string $id
     * @param string $indexName
     * @param string $typeName
     * @return boolean
     */
    public function existsDoc($id, $indexName, $typeName)
    {
        $response = $this->client->exists([
            'index' => $indexName,
            'type' => $typeName,
            'id' => $id,
        ]);

        return $response;
    }


    /**
     * 获取文档
     *
     * @param string $id
     * @param string $indexName
     * @param string $typeName
     * @return array
     */
    public function getDoc($id, $indexName, $typeName)
    {
        $response = $this->client->get([
            'index' => $indexName,
            'type' => $typeName,
            'id' => $id,
        ]);

        return $response;
    }

    /**
     * 搜索文档
     *
     * @param array $body
     * @param string $indexName
     * @param string $typeName
     * @return array
     */
    public function search(array $body, $indexName, $typeName)
    {
        return $this->client->search([
            'index' => $indexName,
            'type' => $typeName,
            'body' => $body
        ]);
    }

    /**
     * 更新文档
     *
     * @param string $id
     * @param array $doc
     * @param string $indexName
     * @param string $typeName
     * @return mixed
     */
    public function updateDoc($id, array $doc, $indexName, $typeName)
    {
        $response = $this->client->update([
            'index' => $indexName,
            'type' => $typeName,
            'id' => $id,
            'body' => [
                'doc' => $doc
            ]
        ]);

        return $response;
    }

    /**
     * 删除文档
     *
     * @param string $id
     * @param string $indexName
     * @param string $typeName
     * @return mixed
     */
    public function deleteDoc($id, $indexName, $typeName)
    {
        $response = $this->client->delete([
            'index' => $indexName,
            'type' => $typeName,
            'id' => $id
        ]);

        return $response;
    }
}