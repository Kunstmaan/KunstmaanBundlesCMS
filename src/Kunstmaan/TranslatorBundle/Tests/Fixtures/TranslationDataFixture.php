<?php

declare(strict_types=1);

namespace Kunstmaan\TranslatorBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Kunstmaan\TranslatorBundle\Entity\Translation;

/**
 * @internal
 */
final class TranslationDataFixture implements FixtureInterface
{
    /** @var Generator */
    private $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->errorKeys($manager);
        $this->exceptionKeys($manager);
        $this->extraSingleKey($manager, 'headers.frontpage', 'a not yet updated frontpage header', 'messages');
        $this->extraSingleKey($manager, 'validation.ok', 'Everything ok', 'validation');
        $this->extraSingleKey($manager, 'validation.in_the_future', 'This should be the last updated translation', 'validation', Translation::STATUS_ENABLED, true);
        $this->extraSingleKey($manager, 'these_are_deprecated', 'these are deprecated', 'messages', Translation::STATUS_DEPRECATED);

        $manager->flush();
    }

    private function errorKeys(ObjectManager $manager)
    {
        foreach (range(1, 50) as $i) {
            $translation = new Translation();
            $translation->setKeyword('errors.' . $this->faker->word() . '.' . $this->faker->word() . '_' . $i);
            $translation->setLocale('nl');
            $translation->setDomain('messages');
            $translation->setStatus(Translation::STATUS_ENABLED);
            $translation->setText($this->faker->text());
            $translation->setCreatedAt($this->faker->dateTime());

            $manager->persist($translation);
        }
    }

    private function exceptionKeys(ObjectManager $manager)
    {
        foreach (range(1, 5) as $i) {
            $translation = new Translation();
            $translation->setKeyword('exceptions.wrong_number.' . $this->faker->word() . '_' . $i);
            $translation->setLocale($this->faker->languageCode());
            $translation->setDomain('validation');
            $translation->setStatus(Translation::STATUS_ENABLED);
            $translation->setText($this->faker->text());
            $translation->setCreatedAt($this->faker->dateTime());

            $manager->persist($translation);
        }
    }

    private function extraSingleKey(ObjectManager $manager, string $key, string $message, string $domain, string $status = Translation::STATUS_ENABLED, bool $updated = false)
    {
        $translation = new Translation();
        $translation->setKeyword($key);
        $translation->setLocale('en');
        $translation->setDomain($domain);
        $translation->setStatus($status);
        $translation->setText($message);
        $translation->setCreatedAt($this->faker->dateTime());
        if ($updated) {
            $translation->setFlag(Translation::FLAG_NEW);
            $translation->setUpdatedAt($this->faker->dateTimeBetween('+1 days', '+200 days'));
        }

        $manager->persist($translation);
    }
}
