<?php
// app/controllers/CategoryController.php

require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    public function index()
    {
        $model = new Category();
        $categories = $model->getAll();
        include __DIR__ . '/../views/categories/index.php';
    }

    // по избор: add(), store(), edit(), delete() и др.
}
