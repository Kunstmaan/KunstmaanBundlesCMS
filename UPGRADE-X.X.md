# UPGRADE FROM 3.5 to X.X

## [MediaBundle] FileHandler and ImageHandler have changed.

The service definitions for both `FileHandler` and `ImageHandler` have changed.
If you have changed their classes using the `%kunstmaan_media.media_handler.[image|file].class%` parameter, make sure they implement the new method.

The `MEDIA_PATH` constant on `FileHandler` has been removed in favor of a property that can be set using the `setMediaPath` method.
