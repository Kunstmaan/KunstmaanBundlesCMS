# Add media-type to the [KunstmaanMediaBundle][KunstmaanMediaBundle]

This document describes how you can add a different media-type to the [KunstmaanMediaBundle][KunstmaanMediaBundle].

## Make new class in the entity folder:

As an example we make a new class to add a type that can contain pdf-files.
To make this work we would extend the Media-class. Since a pdf is also a file, we chose to extend the File-class. The minimum configuration for our pdf-class is the following:


```bash
<?php

namespace Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kunstmaan\MediaBundle\Entity\Pdf
 * Class that defines a pdf in the system
 *
 * @ORM\Table("media_pdf")
 * @ORM\Entity
 */
class Pdf extends File
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uuid;

    /**
     * @var string $context
     *
     */
    protected $context = "kunstmaan_media_pdf";

    public function __construct()
    {
        parent::__construct();
        $this->classtype = "Pdf";
    }
}

?>
```

We also add the new class to th discriminatormap in the Media-class annotations. 
If we want to make Folders able to upload pdf's, we add a method to upload a pdf to the MediaController and make the views for the createfunction.

```bash
    /**
     * @Route("pdfcreate/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_folder_pdfcreate")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanMediaBundle:Pdf:create.html.twig")
     */
    public function pdfcreateAction($gallery_id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Folder')->getFolder($gallery_id);
    
    	$request = $this->getRequest();
    	$helper = new MediaHelper();
    	$form = $this->createForm(new MediaType(), $helper);
    
    	if ('POST' == $request->getMethod()) {
    		$form->bind($request);
    		if ($form->isValid()){
    			if ($helper->getMedia()!=null) {
    				$file = new Pdf();
    				$file->setName($helper->getMedia()->getClientOriginalName());
    				$file->setContent($helper->getMedia());
    				$file->setGallery($gallery);
    
    				$em->getRepository('KunstmaanMediaBundle:Media')->save($file);
    
    				return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_folder_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    			}
    		}
    	}
    
    	$galleries = $em->getRepository('KunstmaanMediaBundle:Folder')
    	->getAllFoldersByType();
    
    	return array(
    			'form' => $form->createView(),
    			'gallery' => $gallery,
    			'galleries' => $galleries
    	);
    }
``` 

## Add provider and context:

As we can see in the codefragment above we need to define a new context for the pdf-class. We do this by adding a provider and a context to config.yml.

```bash
    provider:
        ...
        pdf:
            default: false
            id: kunstmaan_media.provider.pdf

    contexts:
        kunstmaan_media_pdf:
            provider: pdf
```

We also need to define the provider in provider.xml by adding:

```bash    
    <parameters>
        <parameter key="kunstmaan_media.provider.pdf.class">Kunstmaan\MediaBundle\Helper\Provider\PdfProvider</parameter>
    </parameters>

    <services>
        <service id="kunstmaan_media.provider.pdf" class="%kunstmaan_media.provider.pdf.class%" parent="kunstmaan_media.provider.abstract" />
    </services>
```

The id for this service is the same we added to the providersection in config.yml.

## Make provider-class:

In the configuration we've added to provider.xml you can see we defined a parameter which contains a class-name. This is the new provider we have to make, and in which we can check or the file is a pdf. The following codefragment is the minimum configuration for the pdfprovider. Since a pdf is also a file, we chose to extend the FileProvider.

```bash
<?php

namespace Kunstmaan\MediaBundle\Helper\Provider;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Generator\ExtensionGuesser;
use Kunstmaan\MediaBundle\Helper\Provider\AbstractVideoProvider;

class PdfProvider extends FileProvider
{
    /* @var string */
    protected $template = '';

    public function prepareMedia(Media $media)
    {
        parent::prepareMedia($media);

        if(ExtensionGuesser::guess($media->getContentType()) != ".pdf"){
        	throw new \RuntimeException('This is not a pdf');
        }
    }
}

?>
```

# Further reading:

In addnewfoldertype.md we will show you how to make a folder for only pdf's

       
[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"
