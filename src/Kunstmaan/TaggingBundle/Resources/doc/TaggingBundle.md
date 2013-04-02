KunstmaanGeneratorBundle by Kunstmaan
=================================

# Config

Extend the ORM entity manager mappings with the following in your config.yml :

 orm:
    entity_managers:
        default:
            mappings:
                taggable:
                    type: annotation
                    prefix: DoctrineExtensions\Taggable\Entity
                    dir: "%kernel.root_dir%/../vendor/fpn/doctrine-extensions-taggable/metadata"

# Implement Taggable

Have the entity you want to add tagging to implement 'Taggable' (Kunstmaan\TaggingBundle\Entity\Taggable) and implement the three new methods.

* getTaggableType should return a unique string
* getTabbableid should return a unique identifier for your tagged object
* getTags should return the tags linked to the tagged object

# Form

To add the tags field to your builder, use the following code :

 $builder->add('tags', 'kunstmaan_taggingbundle_tags');

# Routing

In order to add the tags' AdminList to the Admin menu, add the following to the routing.yml :

 KunstmaanTaggingBundle:
     resource: "@KunstmaanTaggingBundle/Resources/config/routing.yml"
     prefix:   /{_locale}/
     requirements:
         _locale: %requiredlocales%