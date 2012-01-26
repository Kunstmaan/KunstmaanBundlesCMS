<?php

namespace Kunstmaan\ViewBundle;

use Symfony\Bundle\TwigBundle\TwigBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanViewBundle extends Bundle
{
	public function getParent(){
		return "TwigBundle";
	}
}
