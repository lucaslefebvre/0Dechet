<?php

namespace App\EventSubscriber;

use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;

class GlobalVariables {

    private $categories;

    public function __construct(CategoryRepository $category)
    {
        return extract($this->categories = $category->findAll());
    }

}