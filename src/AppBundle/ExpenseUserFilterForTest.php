<?php
namespace AppBundle;

use Doctrine\ORM\Mapping\ClassMetaData;  
use Doctrine\ORM\Query\Filter\SQLFilter;  
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExpenseUserFilterForTest extends SQLFilter  
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        return '';  // no additional WHERE clause for testing.
    }
}
