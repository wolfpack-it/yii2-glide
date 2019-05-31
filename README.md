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
        'path' => '/path/to/source-storage'
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
                'path' => '/path/to/cache-storage'
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

## TODO
- Signing of urls with [Sam IT Url Signer](https://github.com/SAM-IT/yii2-urlsigner)
- Add tests 

## Credits
- [Joey Claessen](https://github.com/joester89)
- [Wolfpack IT](https://github.com/wolfpack-it)

## License

The MIT License (MIT). Please see [LICENSE](https://github.com/wolfpack-it/yii2-glide/blob/master/LICENSE) for more information.