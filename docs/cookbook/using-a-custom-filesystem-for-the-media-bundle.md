# Using a custom filesystem for the MediaBundle

The MediaBundle uses the [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle) to interact with the filesystem.
By default it uses a local filesystem. However, you can override the default adapter to have it use any filesystem supported by Gaufrette.

## Example S3 configuration

### S3 client

Create a S3 client for the adapter to work with.

```
s3:
    class: Aws\S3\S3Client
    arguments:
        options:
            version: latest
            region: "%s3.region%"
            credentials:
                key: "%s3.key%"
                secret: "%s3.secret%"
```

### S3 filesystem adapter

Override the default filesystem adapter service used by the MediaBundle.

```
kunstmaan_media.filesystem_adapter:
    class: Gaufrette\Adapter\AwsS3
    arguments:
        - "@s3"
        - "%s3.uploads_bucket%"
        - []
        - true # detect file content-type
```

Override the media path so files are stored in the buckets root.

```
parameters:
    kunstmaan_media.media_path: ""
```

### LiipImagine

Have the LiipImagine bundle stores its cache on S3.

```
liip_imagine.cache.resolver.s3:
    class: Liip\ImagineBundle\Imagine\Cache\Resolver\AwsS3Resolver
    arguments:
        - "@s3"
        - "%s3.cache_bucket%"
        - null
    tags:
        - { name: 'liip_imagine.cache.resolver', resolver: 's3' }
```

Use the CacheResolver and load data from S3.

```
liip_imagine:
    loaders:
        remote:
            stream:
                wrapper: "%s3.url%"
    data_loader: remote
    cache: s3
```

### Parameters

```
s3.region: eu-west-1
s3.uploads_bucket: xxx
s3.cache_bucket: xxx
s3.key: "xxx"
s3.secret: "xxx"
s3.url: "https://s3-%s3.region%.amazonaws.com/%s3.uploads_bucket%/"
```

### Note on paths

This sample configuration overrules the `kunstmaan_media.media_path` parameter to any empty string.
This is so that files are stored in the buckets root, but also causes the urls on Media entity in the database to be relative to the bucket.
Any images that use an `imagine_filter` will properly work because it will load all images from the bucket using the `s3.url` parameter.
However, if you want to directly link to files or images, you will have to prepend their url with the path to S3.
