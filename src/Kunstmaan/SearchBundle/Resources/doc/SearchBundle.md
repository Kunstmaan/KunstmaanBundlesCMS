# SearchBundle documentation

## Analyzers

The Bundles provide 2 sorts of analyzers to choose from. you can choose between
them by overriding the analysisfactory parameter in the services.

### Language Analyzer

The default analyzer is a language intelligent analyzer who only analyzes real
words and conjugations. This is a great default and for normal search forms
this will be the perfect fit.

Default configuration:
```YAML
parameters:
    kunstmaan_search.search.factory.analysis.class: Kunstmaan\SearchBundle\Search\LanguageAnalysisFactory
```

### NGram Analyzer

The second analyzer the bundles provides is an nGram based analyzer. This
analyzer is not language intelligent but splits everything in little chunks of
words to be searched on. This can be very helpful for a google-like search
implementation but is slower and more cpu intensive than word based search.

nGram configuration
```YAML
parameters:
    kunstmaan_search.search.factory.analysis.class: Kunstmaan\SearchBundle\Search\NGramAnalysisFactory
```

### Custom analyzers

You can easily provide your own analysisfactory with a custom analyzer
specific for your needs. Create a class that implements the
AnalysisFactoryInterface and then override the analysisfactory parameter with
your own class name.

custom configuration
```YAML
parameters:
    kunstmaan_search.search.factory.analysis.class: Demo\AppBundle\Search\CustomAnalysisFactory
```
