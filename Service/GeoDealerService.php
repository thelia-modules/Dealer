<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace Dealer\Service;

use Dealer\Dealer;
use Dealer\Model\DealerQuery;

/**
 * Class GeoDealerService
 * @package Dealer\Service
 */
class GeoDealerService
{
    public function updateFromArray($data)
    {
        $dealer = $this->hydrateObjectArray($data);

        if ($dealer) {
            $dealer->save();
        }

        return $dealer;
    }

    protected function hydrateObjectArray($data)
    {
        $model = new Dealer();

        if (isset($data['id'])) {
            $dealer = DealerQuery::create()->findOneById($data['id']);
            if ($dealer) {
                $model = $dealer;
            }
        } else {
            return null;
        }

        if (isset($data['latitude'])) {
            $model->setLatitude($data['latitude']);
        }

        if (isset($data['longitude'])) {
            $model->setLongitude($data['longitude']);
        }


        return $model;
    }
}