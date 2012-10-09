Cipher
======
Cipher is a helpful service to encrypt and decrypt string values. At the moment there are two cipher implementations available which you can use:

* `Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher`: which will encrypt and decrypt using mcrypt.
* `Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher`: which is an extension of the default one, which encrypts the string to a url safe string value by converting it to a hexadecimal encoded binary string.

To start using cypher you must first configure the cipher secret in your `parameters.yaml`:

```yaml
kunstmaan_utilities.cipher.secret: '<YOUR_SECRET_HERE>'
```

The default implementation that is used is the `Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher`, to use another cipher implementation you can define the class you want to use in your `parameters.yaml` as follows:

```yaml
kunstmaan_utilities.cipher.class: '<YOUR_CLASS_HERE>'
```

The custom cipher class should extend *Kunstmaan\UtilitiesBundle\Helper\Cipher\CipherInterface*

When the service is configured properly you can start using the cipher as follows:

```php
$cipher = $container->get('kunstmaan_utilities.cipher');
$encryptedValue = $cipher->encrypt('YOUR STRING HERE');
$decryptedValue = $cipher->decrypt($encryptedValue);
```