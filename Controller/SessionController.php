<?php

/**
 * Copyright © ContaoBlackForest
 *
 * @package   contao-form-save-to-data-container
 * @author    Sven Baumann <baumann.sv@gmail.com>
 * @author    Dominik Tomasi <dominik.tomasi@gmail.com>
 * @license   GNU/LGPL
 * @copyright Copyright 2014-2016 ContaoBlackForest
 */

namespace ContaoBlackForest\FormSave\Controller;

use Contao\Database\Result;
use Contao\Environment;
use Contao\Input;
use Contao\Session;

/**
 * The session controller.
 */
class SessionController
{
    const SESSION_NAME = 'form.save.to.data.container';

    /**
     * The Session.
     *
     * @var Session $session The session.
     */
    protected $session;

    /**
     * SessionController constructor.
     */
    public function __construct()
    {
        $this->session = Session::getInstance();

        if (!$this->getSession()->get(self::SESSION_NAME)
            && strpos(Input::post('FORM_SUBMIT'), 'auto_form_') === 0
        ) {
            $this->setState('create');
            $this->setFormPage(Environment::get('httpReferer'));
            $this->setPostFormSubmit(Input::post('FORM_SUBMIT'));
        }

        if (in_array($this->getState(), array('edit', 'saved'))
            && Input::get('id')
        ) {
            $this->setEditId(Input::get('id'));
        }
    }

    /**
     * Return the status.
     *
     * @return string The state.
     */
    public function getState()
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        return $data['state'];
    }

    /**
     * Set the status.
     *
     * @param string $state The state.
     *
     * @return void
     */
    public function setState($state)
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        $data['state'] = $state;

        $this->getSession()->set(self::SESSION_NAME, $data);
    }

    /**
     * Return the form page.
     *
     * @return string The form page.
     */
    public function getFormPage()
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        return $data['formPage'];
    }

    /**
     * Set the form page.
     *
     * @param string $formPage The form page.
     *
     * @return void
     */
    protected function setFormPage($formPage)
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        $data['formPage'] = $formPage;

        $this->getSession()->set(self::SESSION_NAME, $data);
    }

    /**
     * Return the post form submit.
     *
     * @return string The post form submit.
     */
    public function getPostFormSubmit()
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        return $data['postFormSubmit'];
    }

    /**
     * Set the post form submit.
     *
     * @param string $postFormSubmit The post form submit.
     *
     * @return void
     */
    protected function setPostFormSubmit($postFormSubmit)
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        $data['postFormSubmit'] = $postFormSubmit;

        $this->getSession()->set(self::SESSION_NAME, $data);
    }

    /**
     * Return the submit data.
     *
     * @return array The submit data.
     */
    public function getSubmitData()
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        return $data['submitData'];
    }

    /**
     * Set the submit data.
     *
     * @param array $postFormSubmit The submit data.
     *
     * @return void
     */
    public function setSubmitData($postFormSubmit)
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        $data['submitData'] = $postFormSubmit;

        $this->getSession()->set(self::SESSION_NAME, $data);
    }

    /**
     * Return the edit id.
     *
     * @return string The edit id.
     */
    public function getEditId()
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        if (!$data['editId']
            && array_key_exists('FE_DATA', $_SESSION)
            && array_key_exists(self::SESSION_NAME, $_SESSION['FE_DATA'])
            && array_key_exists('editId', $_SESSION['FE_DATA'][self::SESSION_NAME])
        ) {
            $data['editId'] = $_SESSION['FE_DATA'][self::SESSION_NAME]['editId'];
        }

        return $data['editId'];
    }

    /**
     * Set the edit id.
     *
     * @param string $postFormSubmit The edit id.
     *
     * @return void
     */
    protected function setEditId($postFormSubmit)
    {
        $data = $this->getSession()->get(self::SESSION_NAME);

        $data['editId'] = $postFormSubmit;

        $this->getSession()->set(self::SESSION_NAME, $data);
    }

    /**
     * Return the Session.
     *
     * @return Session The session.
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Remove the session.
     *
     * @return void
     */
    public function removeSession()
    {
        $this->getSession()->remove(self::SESSION_NAME);

        unset(
            $_SESSION['FE_DATA'][self::SESSION_NAME],
            $_SESSION['FORM_DATA'],
            $_POST
        );
    }
}

