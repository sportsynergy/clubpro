# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "ubuntu/xenial64"

  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 3307

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder ".", "/opt/clubpro"

  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  #config.push.define "atlas" do |push|
  #   push.app = "sportsynergy/clubpro"
  #end

  config.vm.provider "virtualbox" do |v|
    v.name = "clubpro"
  end

  # Enable provisioning with a shell script. Additional provisioners such as Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the documentation for more information about their specific syntax and use. When your done with this copy in the deploy/clubpro.conf to /etc/apache2/sites_available.
  config.vm.provision "shell", inline: <<-SHELL
     export DEBIAN_FRONTEND=noninteractive
     apt-get update
     apt-get install -y -q mysql-server
     apt-get install -y apache2 zip libdbi-perl libdbd-mysql-perl  mysql-client-core-5.7
     apt-get install -y php libapache2-mod-php php-mcrypt php-mysql php-curl
  SHELL
end
