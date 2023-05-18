<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Escaper;

// login controller
class LoginController extends Controller
{
    public function indexAction()
    {
        // default login view
    }

    public function loginAction()
    {
        $escaper = new Escaper();

        $email = $escaper->escapeHtml($this->request->getPost('email'));
        $pass = $escaper->escapeHtml($this->request->getPost('password'));

        $collection = $this->mongo->users;
        $item = $collection->findOne(['email' => $email, 'password' => $pass]);

        if ($item) {
            $this->log->warning("Successful Login : email=>$email && password => $pass");
            $this->response->redirect('../admin/product/index');
        } else {
            $this->log->warning("Wrong Credential : email=>$email");
            echo "Wrong Credentials";
            die;
        }
    }
}
