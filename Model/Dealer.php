<?php

namespace Dealer\Model;

use Dealer\Model\Base\Dealer as BaseDealer;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Dealer\Model\DealerShedulesQuery as ChildDealerShedulesQuery;
use Propel\Runtime\Exception\PropelException;
use Dealer\Model\DealerShedules as ChildDealerShedules;

class Dealer extends BaseDealer
{

    /**
     * @var        ObjectCollection|ChildDealerShedules[] Collection to store aggregation of ChildDealerShedules objects.
     */
    protected $collDealerDefaultSheduless;

    /**
     * @var        ObjectCollection|ChildDealerShedules[] Collection to store aggregation of ChildDealerShedules objects.
     */
    protected $collDealerExtraSheduless;

    /**
     * Initializes the collDealerSheduless collection.
     *
     * By default this just sets the collDealerSheduless collection to an empty array (like clearcollDealerSheduless());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerDefaultSheduless($overrideExisting = true)
    {
        if (null !== $this->collDealerDefaultSheduless && !$overrideExisting) {
            return;
        }
        $this->collDealerDefaultSheduless = new ObjectCollection();
        $this->collDealerDefaultSheduless->setModel('\Dealer\Model\DealerShedules');
    }

    /**
     * Initializes the collDealerSheduless collection.
     *
     * By default this just sets the collDealerSheduless collection to an empty array (like clearcollDealerSheduless());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerExtraSheduless($overrideExisting = true)
    {
        if (null !== $this->collDealerExtraSheduless && !$overrideExisting) {
            return;
        }
        $this->collDealerExtraSheduless = new ObjectCollection();
        $this->collDealerExtraSheduless->setModel('\Dealer\Model\DealerShedules');
    }

    /**
     * Gets an array of ChildDealerShedules objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerShedules[] List of ChildDealerShedules objects
     * @throws PropelException
     */
    public function getDefaultSchedules($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerShedulessPartial && !$this->isNew();
        if (null === $this->collDealerDefaultSheduless || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerDefaultSheduless) {
                // return empty collection
                $this->initDealerDefaultSheduless();
            } else {
                $collDealerSheduless = ChildDealerShedulesQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->filterByPeriodNull()
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerShedulessPartial && count($collDealerSheduless)) {
                        $this->initDealerDefaultSheduless(false);

                        foreach ($collDealerSheduless as $obj) {
                            if (false == $this->collDealerDefaultSheduless->contains($obj)) {
                                $this->collDealerDefaultSheduless->append($obj);
                            }
                        }

                        $this->collDealerShedulessPartial = true;
                    }

                    reset($collDealerSheduless);

                    return $collDealerSheduless;
                }

                if ($partial && $this->collDealerDefaultSheduless) {
                    foreach ($this->collDealerDefaultSheduless as $obj) {
                        if ($obj->isNew()) {
                            $collDealerSheduless[] = $obj;
                        }
                    }
                }

                $this->collDealerDefaultSheduless = $collDealerSheduless;
                $this->collDealerShedulessPartial = false;
            }
        }

        return $this->collDealerDefaultSheduless;
    }

    /**
     * Gets an array of ChildDealerShedules objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerShedules[] List of ChildDealerShedules objects
     * @throws PropelException
     */
    public function getExtraSchedules($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerShedulessPartial && !$this->isNew();
        if (null === $this->collDealerExtraSheduless || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerSheduless) {
                // return empty collection
                $this->initDealerExtraSheduless();
            } else {
                $collDealerSheduless = ChildDealerShedulesQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->filterByPeriodNotNull()
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerShedulessPartial && count($collDealerSheduless)) {
                        $this->initDealerExtraSheduless(false);

                        foreach ($collDealerSheduless as $obj) {
                            if (false == $this->collDealerExtraSheduless->contains($obj)) {
                                $this->collDealerExtraSheduless->append($obj);
                            }
                        }

                        $this->collDealerShedulessPartial = true;
                    }

                    reset($collDealerSheduless);

                    return $collDealerSheduless;
                }

                if ($partial && $this->collDealerSheduless) {
                    foreach ($this->collDealerExtraSheduless as $obj) {
                        if ($obj->isNew()) {
                            $collDealerSheduless[] = $obj;
                        }
                    }
                }

                $this->collDealerExtraSheduless = $collDealerSheduless;
                $this->collDealerShedulessPartial = false;
            }
        }

        return $this->collDealerExtraSheduless;
    }

}
