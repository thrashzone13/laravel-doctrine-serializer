<?php

namespace Thrashzone13\LaravelDoctrineSerializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use LaravelDoctrine\ORM\Serializers\ArrayEncoder;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class LaravelDoctrineSerializerServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        /** Registering serializer annotations */
        AnnotationRegistry::loadAnnotationClass(Context::class);
        AnnotationRegistry::loadAnnotationClass(DiscriminatorMap::class);
        AnnotationRegistry::loadAnnotationClass(Groups::class);
        AnnotationRegistry::loadAnnotationClass(MaxDepth::class);
        AnnotationRegistry::loadAnnotationClass(SerializedName::class);
        AnnotationRegistry::loadAnnotationClass(Ignore::class);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder(), new ArrayEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter())
        ];

        $this->app->bind(SerializerInterface::class, function () use ($normalizers, $encoders) {
            return new Serializer($normalizers, $encoders);
        });
    }
}