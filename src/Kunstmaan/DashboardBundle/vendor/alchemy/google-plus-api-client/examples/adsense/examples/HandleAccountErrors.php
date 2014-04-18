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

// Require the base class
require_once __DIR__ . "/../BaseExample.php";

/**
 * This example shows how to handle different AdSense account errors.
 *
 * Tags: adclients.list
 *
 * @author Sérgio Gomes <sgomes@google.com>
 */
class HandleAccountErrors extends BaseExample {
  public function render() {
    $optParams['maxResults'] = AD_MAX_PAGE_SIZE;
    try {
      $result = $this->adSenseService->adclients->listAdclients($optParams);

      print 'The call succeeded. Please use an invalid, disapproved or '
          .'approval-pending AdSense account to test error handling.';

    // Handle a few known API errors. See full list at
    // https://developers.google.com/adsense/management/v1.1/reference/#errors
    } catch (Google_ServiceException $e) {
      foreach ($e->getErrors() as $error) {
        switch ($error['reason']) {
          case 'noAdSenseAccount':
            print 'Error handled! No AdSense account for this user.';
            break;
          case 'disapprovedAccount':
            print 'Error handled! This account is disapproved.';
            break;
          case 'accountPendingReview':
            print 'Error handled! This account is pending review.';
            break;
          default:
            // Unrecognized reason, so let's use the error message returned by
            // the API.
            print 'Unrecognized error, showing system message: ';
            print $error['message'];
            break;
        }
      }
    }
  }
}

