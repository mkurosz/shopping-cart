<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const GODFATHER = 'godfather-product-reference';
    public const STEVE_JOBS = 'steve-jobs-product-reference';
    public const SHERLOCK_HOLMES = 'sherlock-holmes-product-reference';
    public const LITTLE_PRINCE = 'little-prince-product-reference';
    public const I_HATE_MYSELFIE = 'i-hate-myselfie-product-reference';
    public const THE_TRIAL = 'the-trial-product-reference';

    public function load(ObjectManager $manager)
    {
        $productGodfather = new Product('The Godfather', 5999, 'PLN');
        $manager->persist($productGodfather);

        $productSteveJobs = new Product('Steve Jobs', 4995, 'PLN');
        $manager->persist($productSteveJobs);

        $productSherlockHolmes = new Product('The Return of Sherlock Holmes', 3999, 'PLN');
        $manager->persist($productSherlockHolmes);

        $productLittlePrince = new Product('The Little Prince', 2999, 'PLN');
        $manager->persist($productLittlePrince);

        $productIHateMyselfie = new Product('I Hate Myselfie!', 1999, 'PLN');
        $manager->persist($productIHateMyselfie);

        $productTheTrial = new Product('The Trial', 999, 'PLN');
        $manager->persist($productTheTrial);

        $manager->flush();

        $this->addReference(self::GODFATHER, $productGodfather);
        $this->addReference(self::STEVE_JOBS, $productSteveJobs);
        $this->addReference(self::SHERLOCK_HOLMES, $productSherlockHolmes);
        $this->addReference(self::LITTLE_PRINCE, $productLittlePrince);
        $this->addReference(self::I_HATE_MYSELFIE, $productIHateMyselfie);
        $this->addReference(self::THE_TRIAL, $productTheTrial);
    }
}