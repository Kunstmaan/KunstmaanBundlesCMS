# UPGRADE FROM 3.6 to 4.0

## LiveReloadBundle & LanguageChooserBundle

These bundles were completely removed.

## URL Chooser

The URLChooserType had been rewritten.

You now have the option to add the following type of links:

- Internal
- External (external links or for example anchors)
- Email (`mailto:` will be automatically added)

We have added this rewrite especially for the MultiDomainBundle but it's completely integrated in the KunstmaanBundlesCMS.

When you choose for an internal link type, you will need to choose a link in the menu tree. On a multidomain website, you can switch you're domain on the right site of the URL chooser.

When clicking a node in the left node tree, a token is generated and saved for the URL. 

## For example: in a single domain website

Selecting the node in the tree with node translation id 50, will generate a token like `[NT50]`.

## For example: in a multi domain website

Selecting the node in the tree with node translation id 50, on domain `my_domain_en` will generate a token like `[my_domain_en:NT50]`.

## MAJOR CHANGE

For the new URL chooser to work, we have added a new twig filter `replace_url`. This filter needs to be used to generate the correct url automatically.

When creating a new pagePart with an url field, the twig filter is automatically being added. 

On all the exiting pageParts with an URLChooser field, you need to change the twig files of the pagePart and pipe all URL's to the replace_url filter.

When adding a rich text field or a wysiwyg field, you will need to pipe the output of this field also to the replace_url filter. Hereby, all URL's chosen in the wysiwyg will also be replaced correctly.
 
The DomainConfigurationInterface has also been changed. If you have implemented this interface, please be sure to check the required methods.

## EventListener

The AdminListEvent has been changed, properties Form, Request and function Response have been added.

## Node version locking

See https://github.com/Kunstmaan/KunstmaanBundlesCMS/tree/master/src/Kunstmaan/NodeBundle/Resources/doc/Locking.md

## Forms must be referenced via FQCN [brakes BC]

All forms were upgraded to be used by **fully qualified class name** instead of creating new form instance.

See more in [Symfony upgrade v2->v3 doc](https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#form)

Before:
```
public function getDefaultAdminType()
{
    return new MyPageAdminType();
}
```

After:
```
public function getDefaultAdminType()
{
    return MyPageAdminType::class;
}
```

A tip: use regex to search part of new form type instances creations (`new .+Type\(`).
"

## VotingBundle

VotingBundle is refactored. If using custom voters implement the new abstract classes
* VoteListener is renamed to AbstractVoteListener
* VotingHelper is renamed to AbstractVotingHelper
