# Release process

We are implementing a flexible release process approach with some limitations. We will not follow the same fixed release points as the Symfony project, nor the version numbers, but we try to make the Kunstmaan Bundles CMS compatible with the latest Symfony releases as soon as possible. This means that when a new minor Symfony version gets released, that this version can be used in the next minor release of the Kunstmaan Bundles CMS. A new major Symfony version can be used in the next major release of the CMS.

## Major releases

A major release will be done every 3 to 9 months. This will depend on:
* the number of backwards incompatible submitted pull requests / new features
* the release of a new major Symfony version

For our major releases we want to implement a feature freeze period. We will do this by releasing a release candidate (RC) first. After a month
we will do the final release. During this freeze, only bugfixed will be merged in the release branch.

## Minor releases

A minor release will be done when necessary. This will depend on:
* the number of backwards compatible new features
* the release of a new minor Symfony version

## Patch releases

A patch release will be done when needed. Some reasons:
* new bugfixes
* security issue was fixed (in CMS or Symfony)
* a third party library compatibility issue was fixed
* ...
