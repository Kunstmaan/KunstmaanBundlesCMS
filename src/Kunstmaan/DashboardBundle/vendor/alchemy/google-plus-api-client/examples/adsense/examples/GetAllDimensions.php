<?php
/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// Require the base class
require_once __DIR__ . "/../BaseExample.php";

/**
 * Gets all dimensions available for the logged in user's default account.
 *
 * Tags: metadata.dimensions.list
 *
 * @author Sérgio Gomes <sgomes@google.com>
 */
class GetAllDimensions extends BaseExample {
  public function render() {
    $listClass = 'list';
    printListHeader($listClass);
    // Retrieve dimension list, and display it.
    $result = $this->adSenseService->metadata_dimensions
        ->listMetadataDimensions();
    if (isset($result['items'])) {
      $dimensions = $result['items'];
      foreach ($dimensions as $dimension) {
        $format = 'Dimension id "%s" for product(s): [%s] was found.';
        $content = sprintf(
            $format,
            $dimension['id'],
            implode(', ', $dimension['supportedProducts']));
        printListElement($content);
      }
    } else {
      printNoResultForList();
    }
    printListFooter();
  }
}

