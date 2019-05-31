<?php

namespace WolfpackIT\glide\actions;

use League\Glide\Server;
use WolfpackIT\glide\components\Glide;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class GlideAction
 * @package WolfpackIT\glide\actions
 */
class GlideAction extends Action
{
    /**
     * Glide component
     *
     * @var string|array|Glide
     */
    public $glide = [
        'class' => Glide::class
    ];

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * GlideAction constructor.
     * @param Request $request
     * @param Response $response
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct(
        $id,
        $controller,
        Request $request,
        Response $response,
        $config = []
    ) {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($id, $controller, $config);
    }

    public function init()
    {
        $this->glide =
            is_string($this->glide) && $this->controller->module->has($this->glide)
                ? $this->controller->module->has($this->glide)
                : \Yii::createObject($this->glide);

        parent::init();
    }

    /**
     * @param $path
     * @return Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function run(
        $path
    ) {
        $server = $this->getServer();

        if (!$server->sourceFileExists($path)) {
            throw new NotFoundHttpException('Image not found');
        }

        if (
            $server->cacheFileExists($path, [])
            && $server->getSource()->getTimestamp($path) >= $server->getCache()->getTimestamp($path)
        ) {
            $server->deleteCache($path);
        }

        try {
            $path = $server->makeImage($path, $this->request->get());

            $this->response->headers->add('Content-Type', $server->getCache()->getMimetype($path));
            $this->response->headers->add('Content-Length', $server->getCache()->getSize($path));
            $this->response->headers->add('Cache-Control', 'max-age=31536000, public');
            $this->response->headers->add('Expires', (new \DateTime('UTC + 1 year'))->format('D, d M Y H:i:s \G\M\T'));

            $this->response->format = Response::FORMAT_RAW;
            $this->response->stream = $server->getCache()->readStream($path);

            return $this->response;
        } catch (\Throwable $e) {
            throw new ServerErrorHttpException('Failed outputting the image.', 0, $e);
        }
    }

    /**
     * @return Server
     */
    protected function getServer(): Server
    {
        return $this->glide->getServer();
    }
}