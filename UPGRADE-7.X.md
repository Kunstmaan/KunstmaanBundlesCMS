UPGRADE FROM 7.3 to 7.X
========================

RedirectBundle
--------------

- Doctrine migration required.
- Add a line to the migration to update all redirect entities:
  ```php
  $this->addSql("UPDATE kuma_redirects SET origin_pattern = REPLACE(origin, '*', '%');");
  ```
