# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.


  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "sportsynergy"

  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :forwarded_port, guest: 3306, host: 3307

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  

  
  config.vm.synced_folder ".", "/opt/clubpro"


 
end
