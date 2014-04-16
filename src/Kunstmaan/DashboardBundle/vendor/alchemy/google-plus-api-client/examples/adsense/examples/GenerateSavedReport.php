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
 * This example runs a saved report, given its ID (and the account ID).
 *
 * To get the list of saved reports, see GetAllSavedReports.php.
 *
 * Tags: accounts.reports.saved.generate
 *
 * @author Sérgio Gomes <sgomes@google.com>
 */
class GenerateReport extends BaseExample {
  public function render() {
    $accountId = ACCOUNT_ID;
    $savedReportId = SAVED_REPORT_ID;
    // Retrieve report.
    $report = $this->adSenseService->account_reports_saved
        ->generate($accountId, $savedReportId);

    if (isset($report['rows'])) {
      printReportTableHeader($report['headers']);
      printReportTableRows($report['rows']);
      printReportTableFooter();
    } else {
      printNoResultForTable(count($report['headers']));
    }
  }
}

