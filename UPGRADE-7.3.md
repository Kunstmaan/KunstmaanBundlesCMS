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

GeneratorBundle
-----------

- The `kunstmaan/sensio-generator-bundle` dependency is removed, if you still need it in your project, you can install it manually.
