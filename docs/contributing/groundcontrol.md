# Ground Control

Ground Control is a front end development skeleton provided by the Kunstmaan Bundles.
It enables you to use modern build processes like webpack and gulp.

In this document we'll describe how you can do updates to the ground control skeleton.

## Where can I find it?

The skeleton can be found inside the [src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle/skeleton/layout/groundcontrol](../src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle/skeleton/layout/groundcontrol) directory.

Some more info on the ground control file structure:

1. `bin`: contains scripts for the different build tasks
2. `dist`: output of the processed ground control skeleton, we come back to this in the next section
3. `.babelrc`, `package.json`, ...: various files needed by the frontend build process

## How to develop on it?

The source files contain [Twig](https://twig.symfony.com/) syntax.
This is needed because when generating your website skeleton some variables (eg namespace) need to be processed.

To make development easier there is a task which can create a processed skeleton inside the `dist` directory.
To do this you need to run `npm run buildGroundControlSkeleton`.

The `buildGroundControlSkeleton` task will:

1. Process the ground control skeleton
2. Copy the outpu to the `dist` directory
3. Build the skeleton to verify there are no errors

You can run this task as many times as you want. Every time you run it, it will cleanup the `dist` directory to start with a clean output.
