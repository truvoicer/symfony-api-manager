<?php
namespace App\Service;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class SerializerService {

    private $classMetadataFactory;

    public function __construct()
    {
        $this->classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    }

    public function entityToJson($entity, array $groups) {
        $normalizer = new ObjectNormalizer($this->classMetadataFactory, null, null,
            null, null, null, $this->getDefaultContext());
        $serializer = new Serializer([$normalizer], [new JsonEncoder()] );

        return $serializer->serialize($entity, "json", ["groups" => $groups]);
    }

    public function entityToArray($entity, array $groups = ['main']) {
        $normalizer = new ObjectNormalizer($this->classMetadataFactory, new CamelCaseToSnakeCaseNameConverter(), null,
            null, null, null, $this->getDefaultContext());
        $serializer = new Serializer([$normalizer]);
        return $serializer->normalize($entity, null,
            [
                "groups" => $groups
            ]);
    }

    public function entityArrayToArray(array $entityArray, array $groups = ['main']) {
        return array_map(function ($item) use($groups) {
            return $this->entityToArray($item, $groups);
        }, $entityArray);
    }

    private function getDefaultContext() {
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };
        return [
            ObjectNormalizer::CALLBACKS => [
                'date_added' => $dateCallback,
                'date_updated' => $dateCallback,
                'expiresAt' => $dateCallback
            ],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object;
            },
        ];
    }
}