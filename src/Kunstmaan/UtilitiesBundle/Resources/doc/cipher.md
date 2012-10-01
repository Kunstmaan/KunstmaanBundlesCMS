Cipher
======
Cipher is a helpful service to encrypt and decrypt string values. At the moment there are two cipher implementations available which you can use:

* `Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher`: which will encrypt and decrypt using mcrypt.
* `Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher`: which is an extension of the default one, which encrypts the string to a url safe string value by converting it to a hexadecimal encoded binary string.

To start using cypher you must first register the service:

```yaml
kunstmaan_utilities.cipher:
    class: '<CIPHER_IMPLEMENTATION_CLASS>'
    arguments: ['<YOUR_SECRET_HERE>']
```

After registering the service you can encrypt and decrypt a string value as follows:

```php
$cipher = $container->get('kunstmaan_utilities.cipher');
$encryptedValue = $cipher->encrypt('YOUR STRING HERE');
$decryptedValue = $cipher->decrypt($encryptedValue);
```

You can also specify your own cipher class, this class should extend *Kunstmaan\UtilitiesBundle\Helper\Cipher\CipherInterface*