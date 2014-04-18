<?php

class DefaultController extends BackController
{
    public function actionIndex()
    {
        $this->render('index');
    }
}