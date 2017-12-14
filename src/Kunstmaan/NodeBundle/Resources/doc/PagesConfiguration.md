# Pages Configuration

## Example

```
kunstmaan_node:
    pages:
        AcmeBundle\Entity\Pages\HomePage:
            name: Home page
            indexable: false
            # font awesome icons:
            icon: fa fa-home
            # hide from sidebar tree in admin panel:
            hidden_from_tree: false
            allowed_children:
                # simple reference:
                - AcmeBundle\Entity\Pages\SearchPage
                # custom reference:
                - name: Custom name
                  class: AcmeBundle\Entity\Pages\ArticlePage

        AcmeBundle\Entity\Pages\ArticlePage:
            # the name is translated by default:
            name: pages.article
            indexable: true
            icon: fa fa-newspaper
            search_type: article

        AcmeBundle\Entity\Pages\SearchPage:
            name: Search results page
            indexable: false
            icon: fa fa-search

```
