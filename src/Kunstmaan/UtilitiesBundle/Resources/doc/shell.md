Shell
======
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