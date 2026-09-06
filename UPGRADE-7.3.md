UPGRADE FROM 7.2 to 7.3
========================

FormBundle
-----------

- Public form file uploads (the `FileUploadPagePart`) are now restricted to an allow-list of file
  types, and the stored file extension is always derived from the uploaded file's **content**
  instead of the client-supplied file name. This closes an unauthenticated arbitrary file upload
  (stored XSS / potential RCE where `public/` executes PHP).

  The default allow-list contains common document and image types (`pdf`, `doc(x)`, `xls(x)`,
  `ppt(x)`, `odt`/`ods`/`odp`, `txt`, `csv`, `rtf`, `jpg`/`jpeg`, `png`, `gif`, `webp`, `avif`, `jxl`, `zip`).
  Dangerous types such as `php`, `html` and `svg` are intentionally excluded. If your forms rely
  on other file types, extend the list via configuration:

  ```yaml
  kunstmaan_form:
      file_upload:
          allowed_extensions:
              - pdf
              - jpg
              # ...
  ```

  As additional hardening it is still strongly recommended to serve the `public/uploads/formsubmissions/` directory with
  script execution disabled and with a `Content-Disposition: attachment` + `X-Content-Type-Options: nosniff` response.

MediaBundle
-----------

- The `blacklisted_extensions` option is now matched **case-insensitively**. Previously the check
  was case-sensitive while the stored extension was lowercased afterwards, so a file uploaded as
  `webshell.pHp` bypassed the blacklist and was written to disk as `webshell.php`. This closes an
  authenticated arbitrary file upload (RCE where `public/` executes PHP).

- The default `blacklisted_extensions` list is expanded from `php`/`htaccess` to the full set of
  commonly server-executable extensions (`phtml`, `php5`, `phar`, `phps`, `shtml`, `cgi`, `pl`,
  `asp`, `jsp`, ...). Files with these extensions are stored as `.txt`, as before. If you relied on
  uploading one of these types, override the option:

  ```yaml
  kunstmaan_media:
      blacklisted_extensions:
          - php
          # ...
  ```

- A new opt-in `allowed_extensions` option was added. When it is left empty (the default) only the
  blacklist applies and behaviour is unchanged. When set, it acts as a strict allow-list and any
  other extension is stored as `.txt`. The blacklist is still applied on top of it, so an
  executable extension can never be re-enabled through the allow-list.

  ```yaml
  kunstmaan_media:
      allowed_extensions: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'docx', 'zip']
  ```

  Note that `svg` and `html` are intentionally **not** blacklisted by default because a media
  library legitimately hosts them, but both are same-origin stored-XSS vectors. Add them to the
  blacklist, or use `allowed_extensions`, if your project does not need them.

- As additional hardening it is strongly recommended to serve the `public/uploads/media/` directory
  with script execution disabled and with an `X-Content-Type-Options: nosniff` response header.

GeneratorBundle
-----------

- The `kunstmaan/sensio-generator-bundle` dependency is removed, if you still need it in your project, you can install it manually.
