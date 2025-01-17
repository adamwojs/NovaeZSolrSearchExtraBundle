<?php
/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use eZ\Publish\SPI\Persistence\Content\Type as ContentType;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;

class CustomFulltextFieldMapper extends CustomFieldMapper
{
    /**
     * @param array                                                         $fields
     * @param \eZ\Publish\SPI\Search\Field                                  $indexField
     * @param \eZ\Publish\SPI\Persistence\Content\Type                      $contentType
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param array                                                         $fieldNames
     */
    protected function appendField(
        array &$fields,
        Field $indexField,
        ContentType $contentType,
        FieldDefinition $fieldDefinition,
        array $fieldNames
    ): void {
        if (!$indexField->type instanceof FieldType\FullTextField || !$fieldDefinition->isSearchable) {
            return;
        }

        foreach ($fieldNames as $fieldName) {
            $fields[] = new Field(
                "meta_{$fieldName}__text",
                $indexField->value,
                $this->getIndexFieldType($contentType, $fieldName)
            );
        }
    }

    /**
     * Return index field type for the given $contentType.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type $contentType
     * @param string                                   $fieldName
     *
     * @return \eZ\Publish\SPI\Search\FieldType
     */
    private function getIndexFieldType(ContentType $contentType, $fieldName = 'text')
    {
        $newFieldType        = new FieldType\TextField();
        $newFieldType->boost = $this->boostFactorProvider->getContentMetaFieldBoostFactor(
            $contentType,
            $fieldName
        );

        return $newFieldType;
    }
}
