<?php
declare(strict_types=1);

namespace App\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Country;

#[Route('/api/doc')]
class CountryController extends AbstractController
{
    private $manager;
    private $countryRepo;
    private $serializer;

    public function __construct(
    CountryRepository $countryRepo,
    EntityManagerInterface $manager,
    SerializerInterface $serializer
    ) {
        $this->countryRepo = $countryRepo;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/{uuid}', name:'getCountry', methods: ['GET'])]
    public function getCountry(int $uuid): Response
    {
        $country = $this->countryRepo->find($uuid);

        if (!$country) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $data = $this->serializer->serialize($country, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/list', name:'getCountries',methods: ['GET'])]
    public function getCountries(): Response
    {
        $countries = $this->countryRepo->findAll();
        $data = $this->serializer->serialize($countries, 'json');

       return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/', name:'addCountry',methods: ['POST'])]
    public function addCountry(Request $request): Response
    {
        $requestData = $request->getContent();

       $country = $this->serializer->deserialize($requestData, Country::class, 'json');

       if (
        !$country->getUuid()||
        !$country->getRegion()||
        !$country->getSubRegion()||
        !$country->getDemonym()||
        !$country->getPopulation()||
        !$country->isIndependant()||
        !$country->getFlag()||
        !$country->getCurrency()
        ) {
           return new JsonResponse(['error' => 'Missing required fields'], 400);
       }

       $this->manager->persist($country);
       $this->manager->flush();

       $data = $this->serializer->serialize($country, 'json');

       return new JsonResponse(['message' => 'Country created!', 'country' => json_decode($data)], 201);
    }

    #[Route('/{uuid}', name:'updateCountry',methods: ['PATCH'])]
    public function updateCountry(Country $country,Request $request): Response
    {
        $requestData = $request->getContent();
       $updatedCountry = $serializer->deserialize($requestData, Country::class, 'json');

       $$country->setUuid($updatedCountry->getUuid());
       $$country->setName($updatedCountry->getName());
       $$country->setRegion($updatedCountry->getRegion());
       $$country->setSubRegion($updatedCountry->getSubRegion());
       $$country->setDemonym($updatedCountry->getDemonym());
       $$country->setPopulation($updatedCountry->getPopulation());
       $$country->setIndependant($updatedCountry->isIndependant());
       $$country->setFlag($updatedCountry->getFlag());
       $$country->setCurrency($updatedCountry->getCurrency());

       $this->manager->flush();

       return new Response('Country updated!', 200);
    }

    #[Route('/{uuid}', name:'deleteCountry',methods: ['DELETE'])]
    public function deleteCountry(Country $country): Response
    {
        $this->manager->remove($country);
        $this->manager->flush();

       return new Response('Country deleted!', 200);
    }
}