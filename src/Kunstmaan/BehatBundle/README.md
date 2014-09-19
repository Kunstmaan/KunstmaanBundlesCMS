# KunstmaanBehatBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanBehatBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanBehatBundle)

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanBehatBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanBehatBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/behat-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/behat-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/behat-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/behat-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanBehatBundle)](https://github.com/igrigorik/ga-beacon)

An extension on the Behat Mink feature contexts. You can extend the FeatureContext from this bundle or simply use the SubContexts in your own.

## FeatureContext

The FeatureContext overrides the standard MinkContext methods to add some additional functionality.

The assertPageContainsText($text) definition is currently extended so it waits for AJAX requests to finish before continuing.

## SubContexts

### Failed step screenshots

The FailedScreenshotSubContext offers a takeScreenshotAfterFailedStep($event) definition which takes a screenshot and saves it to the "build/behat" folder when a step fails.

### Radio Button

The RadioButtonSubContect offers support for radio button testing in your features.
