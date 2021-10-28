# Cropping media objects in the bundles

## Stability warning

WARNING: This feature is experimental and is a subject to change, be advised when using this feature and classes/templates.

## cropping

Oftentimes before this implementation we needed to make pageparts with multiple media object uploads to be able to scale up or down for desktops to mobile phones.
Now we have made a feature that allows you to use one media object and crop it in several different ways for each viewport.

## Implementation

To use an editable media object you want to use the wrapper object instead of the actual media object. If you do that all the rest will be done automatically.

pagepart.php
```php
<?php

namespace App\Entity\PageParts;

use App\Form\PageParts\EditableImagePagePartAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_croppable_image_page_parts")
 */
class EditableImagePartPart extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\EditableMediaWrapper", cascade={"persist"})
     * @ORM\JoinColumn(name="media_wrapper_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    private $mediaWrapper;

    /**
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow;

    public function getOpenInNewWindow(): ?bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow($openInNewWindow): EditableImagePartPart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setLink($link): EditableImagePartPart
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getMediaWrapper()
    {
        return $this->mediaWrapper;
    }

    public function setMediaWrapper(EditableMediaWrapper $mediaWrapper)
    {
        $this->mediaWrapper = $mediaWrapper;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'PageParts/EditableImagePagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return EditableImagePagePartAdminType::class;
    }
}
```

pagepartadmintype.php
```php
<?php

namespace App\Form\PageParts;

use App\Entity\PageParts\EditableImagePartPart;
use Kunstmaan\MediaBundle\Form\EditableMediaWrapperAdminType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditableImagePagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('mediaWrapper', EditableMediaWrapperAdminType::class, [
            'required' => true,
        ]);
        $builder->add('link', URLChooserType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.link',
        ]);
        $builder->add('openInNewWindow', CheckboxType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'editableimagepageparttype';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditableImagePartPart::class,
        ]);
    }
}
```

In your twig file you will have to use the new twig function we provided to generate the cropped image. (this image will be cached for future requests with the twig filter)
```twig
    {% set imgUrl = cropped_imagine_filter(resource.mediaWrapper, 'desktop') %}
```
And in your twig file you can use the following call to get the focus_point class for your image with the following method
```twig
    {% set imgUrl = get_focus_point_class(resource.mediaWrapper, 'desktop') %}
```

It is also possible to configure your own viewports using the following config.
```yaml
kunstmaan_media:
    cropping_views:
        custom_views:
            example1:
                use_focus_point: false
                use_cropping: true
                views:
                    - { name: desktop, width: 50, height: 50, lock_ratio: true}
            example2:
                use_focus_point: true
                use_cropping: false
                views:
                    - { name: desktop, width: 1, height: 1}
                    - { name: phone, width: 1, height: 1}
```
```
width: minimum width of your cropbox
height: minimum height of your cropbox
name: name that is shown in cropping modal and needs to be used in your twig template
lock_ratio: if the cropping box needs to respect the aspect ratio of your width and height at all times. Is Ignored when byFocusPoint is true
by_focus_point: if by_focus_point is set to true the frontend won't display a cropping box but will allow the user to select a focus point and use the width/height configured to calculate what area is shown.
```

You can then use it by telling your form admintype which group of viewports to use.

```php
<?php

class EditableImagePagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('mediaWrapper', EditableMediaWrapperAdminType::class, [
            'required' => true,
            'cropping_views_group' => 'example1',
        ]);
    }
}

```
