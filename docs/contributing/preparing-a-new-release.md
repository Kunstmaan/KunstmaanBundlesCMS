# Preparing a new release

## Before tagging

### CHANGELOG.md

Install [github-flow-changelog](https://github.com/Kunstmaan/github-flow-changelog)

```
git clone https://github.com/Kunstmaan/github-flow-changelog.git
cd github-flow-changelog
composer install
```

[Generate a GitHub API token here](https://github.com/settings/applications)

Update the changelog by running:

```
./gfc changelog <token here> Kunstmaan KunstmaanBundlesCMS > ~/Development/KunstmaanBundlesCMS/CHANGELOG.md

```

Check if all pull requests are correctly named and attached to a milestone, fix, and rerun the command. Repeat!

### UPGRADE.md

Make sure there is ample upgrade documentation available before tagging a new major release.

## After tagging

### Backward compatible branches

If you tag a new minor version (3.1, 3.2) open a new branch named the same from the previous tagged version in that branch so we can backport fixes.

### composer.json

Increase the dev-master branch alias to the next release. Do the same in all bundles.

```
    "extra": {
	"branch-alias": {
	    "dev-master": "3.2-dev"
	}
    }
```
