<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/api/products", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $products = $this->productRepository->findAll();


        foreach ($products as $product) {
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'amount' => $product->getAmount(),
                'url' => $product->getUrl(),
            ];
        }

        return new JsonResponse($productsData, Response::HTTP_OK);
    }


    /**
     * @Route("/api/products/{id}", name="get_one_product", methods={"GET"})
     */
    public function get($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if (empty($product)) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $productData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'amount' => $product->getAmount(),
            'url' => $product->getUrl(),
        ];

        return new JsonResponse($productData, Response::HTTP_OK);
    }

    /**
     * @Route("/api/products", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $price = $request->request->get('price');
        $amount = $request->request->get('amount');
        $url = $request->request->get('url');
        if (empty($name) || empty($description) || empty($price) || empty($url)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->productRepository->saveProduct($name, $description, $price, $amount, $url);
        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/products/{id}", name="update_product", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if (empty($product)) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        empty($request->request->get('name')) ? true : $product->setName($request->request->get('name'));
        empty($request->request->get('description')) ? true : $product->setDescription($request->request->get('description'));
        empty($request->request->get('price')) ? true : $product->setPrice($request->request->get('price'));
        empty($request->request->get('amount')) ? true : $product->setAmount($request->request->get('amount'));
        empty($request->request->get('url')) ? true : $product->setUrl($request->request->get('url'));

        $newProduct = $this->productRepository->updateProduct($product);

        return new JsonResponse($newProduct, Response::HTTP_OK);
    }

    /**
     * @Route("/api/products/{id}", name="delete_product", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if (empty($product)) {
            return new JsonResponse(['status' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->productRepository->removeProduct($product);

        return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_NO_CONTENT);
    }
}
