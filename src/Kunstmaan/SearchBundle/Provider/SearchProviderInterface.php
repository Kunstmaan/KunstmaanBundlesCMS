<?php

namespace Kunstmaan\SearchBundle\Provider;

interface SearchProviderInterface
{
    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName();

    /**
     * Return the client object
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Create an index
     *
     * @param string $indexName Name of the index
     */
    public function createIndex($indexName);

    /**
     * Return the index object
     *
     * @param $indexName
     *
     * @return mixed
     */
    public function getIndex($indexName);

    /**
     * Create a document
     *
     * @param string $uid
     * @param mixed  $document
     * @param string $indexName
     * @param string $indexType
     *
     * @return mixed
     */
    public function createDocument($document, $uid, $indexName = '', $indexType = '');

    /**
     * Add a document to the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index to add the document to
     * @param array  $document  The document to index
     * @param string $uid       Unique ID for this document, this will allow the document to be overwritten by new data
     *                          instead of being duplicated
     */
    public function addDocument($indexName, $indexType, $document, $uid);

    /**
     * Add a collection of documents at once
     *
     * @param mixed  $documents
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index the document is located
     *
     * @return mixed
     */
    public function addDocuments($documents, $indexName = '', $indexType = '');

    /**
     * delete a document from the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index the document is located
     * @param string $uid       Unique ID of the document to be delete
     */
    public function deleteDocument($indexName, $indexType, $uid);

    /**
     * @param string $indexName
     * @param string $indexType
     */
    public function deleteDocuments($indexName, $indexType, array $ids);

    /**
     * Delete an index
     *
     * @param string $indexName Name of the index to delete
     */
    public function deleteIndex($indexName);
}
