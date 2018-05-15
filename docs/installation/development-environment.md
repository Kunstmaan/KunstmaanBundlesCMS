# Using PuPHPet and Vagrant

<img align="right"  width="200" alt="PuPHPet" src="https://raw.githubusercontent.com/kunstmaan/KunstmaanBundlesCMS/master/docs/images/wizard.png" />

For the sake of clarity we will perform this installation in a Vagrant box built with [PuPHPet](https://puphpet.com). PuPHPet is a web application that allows you to easily and quickly generate [Vagrant](http://vagrantup.com/) and [Puppet](https://puppetlabs.com/) controlled virtual machines. It's a perfect replacement for local development environments like XAMPP, WAMPP, MAMPP, etc

## Getting the PuPHPet Vagrant box

Get our preconfigured box by cloning [this repo](https://github.com/Kunstmaan/puphpet) to your local machine with this command:

```sh
git clone https://github.com/Kunstmaan/puphpet
```

Install Vagrant from [vagrantup.com](http://vagrantup.com/). Also install these  plugins if you haven't already:

```sh
vagrant plugin install vagrant-bindfs vagrant-cachier vagrant-hostmanager
```

After cloning go into the `puphpet` folder and run `vagrant up`. This will take a while but when completed you will have a fully functional Ubuntu 14.04 box with all dependencies installed and configured.

## Adjusting the dummy Apache and MySQL configuration

We use  *myprojectname* as a name for our project and vhost setup. We also use dummy database credentials (*mydbname, mydbuser, mydbpass*) for MySQL.

For real world use you will need to change this. You can do this via the [PuPHPet web interface](https://puphpet.com) by dragging the `puphpet/config.yaml` file into the browser and adjust the settings. Alternatively you can easily edit the YAML file `puphpet/config.yaml` by hand.
