<?php

declare(strict_types=1);

namespace WolfpackIT\glide\actions;

use League\Glide\Server;
use WolfpackIT\glide\components\Glide;
use yii\base\Action;
use yii\di\Instance;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class GlideAction extends Action
{
    public Glide|string|array $glide = Glide::class;

    public function init()
    {
        $this->glide = Instance::ensure($this->glide, Glide::class);

        parent::init();
    }

    public function run(
        Request $request,
        Response $response,
        $path
    ) {
        $server = $this->getServer();

        if (!$server->sourceFileExists($path)) {
            throw new NotFoundHttpException('Image not found');
        }

        if (
            $server->cacheFileExists($path, [])
            && $server->getSource()->lastModified($path) >= $server->getCache()->lastModified($path)
        ) {
            $server->deleteCache($path);
        }

        try {
            $path = $server->makeImage($path, $request->get());

            $response->headers->add('Content-Type', $server->getCache()->mimeType($path));
            $response->headers->add('Content-Length', $server->getCache()->fileSize($path));
            $response->headers->add('Cache-Control', 'max-age=31536000, public');
            $response->headers->add('Expires', (new \DateTime('UTC + 1 year'))->format('D, d M Y H:i:s \G\M\T'));

            $response->format = Response::FORMAT_RAW;
            $response->stream = $server->getCache()->readStream($path);

            return $response;
        } catch (\Throwable $e) {
            throw new ServerErrorHttpException('Failed outputting the image.', 0, $e);
        }
    }

    protected function getServer(): Server
    {
        return $this->glide->getServer();
    }
}
