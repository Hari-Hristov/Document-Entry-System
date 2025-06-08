<?php
// app/controllers/CategoryController.php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/render.php'; // ако render() е в отделен файл

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        global $pdo;
        $this->categoryModel = new Category($pdo);
    }

    public function index()
    {
        $categories = $this->categoryModel->getAll();
        render('categories/index', ['categories' => $categories]);
    }

    public function create()
    {
        render('categories/create');
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        if (empty(trim($name))) {
            echo "Името на категорията не може да бъде празно.";
            return;
        }

        $this->categoryModel->create($name);
        header('Location: index.php?controller=category&action=index');
        exit;
    }
}
