<?php

namespace Multi\Front\Controllers;

use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        // default login view
        $this->response->redirect("product/view");
    }
    public function viewAction()
    {
        $collection = $this->mongo->products->find();
        $this->view->data = $collection;
    }
    public function searchAction()
    {
        print_r($_POST['val']);
        $collection = $this->mongo->products;
        $fruitQuery = array('name' => $_POST['val']);
        $cursor = $collection->find($fruitQuery);
        $this->view->data = $cursor;
    }
    public function detailsAction()
    {
        $id = $_GET['id'];
        $collection = $this->mongo->products;
        $item = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        $this->view->data = $item;
    }
}
