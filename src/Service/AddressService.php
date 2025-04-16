<?php

namespace App\Service;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Intl\Countries;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class AddressService
{
    private const GOOGLE_MAPS_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    private string $googleMapsApiKey;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private AddressRepository $addressRepository,
        private ValidatorInterface $validator,
        private HttpClientInterface $httpClient, // Client HTTP pour faire les requêtes vers Google Maps
        private ParameterBagInterface $parameterBag // Accès aux paramètres de l'environnement
    ) {
        // Récupérer la clé API depuis .env
        $this->googleMapsApiKey = $this->parameterBag->get('GOOGLE_MAPS_API_KEY');
    }

    public function createAddress(Address $address): Address
    {
        // Récupérer les coordonnées géographiques via Google Maps
        $this->setCoordinatesFromAddress($address);

        // Validation
        $this->validateAddress($address);

        // Sauvegarde
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $address;
    }

    public function updateAddress(int $id, Address $updatedAddress): ?Address
    {
        $address = $this->addressRepository->find($id);

        if (!$address) {
            return null;
        }

        // Mise à jour des champs
        $address->setStreet($updatedAddress->getStreet());
        $address->setCity($updatedAddress->getCity());
        $address->setZipCode($updatedAddress->getZipCode());
        $address->setCountry($updatedAddress->getCountry());
        $address->setLabe($updatedAddress->getLabe());
        $address->setLatitude($updatedAddress->getLatitude());
        $address->setLongitude($updatedAddress->getLongitude());
        $address->setUser($updatedAddress->getUser());

        // Valider après la mise à jour
        $this->validateAddress($address);

        $this->entityManager->flush();

        return $address;
    }

    public function deleteAddress(int $id): bool
    {
        $address = $this->addressRepository->find($id);

        if (!$address) {
            return false;
        }

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return true;
    }

    private function setCoordinatesFromAddress(Address $address): void
    {
        // Créer une chaîne d'adresse pour envoyer à l'API Google Maps
        $addressString = $address->getStreet() . ', ' . $address->getCity() . ', ' . $address->getZipCode() . ', ' . $address->getCountry();

        // Faire une requête à l'API Google Maps pour obtenir les coordonnées géographiques
        $response = $this->httpClient->request('GET', self::GOOGLE_MAPS_API_URL, [
            'query' => [
                'address' => $addressString,
                'key' => $this->googleMapsApiKey, // Utiliser la clé API récupérée depuis l'environnement
            ]
        ]);

        // Traiter la réponse de l'API
        $data = $response->toArray();

        // Vérifier si la réponse contient des résultats
        if (isset($data['results'][0])) {
            $location = $data['results'][0]['geometry']['location'];
            $address->setLatitude($location['lat']);
            $address->setLongitude($location['lng']);
        } else {
            throw new BadRequestHttpException('Impossible de récupérer les coordonnées géographiques pour l\'adresse fournie.');
        }
    }

    private function validateAddress(Address $address): void
    {
        // Définir les contraintes de validation
        $constraints = new Assert\Collection([
            'street' => [
                new Assert\NotBlank(message: 'La rue ne peut pas être vide.'),
                new Assert\Length(max: 255, maxMessage: 'La rue ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'city' => [
                new Assert\NotBlank(message: 'La ville ne peut pas être vide.'),
                new Assert\Length(max: 255, maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'zipCode' => [
                new Assert\NotBlank(message: 'Le code postal ne peut pas être vide.'),
                new Assert\Regex(pattern: '/^\d{5}$/', message: 'Le code postal doit contenir 5 chiffres.'),
            ],
            'country' => [
                new Assert\NotBlank(message: 'Le pays ne peut pas être vide.'),
                new Assert\Length(max: 255, maxMessage: 'Le pays ne peut pas dépasser {{ limit }} caractères.'),
                new Assert\Country(message: 'Le pays sélectionné n\'est pas valide.'),
            ],
            'labe' => [
                new Assert\Length(max: 255, maxMessage: 'Le label ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'latitude' => [
                new Assert\Type(type: 'float', message: 'La latitude doit être un nombre.'),
                new Assert\Range(notInRangeMessage: 'La latitude doit être entre -90 et 90.', min: -90, max: 90),
            ],
            'longitude' => [
                new Assert\Type(type: 'float', message: 'La longitude doit être un nombre.'),
                new Assert\Range(notInRangeMessage: 'La longitude doit être entre -180 et 180.', min: -180, max: 180),
            ],
            'user' => [
                new Assert\NotNull(message: 'L\'utilisateur associé ne peut pas être nul.'),
            ],
        ]);

        // Valider les contraintes
        $violations = $this->validator->validate($address, $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errorMessages));
        }
    }

    // Méthode pour obtenir la liste des pays pour un formulaire
    public function getCountryList(): array
    {
        // Obtenir les pays en utilisant le nom en français
        return Countries::getNames();
    }
}
