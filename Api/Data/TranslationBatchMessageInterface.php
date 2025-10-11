<?php
namespace NativeMind\Translation\Api\Data;

interface TranslationBatchMessageInterface
{
    /**
     * Get batch ID
     *
     * @return string
     */
    public function getBatchId();

    /**
     * Set batch ID
     *
     * @param string $batchId
     * @return $this
     */
    public function setBatchId($batchId);

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType();

    /**
     * Set entity type
     *
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * Get entity IDs
     *
     * @return int[]
     */
    public function getEntityIds();

    /**
     * Set entity IDs
     *
     * @param int[] $entityIds
     * @return $this
     */
    public function setEntityIds(array $entityIds);

    /**
     * Get store IDs
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set store IDs
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds);

    /**
     * Get force flag
     *
     * @return bool
     */
    public function getForce();

    /**
     * Set force flag
     *
     * @param bool $force
     * @return $this
     */
    public function setForce($force);
}




