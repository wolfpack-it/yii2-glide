<?php

declare(strict_types=1);

namespace WolfpackIT\glide\filters;

use League\Glide\Signatures\Signature;
use League\Glide\Signatures\SignatureException;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Request;

class SignatureFilter extends ActionFilter
{
    public Signature|array|string $signature = Signature::class;

    public function beforeAction($action): bool
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
