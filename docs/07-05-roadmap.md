# BundlesCMS roadmap

## v4.1.0
> Due to conflicts with `symfony-cmf/routing-bundle` this will be the 
last release of v4 and will not be supported anymore.

- [x] Allow locking of individual entities in adminlists #1384

## v5.x
- [x] Set minimum PHP requirement to 5.6 #1444
- [ ] A general API to expose pages/pageparts/media/... so it can be uses by other platforms/apps/... #1330
- [ ] More advanced ROLE system for admin interface (TRANSLATOR_ROLE, USER_MANAGEMENT_ROLE, ...) #1329
- [ ] Implement new way to choose pageparts #1247
- [ ] Abstract implementation for page adminlists #1470
- [ ] Password strength configuration #1469
- [ ] Notes for redirects #1411
- [ ] Nested regions in page template config #1332
- [ ] Bulk move images in media manager to other folder #1333
- [ ] Varnish cache clear / ban #1331
- [ ] Allow `/admin` url to be configurable in parameters.yml #1323
- [ ] New custom kunstmaan debug toolbar #1274

## v6.x
- [ ] Set minimum PHP requirement to 7.0 #1445
- [ ] Make `Kunstmaan\FormBundle\Entity\FormSubmissionField` more flexible by removing the discriminator map #1416
- [ ] Elasticsearch 5.x


## Feature requests
- [ ] Health page, list of all 404's
- [ ] Theming #655
- [ ] Create configuration reference for all bundles #1334
- [ ] Improve translation administration (export - import)
- [ ] Better seo interface (preview, warnings)
- [ ] Browser notifications
- [ ] 2FA authentication
- [ ] Webcomponents
- [ ] Gulp instead of assetic #676
    - [ ] Part 1: Add Groundcontrol-simple #1481
    - [ ] Part 2: Implement Groundcontrol-simple to backend styles/js
    - [ ] Part 3: Compiled assets
