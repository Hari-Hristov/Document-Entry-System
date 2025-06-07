<?php
// app/controllers/CategoryController.php

require_once _DIR_ . '/../models/Category.php';

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * Показва списък с категории
     */
    public function index()
    {
        $categories = $this->categoryModel->getAll();
        include _DIR_ . '/../views/categories/index.php';
    }

    /**
     * Показва форма за добавяне на категория
     */
    public function create()
    {
        include _DIR_ . '/../views/categories/create.php';
    }

    /**
     * Записва нова категория
     */
    public function store()
    {
        $name = $_POST['name'] ?? '';
        if (empty(trim($name))) {
            echo "Името на категорията не може да бъде празно.";
            return;
        }

        $this->categoryModel->create($name);
        header('Location: index.php?controller=category&action=index');
    }
}

