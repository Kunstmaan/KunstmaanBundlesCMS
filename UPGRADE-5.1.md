# UPGRADE FROM 5.0 to 5.1

## Deprecate use of old service id's

PR https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1814 introduces the use of FQCN as service id's instead of the old id's.
Therefore we added child definitions for the old service id's to still work.
When you are still using the old service id's in your project, you will get deprecation messages.
In KunstmaanBundlesCMS 6.0 the old service id's will be removed, so be sure to update your custom bundles.
