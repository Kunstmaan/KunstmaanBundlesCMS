AdminBundle
-----------

* The `Kunstmaan\AdminBundle\Entity\Role` class doesn't exents from the deprecated `Symfony\Component\Security\Core\Role\Role` 
  class if you run your code on symfony 5. The Role class was deprecated and removed in symfony 5. If you used this class 
  to check the `Role` entity change it to the `Kunstmaan\AdminBundle\Entity\Role` class. 
  The `Role` entity won't change if you run on symfony 3.4 but it's adviced to make this change already.
