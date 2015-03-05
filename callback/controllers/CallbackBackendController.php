<?php

/**
 * CallbackBackendController контроллер для работы с конструктором форм обратной связи в панели управления
 *
 * @author WebGears team
 * @link http://none.shit
 * @copyright 2015 BlackTag && WebGears team
 * @package yupe.modules.callback.install
 * @license  BSD
 *
 **/
class CallbackBackendController extends yupe\components\controllers\BackController
{
	/**
     * Manages all models.
     *
     * @return void
     */
    public function actionIndex()
    {
        $this->render('index');
    }
}