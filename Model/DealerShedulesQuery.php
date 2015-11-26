<?php

namespace Dealer\Model;

use Dealer\Model\Base\DealerShedulesQuery as BaseDealerShedulesQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Propel\Runtime\ActiveQuery\Criteria;


/**
 * Skeleton subclass for performing query and update operations on the 'dealer_shedules' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class DealerShedulesQuery extends BaseDealerShedulesQuery
{
    public function filterByPeriodNotNull()
    {
        return $this->where(DealerShedulesTableMap::PERIOD_BEGIN . " " . Criteria::ISNOTNULL . " " . Criteria::LOGICAL_AND . " " . DealerShedulesTableMap::PERIOD_END . " " . Criteria::ISNOTNULL);

    }
    public function filterByPeriodNull()
    {
        return $this->where(DealerShedulesTableMap::PERIOD_BEGIN . " " . Criteria::ISNULL . " " . Criteria::LOGICAL_AND . " " . DealerShedulesTableMap::PERIOD_END . " " . Criteria::ISNULL);

    }
} // DealerShedulesQuery
