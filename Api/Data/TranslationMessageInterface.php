<?php
namespace NativeMind\Translation\Api\Data;

interface TranslationMessageInterface
{
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
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

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




