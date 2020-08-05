<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /* @var $productGodfather Product */
        $productGodfather = $this->getReference(ProductFixtures::GODFATHER);

        /* @var $productSteveJobs Product */
        $productSteveJobs = $this->getReference(ProductFixtures::STEVE_JOBS);

        /* @var $productSherlockHolmes Product */
        $productSherlockHolmes = $this->getReference(ProductFixtures::SHERLOCK_HOLMES);

        /* @var $productLittlePrince Product */
        $productLittlePrince = $this->getReference(ProductFixtures::LITTLE_PRINCE);

        /* @var $productIHateMyselfie Product */
        $productIHateMyselfie = $this->getReference(ProductFixtures::I_HATE_MYSELFIE);

        /* @var $productTheTrial Product */
        $productTheTrial = $this->getReference(ProductFixtures::THE_TRIAL);

        $cart1 = new Cart();
        $cart1->addProduct($productGodfather);
        $cart1->addProduct($productGodfather);
        $cart1->addProduct($productSteveJobs);
        $manager->persist($cart1);

        $cart2 = new Cart();
        $cart2->addProduct($productSherlockHolmes);
        $cart2->addProduct($productTheTrial);
        $manager->persist($cart2);

        $cart3 = new Cart();
        $cart3->addProduct($productLittlePrince);
        $cart3->addProduct($productIHateMyselfie);
        $cart3->addProduct($productTheTrial);
        $manager->persist($cart3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ProductFixtures::class,
        );
    }
}
