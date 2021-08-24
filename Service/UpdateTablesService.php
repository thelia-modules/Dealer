<?php


namespace Dealer\Service;


use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;
use Propel\Runtime\ServiceContainer\ServiceContainerInterface;
use Thelia\Install\Database;
use Thelia\Model\CountryQuery;
use Thelia\Model\Lang;

class UpdateTablesService
{
    const SERVICE_ID = 'dealer.command.update_tables';

    /**
     * @return array|string
     */
    public function updateTables()
    {
        $query = "SELECT id, address1, address2, zipcode, city, country, latitude, longitude, company, description FROM `dealer_tab`";

        $connection = Propel::getConnection();
        try {
            $stmt = $connection->prepare($query);
            $stmt->execute();

            $countriesNotFound = [];
            while ($results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $country = CountryQuery::create()
                    ->useCountryI18nQuery()
                        ->filterByTitle('%' . $results['country'] . '%', Criteria::LIKE)
                    ->endUse()
                    ->findOne()
                ;

                if (null === $country) {
                    $country = 64;
                    $countriesNotFound[] = $results ['country'];
                }

                $now = new \DateTime();

                $dealer = new Dealer();
                $dealer
                    ->setVisible(true)
                    ->setAddress1($results['address1'])
                    ->setAddress2($results['address2'])
                    ->setZipcode($results['zipcode'])
                    ->setCity($results['city'])
                    ->setCountry($country)
                    ->setLatitude($results['latitude'])
                    ->setLongitude($results['longitude'])
                    ->setCreatedAt($now)
                    ->setUpdatedAt($now)
                    ->setLocale(Lang::getDefaultLanguage()->getLocale())
                    ->setTitle($results['company'])
                    ->setDescription($results['description'])
                    ->save()
                ;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return $countriesNotFound;
    }
}