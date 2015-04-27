<?php
namespace AppBundle;

use Doctrine\ORM\Mapping\ClassMetaData;  
use Doctrine\ORM\Query\Filter\SQLFilter;  
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExpenseUserFilter extends SQLFilter  
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $fieldName = 'user';
        $query = sprintf("%s.%s = 'UNKNOWN_USER'", $targetTableAlias, $fieldName);

        try {
            // Don't worry, getParameter automatically quotes parameters
            //#dump($this->getParameter('id'));#die;
            $userId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            // No user id has been defined
            throw new AccessDeniedHttpException('Not logged in');
            return $query;
        }

        if (empty($userId)) {
            throw new AccessDeniedHttpException('Not logged in');
            return $query;
        }

        $query = sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);
        return $query;
    }
}
