<?php

namespace WolfpackIT\glide\filters;

use League\Glide\Signatures\Signature;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Request;

/**
 * Class SignatureFilter
 * @package WolfpackIT\glide\filters
 */
class SignatureFilter extends ActionFilter
{
    /**
     * @var Signature
     */
    public $signature = Signature::class;

    /**
     * @param Action $action
     * @return bool
     * @throws InvalidConfigException
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        /** @var Request $request */
        $request = $action->controller->module->get('request');
        $queryParams = $request->queryParams;
        $path = ArrayHelper::remove($queryParams, 'path');
        try {
            $this->signature->validateRequest($request->getPathInfo(), $queryParams);
        } catch (SignatureException $e) {
            throw new ForbiddenHttpException($e->getMessage(), 0, $e);
        }

        return $result;
    }

    public function init()
    {
        $this->signature = Instance::ensure($this->signature, Signature::class);

        parent::init();
    }
}