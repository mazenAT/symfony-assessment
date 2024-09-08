<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Country;
use App\Entity\Currency;


class DataFetcherController extends AbstractController
{
    private $manager;
    public function __construct(private HttpClientInterface $client, EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public function fetchDataFromApi ():void {
        //retrieving countries from the api endpoint
        $response = $this->client->request(
            'GET',
            $_SERVER['API_ENDPOINT']
        );

        //converting the data coming form api end point to array to be easy in handling it
        $content = $response->toArray();

        //creating the country object to save in data base that
        for ($i=0; $i < sizeof($content); $i++) {
            $country = new Country();
            $country->setUuid($i);
            //data coming from api is consisting of to many types of names so extracting the common one
            foreach ($content[$i]['name'] as $key => $value) {
                if ($key === 'common') {
                    $country->setName($value);
                }
            }
            $country->setRegion($content[$i]['region']);
            $country->setSubRegion($content[$i]['subregion']);
            //data coming from api is consisting of to many types of demonyms so extracting the eng one
            foreach ($content[$i]['demonyms'] as $key => $value) {
                if ($key === 'eng') {
                    $country->setDemonym($value['f']);
                }
            }

            $country->setPopulation($content[$i]['population']);
            if (!empty($content[$i]['independent'])) {
                $country->setIndependant($content[$i]['independent']);
            }
            $country->setFlag($content[$i]['flag']);
            //creating the currency object to be added to the country
            $currency = new Currency();
            foreach ($content[$i]['currencies'] as $currencyItem => $currencyValue) {
                $currency->getId();
                $currency->setName($currencyValue['name']);
                $currency->setSymbol($currencyValue['symbol']);
                $country->setCurrency($currency);
            }

            //saving the retrieved country data to the database
            $this->manager->persist($country);
            $this->manager->flush();
        }
    }
}
