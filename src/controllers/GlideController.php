<?php

namespace WolfpackIT\glide\actions;

use WolfpackIT\glide\actions\GlideAction;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Class GlideController
 * @package WolfpackIT\glide\actions
 */
class GlideController extends Controller
{
    /**
     * @return array
     */
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

    /**
     * @return array
     */
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
                ]
            ],
            parent::behaviors()
        );
    }
}