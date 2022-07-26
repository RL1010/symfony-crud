<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    ) {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
    }

    public function saveProduct($name, $description, $price, $amount, $url)
    {

        $newProduct = new Product();

        $newProduct
            ->setName($name)
            ->setDescription($description)
            ->setPrice($price)
            ->setAmount($amount)
            ->setUrl($url);


        $this->manager->persist($newProduct);
        $this->manager->flush();
    }

    public function updateProduct(Product $product): Product
    {
        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }

    public function removeProduct(Product $product)
    {
        $this->manager->remove($product);
        $this->manager->flush();
    }
}
