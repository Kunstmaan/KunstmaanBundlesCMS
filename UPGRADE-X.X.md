# UPGRADE FROM 3.5 to X.X

## [MediaBundle] FileHandler and ImageHandler have changed.

The service definitions for both `FileHandler` and `ImageHandler` have changed.
If you have changed their classes using the `%kunstmaan_media.media_handler.[image|file].class%` parameter, make sure they implement the new method.

The `MEDIA_PATH` constant on `FileHandler` has been removed in favor of a property that can be set using the `setMediaPath` method.


## [PagePartBundle] `PagePartConfigurationReader` and `PageTemplateConfigurationReader` removed

If you relied on those classes use new interfaces and services instead:

 * `kunstmaan_page_part.page_part_configuration_reader` implementing `PagePartConfigurationReaderInterface`
 * `kunstmaan_page_part.page_template_configuration_reader` implementing `PageTemplateConfigurationReaderInterface`

Classes using those services has changed as well and now take them as a constructor dependency instead of creating an instance in place.

The `PageTemplateConfigurationRepostiory::findOrCreateFor` has been moved to `PageTemplateConfigurationService::findOrCreateFor`. It can be found via the `kunstmaan_page_part.page_template.page_template_configuration_service` service.
