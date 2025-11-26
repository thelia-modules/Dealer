<?php

namespace Dealer\Model;

use Dealer\Model\Base\DealerShedules as BaseDealerShedules;
use Propel\Runtime\Map\TableMap;

class DealerShedules extends BaseDealerShedules
{
    public const DAYS = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
    ];

    /**
     * @inheritDoc
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false): array
    {
        $result = parent::toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, $includeForeignObjects);
        $result["dayFormated"] = $this->getDayFormated();
        $result["beginFormated"] = $this->getBegin("H:i");
        $result["endFormated"] = $this->getEnd("H:i");
        $result["periodBeginFormated"] = $this->getPeriodBegin("Y-m-d");
        $result["periodEndFormated"] = $this->getPeriodEnd("Y-m-d");

        return $result;
    }

    public function getDayFormated()
    {
        return self::DAYS[$this->getDay()] ?? null;
    }
}
