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
 * Gets all metrics available for the logged in user's default account.
 *
 * Tags: metadata.metrics.list
 *
 * @author Sérgio Gomes <sgomes@google.com>
 */
class GetAllMetrics extends BaseExample {
  public function render() {
    $listClass = 'list';
    printListHeader($listClass);
    // Retrieve metric list, and display it.
    $result = $this->adSenseService->metadata_metrics->listMetadataMetrics();
    if (isset($result['items'])) {
      $metrics = $result['items'];
      foreach ($metrics as $metric) {
        $format = 'Metric id "%s" for product(s): [%s] was found.';
        $content = sprintf(
            $format,
            $metric['id'],
            implode(', ', $metric['supportedProducts']));
        printListElement($content);
      }
    } else {
      printNoResultForList();
    }
    printListFooter();
  }
}

