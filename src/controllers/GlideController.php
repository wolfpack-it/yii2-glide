<?php

declare(strict_types=1);

namespace WolfpackIT\glide\controllers;

use WolfpackIT\glide\actions\GlideAction;
use WolfpackIT\glide\filters\SignatureFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class GlideController extends Controller
{
    public function actions(): array
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'index' => [
                    'class' => GlideAction::class
                ]
            ]
        );
    }

    public function behaviors(): array
    {
        return ArrayHelper::merge(
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index']
                        ]
                    ]
                ],
//                SignatureFilter::class => [
//                    'class' => SignatureFilter::class,
//                ]
            ],
            parent::behaviors()
        );
    }
}
