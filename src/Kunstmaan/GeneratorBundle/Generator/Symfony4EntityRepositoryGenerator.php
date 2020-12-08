<?php

namespace Kunstmaan\GeneratorBundle\Generator;

/**
 * @internal
 */
final class Symfony4EntityRepositoryGenerator
{
    private static $_template =
        '<?php

namespace <namespace>;

use <entityClassName>;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class <repository> extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, <entity>::class);
    }
}
';

    /**
     * @param string $entityClassName
     * @param string $repositoryClassName
     *
     * @return string
     */
    public function generateEntityRepositoryClass(string $entityClass, string $repositoryClass)
    {
        $variables = [
            '<namespace>' => $this->generateEntityRepositoryNamespace($repositoryClass),
            '<entityClassName>' => $entityClass,
            '<entity>' => $this->generateEntityName($entityClass),
            '<repository>' => $this->generateClassName($repositoryClass),
        ];

        return str_replace(array_keys($variables), array_values($variables), self::$_template);
    }

    /**
     * @param string $fullClassName
     * @param string $outputDirectory
     */
    public function writeEntityRepositoryClass($entityClass, $repositoryClass, $outputDirectory)
    {
        $classNameParts = explode('\\', $repositoryClass);
        $code = $this->generateEntityRepositoryClass($entityClass, $repositoryClass);

        $path = $outputDirectory . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . end($classNameParts) . '.php';

        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!file_exists($path)) {
            file_put_contents($path, $code);
            chmod($path, 0664);
        }
    }

    /**
     * @return bool|string
     */
    private function generateEntityRepositoryNamespace(string $repositoryClassName)
    {
        return substr($repositoryClassName, 0, strrpos($repositoryClassName, '\\'));
    }

    /**
     * @return bool|string
     */
    private function generateEntityName(string $entityClassName)
    {
        return substr(strrchr($entityClassName, '\\'), 1);
    }

    /**
     * Generates the class name
     *
     * @param string $fullClassName
     *
     * @return string
     */
    private function generateClassName($fullClassName)
    {
        $namespace = $this->getClassNamespace($fullClassName);

        $className = $fullClassName;

        if ($namespace) {
            $className = substr($fullClassName, strrpos($fullClassName, '\\') + 1, strlen($fullClassName));
        }

        return $className;
    }

    /**
     * Generates the namespace, if class do not have namespace, return empty string instead.
     *
     * @param string $fullClassName
     *
     * @return string $namespace
     */
    private function getClassNamespace($fullClassName)
    {
        $namespace = substr($fullClassName, 0, strrpos($fullClassName, '\\'));

        return $namespace;
    }
}
