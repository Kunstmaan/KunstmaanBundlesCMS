# Administer the robots.txt

## robots.txt files?

A web crawler, also known as robot, searches the web to index it's content. A well known example of such a crawler is Google.
You can guide these crawlers on your website by providing a robots.txt file.
Although respectable web crawlers follow the directives in a robots.txt file, each crawler might interpret the directives differently.

By default, all robots will be granted access to your site.
But luckily the Kunstmaan bundles made it easy to manage your robots.txt file and the behaviour of robots.

## Administer using the admin interface

If you are a site administrator.
You can edit your robots.txt file in admin interface by going to settings and by choosing for "Robots" in the sub menu.

![Image of Robots administration](https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/robots-admin.png)

## Administer using a robots.txt file

If you leave the Robots field empty, the Kunstmaan bundles will look for a robot.txt file in the document root folder and use the content of that file.
You could also use this as a fall back method, so we recommend having a robots.txt file present in your document root (typically the /web folder).

## Robots.txt syntax

The syntax for using the keywords is as follows:


* **User-agent:** the name of the robot the following rule applies to, you can use * as a wildcard
* **Disallow:** the path you want to block, you can use multiples of this line and use * as a wildcard
* **Allow:** the path of a subdirectory, within a blocked parent directory, that you want to unblock


Some examples could be:

```
User-agent: *
Disallow: /folder/
Allow: /folder/subfolder/
```

or

```
User-agent: *
Disallow: /file.html
```

## Extra information

* [Google developer docs](https://support.google.com/webmasters/answer/6062608?hl=en)