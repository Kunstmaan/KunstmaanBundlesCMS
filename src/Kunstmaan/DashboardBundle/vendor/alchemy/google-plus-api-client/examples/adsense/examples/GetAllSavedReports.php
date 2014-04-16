<?php
/*
 * Copyright 2012 Google Inc.
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

// Require the base class.
require_once __DIR__ . "/../BaseExample.php";

/**
 * This example gets all saved reports for an account.
 *
 * Tags: accounts.reports.saved.list
 *
 * @author Sérgio Gomes <sgomes@google.com>
 */
class GetAllSavedReports extends BaseExample {
  public function render() {
    $accountId = ACCOUNT_ID;
    $optParams['maxResults'] = AD_MAX_PAGE_SIZE;
    $listClass = 'saved reports';
    printListHeader($listClass);
    $pageToken = null;
    do {
      $optParams['pageToken'] = $pageToken;
      // Retrieve saved report list, and display it.
      $result = $this->adSenseService->accounts_reports_saved
          ->listAccountsReportsSaved($accountId, $optParams);
      $savedReports = $result['items'];
      if (empty($savedReports)) {
        foreach ($savedReports as $savedReport) {
          $content = array();
          $mainFormat = 'Saved report with name "%s" and ID "%s" was found.';
          $content[] = sprintf(
              $mainFormat, $savedReport['name'], $savedReport['id']);
          printListElementForClients($content);
        }
        $pageToken = isset($result['nextPageToken']) ? $result['nextPageToken']
            : null;
      } else {
        printNoResultForList();
      }
    } while ($pageToken);
    printListFooter();
  }
}

