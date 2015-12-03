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

namespace Dealer\Test\PhpUnit\Base;

use Dealer\Test\PhpUnit\Base\ContainerAwareTestCase;
use Propel\Runtime\Propel;

/**
 * Class AbstractPropelTest
 * @package Dealer\Test
 */
abstract class AbstractPropelTest extends ContainerAwareTestCase
{
    public static function setUpBeforeClass()
    {
        Propel::getConnection()->beginTransaction();
    }

    public static function tearDownAfterClass()
    {
        Propel::getConnection()->rollBack();
    }
}