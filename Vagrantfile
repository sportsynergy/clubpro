# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "sportsynergy"
  config.vm.box_url = "https://s3.amazonaws.com/sportsynergy.vms/sportsynergy.box"
  config.vm.synced_folder ".", "/opt/clubpro"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 3307
  
end