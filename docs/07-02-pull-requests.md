# Submitting pull requests

Pull request are the best way to provide a bug fix or to propose enhancements to the KunstmaanBundlesCMS. This guide will show you how to get started.

## Step 1: Setup your environment

Before working on the KunstmaanBundlesCMS, setup a Symfony 2 friendly environment like described in the [system requirements documentation](./03.01. System requirements.md) or [work with the PuPHPet Vagrant box](./03.02. Development environment.md).

Make sure your git setup is complete. If you are new to git, we highly recommended you read the excellent and free [ProGit book](http://git-scm.com/book).

## Step 2: Get the source

Start by forking the [repository](https://github.com/Kunstmaan/KunstmaanBundlesCMS) and add the following to the `composer.json` file of your project:


```
    "repositories": [
	{
	    "type": "vcs",
	    "url": "https://github.com/USERNAME/KunstmaanBundlesCMS"
	}
    ],
```

Also change the version constraint of kunstmaan/bundles-cms to `dev-master`. Delete the `vendor/kunstmaan` folder and run `composer update --prefer-source`. You now have the master version of your fork in your project.

You just need to add the upstream repository as a remote.

```
git remote add upstream https://github.com/Kunstmaan/KunstmaanBundlesCMS.git
```

## Step 3: Working on the pull request

> Before you start, you must know that all the patches you are going to submit
must be released under the *MIT license*.

Each time you want to work on a patch for a bug or on an enhancement, create a
topic branch. We prefer to work following the [Github Flow method](https://guides.github.com/introduction/flow/). Starting from the `master` branch create a new one with a descriptive name.

```bash
git checkout -b BRANCH_NAME master
```

> Use a descriptive name for your branch (`issue_XXX` where `XXX` is the GitHub issue number is a good convention for bug fixes, for features you could use e.g. `refactor-authentication`, `user-content-cache-key`, `make-retina-avatars`).

The above checkout commands automatically switch the code to the newly created
branch (check the branch you are working on with ``git branch``).

Now work on the code as much as you want and commit as much as you want; but keep
in mind the following:

* Follow the [coding standards](./06-03-coding-standards.md)
* Do atomic and logically separate commits (use the power of ``git rebase`` to
  have a clean and logical history);
* Squash irrelevant commits that are just about fixing coding standards or
  fixing typos in your own code;
* Never fix coding standards in some existing code as it makes the code review
  more difficult (submit CS fixes as a separate patch);
* Write good commit messages (see the tip below).

> A good commit message is composed of a summary (the first line), optionally followed by a blank line and a more detailed description. The summary should start with the Component you are working on in square brackets (``[AdminBundle]``, ``[SeoBundle]``, ...). Use a verb (``fixed ...``, ``added ...``, ...) to start the summary and don't add a period at the end.

## Step 4: Send a pull request

The title of your pull request should always start with the component you modified ([AdminBundle], [SeoBundle], ...). Use a verb (fixed ..., added ..., ...) to start the title and don't add a period at the end. Try to keep it brief but comprehensive.

When your pull request is not about a bug fix (when you add a new feature or change
an existing one for instance), it must also include the following:

* An explanation of the changes in the relevant ``CHANGELOG`` file(s) (the
  ``[BC BREAK]`` or the ``[DEPRECATION]`` prefix must be used when relevant);

* An explanation on how to upgrade an existing application in the relevant
  ``UPGRADE`` file(s) if the changes break backward compatibility or if you
  deprecate something that will ultimately break backward compatibility.


Whenever you feel that your patch is ready for submission, follow the
following steps.

### Rebase your pull request

Before submitting your pull request, update your branch (needed if it takes you a
while to finish your changes):

```bash
git checkout master
git fetch upstream
git merge upstream/master
git checkout BRANCH_NAME
git rebase master
```

When doing the ``rebase`` command, you might have to fix merge conflicts.
``git status`` will show you the *unmerged* files. Resolve all the conflicts,
then continue the rebase:

```bash
git add ... # add resolved files
git rebase --continue
```

Push your branch remotely:

```bash
git push --force origin BRANCH_NAME
```

### Make a Pull Request

You can now make a pull request on the `Kunstmaan/KunstmaanBundlesCMS` GitHub repository.

The pull request description must include the following checklist at the top
to ensure that contributions may be reviewed without needless feedback
loops and that your contributions can be included into the core as quickly as
possible:

```
| Q             | A
| ------------- | ---
| Bug fix?      | yes|no
| New feature?  | yes|no
| BC breaks?    | yes|no
| Deprecations? | yes|no
| Fixed tickets | comma separated list of tickets fixed by the PR
```

The whole table must be included (do **not** remove lines that you think are
not relevant).

Some answers to the questions trigger some more requirements:

* If you answer yes to "Bug fix?", check if the bug is already listed in the issues and reference it/them in "Fixed tickets";

* If you answer yes to "New feature?", you must include documentation in your pull request. For small features that do not need a whole chapter, add a small snippet in the Cookbook section;

* If you answer yes to "BC breaks?", the patch must contain updates to the relevant ``CHANGELOG`` and ``UPGRADE`` files;

* If you answer yes to "Deprecations?", the patch must contain updates to the relevant ``CHANGELOG`` and ``UPGRADE`` files;

If some of the previous requirements are not met, create a todo-list and add
relevant items:

```
    - [ ] Fix the specs as they have not been updated yet
    - [ ] Submit changes to the documentation
    - [ ] Document the BC breaks
```

If the code is not finished yet because you don't have time to finish it or
because you want early feedback on your work, add an item to todo-list:

```
    - [ ] Finish the feature
    - [ ] Gather feedback for my changes
```

As long as you have items in the todo-list, please prefix the pull request
title with "[WIP]".

In the pull request description, give as much details as possible about your
changes (don't hesitate to give code examples to illustrate your points). If
your pull request is about adding a new feature or modifying an existing one,
explain the rationale for the changes. The pull request description helps the
code review.

### Rework your Patch

Based on the feedback on the pull request, you might need to rework your
patch. Before re-submitting the patch, rebase with ``upstream/master``, don't merge; and force the push to the origin:

```
git rebase -f upstream/master
git push --force origin BRANCH_NAME
```

> When doing a ``push --force``, always specify the branch name explicitly to avoid messing other branches in the repo (``--force`` tells Git that you really want to mess with things so do it carefully).

Often, we will ask you to "squash" your commits. This means you will convert many commits to one commit. To do this, use the rebase command:

```
git rebase -i upstream/master
git push --force origin BRANCH_NAME
```

After you type this command, an editor will popup showing a list of commits:

```
pick 1a31be6 first commit
pick 7fc64b4 second commit
pick 7d33018 third commit
```

To squash all commits into the first one, remove the word ``pick`` before the
second and the last commits, and replace it by the word ``squash`` or just
``s``. When you save, Git will start rebasing, and if successful, will ask
you to edit the commit message, which by default is a listing of the commit
messages of all the commits. When you are finished, execute the push command.
