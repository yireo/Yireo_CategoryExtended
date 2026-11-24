<?php declare(strict_types=1);

namespace Yireo\CategoryExtended\Model\Category\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Exception as AttributeException;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Yireo\CategoryExtended\Util\CustomUrlValidator;

class CustomUrl extends AbstractBackend
{
    public function __construct(
        private readonly CustomUrlValidator $customUrlValidator
    ) {
    }

    /**
     * @param DataObject $object
     * @return bool
     * @throws LocalizedException
     */
    public function validate($object)
    {
        $this->validateCustomUrl($object);

        return parent::validate($object);
    }

    /**
     * @param DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        $this->validateCustomUrl($object);

        return parent::beforeSave($object);
    }

    /**
     * @throws LocalizedException
     */
    private function validateCustomUrl(DataObject $object): void
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attributeCode);
        if ($value === null) {
            return;
        }

        $value = trim((string)$value, " \f\n\r\t\v\x00");
        $object->setData($attributeCode, $value);
        if ($value === '' || $this->customUrlValidator->isValid($value)) {
            return;
        }

        $message = __('The Custom URL must be a http(s) URL or a relative path, without spaces, quotes or brackets.');
        $exception = new AttributeException($message);
        $exception->setAttributeCode($attributeCode);

        throw $exception;
    }
}
