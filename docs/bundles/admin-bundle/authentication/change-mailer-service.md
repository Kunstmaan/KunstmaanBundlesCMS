# Change authentication mailer service

By default, the CMS config uses swiftmailer to send email related to the authentication system. 
Currently, the authentication system has built-in support for sending mails with Swiftmailer and Symfony mailer. To change 
the service used to send mails, update the `kunstmaan_admin.authentication.mailer.service` config to either 
`Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService` or `Kunstmaan\AdminBundle\Service\AuthenticationMailer\SwiftmailerService`.

```yaml
kunstmaan_admin:
    authentication:
        mailer:
            service: Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService
```

## Custom mailer implementation

You can also use a custom mailer implementation by creating a class that implements the 
`\Kunstmaan\AdminBundle\Service\AuthenticationMailer\AuthenticationMailerInterface` interface and registering it as a service. 
Afterwards update the mailer service config option to the service name.`
