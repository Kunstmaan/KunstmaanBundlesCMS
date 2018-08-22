# UtilitiesBundle

## Cipher

Cipher is a helpful service to encrypt and decrypt string values. At the moment there are two cipher implementations available which you can use:

* `Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher`: which will encrypt and decrypt using mcrypt.
* `Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher`: which is an extension of the default one, which encrypts the string to a url safe string value by converting it to a hexadecimal encoded binary string.

To start using cypher you can configure the cipher secret in your `parameters.yaml` or in your `config.yml`:

```yaml
kunstmaan_utilities.cipher.secret: '<YOUR_SECRET_HERE>'
```
If you don't configure it, then it will use the value `secret` from `parameters.yml`. 


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

## Shell

Shell is a helpfull service which makes it possible to execute shell commands in the background. Next to executing the commands it is also possible to check if the process is still running and kill the process.

You can start using the service for executing a shell command as follows:

```php
$shell = $container->get('kunstmaan_utilities.shell');
$pid = $shell->runInBackground('<YOUR_COMMAND_HERE>');
```

The `runInBackground` function will always return the process id. You can also define the priority for the process:

```php
$shell = $container->get('kunstmaan_utilities.shell');
$pid = $shell->runInBackground('<YOUR_COMMAND_HERE>', 10);
```

When the process is running you can check if it's still running or kill it as follows:

```php
$shell = $container->get('kunstmaan_utilities.shell');
$running = $shell->isRunning('<YOUR_PROCESS_ID');
$success = $shell->kill('<YOUR_PROCESS_ID');
```

Be aware when you use the kill method, make sure you don't use for example a http parameter as $pid because then you have a security hole !!!

When you want to define your own Shell implementation then it should implement the *Kunstmaan\UtilitiesBundle\Helper\ShellInterface*.
