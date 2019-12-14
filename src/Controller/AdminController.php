<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminController
 * Manages the admin page
 * @package App\Controller
 */

class AdminController extends MainController
{
    /**
     * Renders the View Admin
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */

    public function defaultMethod()
    {
        session_start();

        self::redirectLogin();

        return $this->render('admin.twig', [
            'isActif' => self::isActif(),
            'isAdmin' => self::isAdmin(),
            'user' => self::getUser(),
            'type' => self::getType(),
            'action' => self::getAction(),
            'isError' => self::isError(),
            'requestUri' => self::getRequestUri(),

        ]);
    }

    public function getUserSession()
    {
        return $_SESSION['user'];
    }

    public function isAdmin()
    {
        if (self::getUserSession() != null) {
            return self::getUserSession()['admin'];
        }
    }

    public function isActif()
    {
        if (self::getUserSession() != null) {
            return self::getUserSession()['actif'];
        }
    }

    public function isError()
    {
        return filter_input(INPUT_GET, 'error');
    }

    public function getUser()
    {
        $userSession = self::getUserSession();
        if ($userSession !== null) {
            $array['prenom'] = $userSession['prenom'];
            $array['nom'] = $userSession['nom'];
            $array['email'] = $userSession['mail'];
            return $array;
        }
    }

    public function getType()
    {
        if (isset($_GET['type'])) {
            return $_GET['type'];
        }
    }

    public function getAction()
    {
        if (isset($_GET['action'])) {
            return $_GET['action'];
        }
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function redirectLogin()
    {
        if (self::getUserSession() == null) {
            $this->redirect('log', ['type' => 'connexion']);
        }
    }
}
