<?php

namespace App\Services;

use App\Repository\ProductRepository;

class ProductService
{
    public function getProductAll()
    {
        return $this->productRepository->findAll();
    }

    public function getProductMainAll()
    {
        return $this->productRepository->findBy([
            'category'=>'Main'
        ]);
    }


    public function getProductSubAll()
    {
        return $this->productRepository->findBy([
            'category'=>'Sub'
        ]);
    }

    public function getProductActive()
    {
        return $this->productRepository->findBy([
            'isActive'=>'1'
        ]);
    }
    public function getProductFooter()
    {
        return $this->productRepository->findBy([
            'includeInFooter'=>'1'
        ]);
    }
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
}