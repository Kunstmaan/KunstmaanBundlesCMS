# Add folder-type to the [KunstmaanMediaBundle][KunstmaanMediaBundle]

This document describes how you can add a different folder-type to the [KunstmaanMediaBundle][KunstmaanMediaBundle].

## Make new class in the entity folder:

As an example we make a new class to add a folder-type that can contain pdf-files.
To make this work we would extend the Folder-class. The minimum configuration for our pdffolder-class is the following:


```bash
<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that defines a PdfFolder object in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity
 * @ORM\Table(name="media_gallery_pdf")
 * @ORM\HasLifecycleCallbacks
 */
class PdfGallery extends Folder{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    public function getStrategy(){
        return new \Kunstmaan\MediaBundle\Helper\PdfGalleryStrategy();
    }
}

?>
```

We also add this new folder to the FolderFactory:


```bash
private static function getTypeArray(){
      return array("folder" => new Folder(), 
    				 ...,
             "pdf" => new PdfGallery()
    		   );
    }
```


## Add strategy:

As we can see in the folderclass above we need to define a new strategy for the pdfGallery. The following is the minimumconfiguration for the folderstrategy.


```bash
<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * PdfGalleryStrategy
 */
class PdfGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'PdfGallery';
    }

    public function getType()
    {
        return 'pdf';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\MediaBundle\Entity\PdfGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\PdfGallery';
    }

    function getFormType()
    {
        return new \Kunstmaan\MediaBundle\Form\MediaType();
    }

    function getFormHelper()
    {
        return new MediaHelper();
    }

    function getListConfigurator(){
        return new \Kunstmaan\MediaBundle\Helper\MediaList\FileListConfigurator();
    }
}

?>    
```

## Add first pdffolder to the datafixtures:

Since we can't add folders of this type to the system unless we have a mainfolder-for this type of folders, we add one to the datafixtures:

```bash
            $subgal = new PdfGallery();
            $subgal->setParent($gal);
            $subgal->setName('media.menu.pdfs');
            $subgal->setCanDelete(false);
            $subgal->setRel("pdfs");
            $manager->persist($subgal);
            $manager->flush();    
```

       
[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"
