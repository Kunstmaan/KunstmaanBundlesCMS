Cipher
======
Cipher is a helpful service to encrypt and decrypt string values. To start using cypher you must first register the service:

```yaml
kunstmaan_utilities.cipher:
    class: 'Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher'
    arguments: ['<YOUR_SECRET_HERE>']
```

After registering the service you can encrypt and decrypt a string value as follows:

```php
$cipher = $container->get('kunstmaan_utilities.cipher');
$encryptedValue = $cipher->encrypt('YOUR STRING HERE');
$decryptedValue = $cipher->decrypt($encryptedValue);
```

The default encrypt and decrypt can contain rare characters … which are not safe to use in an url. There are also functions available with which you can use the encrypted value in an url.

```php
$cipher = $container->get('kunstmaan_utilities.cipher');
$encryptedValue = $cipher->urlSafeEncrypt('YOUR STRING HERE');
$decryptedValue = $cipher->urlSafeDecrypt($encryptedValue);
```

You can also specify your own cipher class, this class should extend *Kunstmaan\UtilitiesBundle\Helper\Cipher\CipherInterface*