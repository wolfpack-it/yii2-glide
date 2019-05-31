# Glide extension for Yii2

This extension provides [Glide](https://glide.thephpleague.com/) integration for the Yii2 Framework.

[Glide](https://glide.thephpleague.com/) is a package that makes image serving and manipulation really easy. Making use of [Flysystem](http://flysystem.thephpleague.com/) it also abstracts from filesystems.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require wolfpack-it/yii2-glide
```

or add

```
"wolfpack-it/yii2-glide": "^<latest version>"
```

to the `require` section of your `composer.json` file.

## Configuring

### Filesystems
The first step is configuring the filesystems. There can be three:
- The source filesystem (required)
- The cache filesystem (required)
- The watermak filesystem (optional)

Each filesystem can be configured as a component or directly in the container.

Component example:
```php
'components' => [
    'glideSource' => [
        'class' => \creocoder\flysystem\LocalFilesystem::class,
        'path' => '</path/to/source-storage>'
    ],
]
```

### Component
The configured filesystems can then be used in the Glide configuration:
```php
'container' => [
    'definitions' => [
        \WolfpackIT\glide\components\Glide::class => [
            'class' => \WolfpackIT\glide\components\Glide::class,
            'source' => 'glideSource', // via component
            'cache' => [
                'class' => \creocoder\flysystem\LocalFilesystem::class,
                'path' => '</path/to/cache-storage>'
            ], // via configuration
            'watermarks' => \creocoder\flysystem\AwsS3Filesystem:class // via container
        ]
    ]
]
```

### Controller action
The preferred usage is via an action in the controllers action method:
```php
class GlideController extends yii\web\Controller
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
                    'class' => \WolfpackIT\glide\actions\GlideAction::class
                ]
            ]
        );
    }
}
```

## Security
To protect your server agains attacks trying to resize loads of images it is a good idea to protect the urls. A good
package for that is [Sam-ITs Url Signer](https://github.com/SAM-IT/yii2-urlsigner). It signs urls with an expiration
and can lock the params if you don't want anyone to change images.

It is not included in the package since it is simple to configure:

#### Signer configuration

```php
'container' => [
    'definitions' => [
        \SamIT\Yii2\UrlSigner\UrlSigner::class => [
            'secret' => '<secret>',
        ],
    ]
]
```

#### HMAC filter in controller
```php
class GlideController extends yii\web\Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(
            [
                HmacFilter::class => [
                    'class' => HmacFilter::class,
                    'signer' => \Yii::$container->get(\SamIT\Yii2\UrlSigner\UrlSigner::class) //via Dependancy Injection
                    'signer' => $this->controller->module->get('<urlSignerComponent>') // via component
                ]
            ],
            parent::behaviors()
        );
    }
```

#### Signing urls
```php
$urlSigner = \Yii::createObject(\SamIT\Yii2\UrlSigner\UrlSigner::class);

$url = [
    '/img/index', // NOTE: This must be the route from the root 
    'path' => '</path/to/image>'
];
$allowAddition = true; // Whether or not to allow image modifications after url generation
$expiration = new DateTime())->add(new DateInterval('P7D'));

$urlSigner->signParams(
    $url,
    $allowAddition,
    $expiration
);

echo yii\helpers\Url::to($url, true);
```

## TODO
- Add tests 

## Credits
- [Joey Claessen](https://github.com/joester89)
- [Wolfpack IT](https://github.com/wolfpack-it)

## License

The MIT License (MIT). Please see [LICENSE](https://github.com/wolfpack-it/yii2-glide/blob/master/LICENSE) for more information.