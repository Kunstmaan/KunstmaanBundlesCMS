Shell
======
Shell is a helpfull service which makes it possible to execute shell commands in the background. Next to executing the commands it is also possible to check if the process is still running and kill the process.

To start using the shell wrapper you must first register the service:

```yaml
kunstmaan_utilities.shell:
    class: 'Kunstmaan\UtilitiesBundle\Helper\Shell\Shell'
```

After registering the service you can execute a shell command as follows:

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