Vagrant::Config.run do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "fbsecurepasswords2"

  config.vm.customize do |vm|
    vm.memory_size = 1024 
    vm.name = "FlagbitSecurePasswords2"
  end

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  # config.vm.box_url = "http://domain.com/path/to/above.box"

  # Boot with a GUI so you can see the screen. (Default is headless)
  # config.vm.boot_mode = :gui

  # Assign this VM to a host only network IP, allowing you to access it
  # via the IP.
  config.vm.network "33.33.33.33"
  config.vm.share_folder("v-root", "/var/www", "www", :nfs => true)
  config.vm.host_name = "fbsecurepasswords2"

  # Forward a port from the guest to the host, which allows for outside
  # computers to access the VM, whereas host only networking does not.
  #config.vm.forward_port "http", 80, 8080

  # Share an additional folder to the guest VM. The first argument is
  # an identifier, the second is the path on the guest to mount the
  # folder, and the third is the path on the host to the actual folder.
  #config.vm.share_folder "v-root", "/vagrant", "www"

  # Enable provisioning with chef solo, specifying a cookbooks path (relative
  # to this Vagrantfile), and adding some recipes and/or roles.
  #
  # config.vm.provision :chef_solo do |chef|
  #   chef.cookbooks_path = "cookbooks"
  #   chef.add_recipe "mysql"
  #   chef.add_role "web"
  #
  #   # You may also specify custom JSON attributes:
  #   chef.json = { :mysql_password => "foo" }
  # end

  # Enable provisioning with chef solo
  config.vm.provision :chef_server do |chef|
    #chef.recipe_url = "http://cloud.github.com/downloads/tonigrigoriu/magento-cookbooks/cookbooks.tgz"
    #chef.cookbooks_path = "."

    chef.add_recipe("ubuntu")
    chef.add_recipe("vagrant-main")
     chef.add_recipe("nfs")
    #chef.add_recipe("magento")
    #chef.add_recipe("magento::mysql")
    #chef.add_recipe("magento::sample_data")
    #chef.add_recipe("magento::magento")
    #chef.add_recipe("phpmyadmin")

    chef.validation_client_name = "chef-validator"
    chef.validation_key_path = "~/.chef/validation.pem"
    chef.chef_server_url = "http://192.168.2.65:4000/"

    chef.json.merge!({
      :mysql => {
        :server_root_password => "root"
      },
      :ubuntu => {
        :archive_url => "http://de.archive.ubuntu.com/ubuntu"
      },
      :magento => {
        :version => "1.4.1.1"
      },
      :nfs => {
        :exports => {
           "/var/www" => {
             :nfs_options => "33.33.33.1(rw,async,all_squash,anonuid=1000,anongid=1000,no_subtree_check,insecure)"
           }
        } 
      }      
    })

    # Debug
    #chef.log_level = :debug
  end


  # Enable provisioning with chef server, specifying the chef server URL,
  # and the path to the validation key (relative to this Vagrantfile).
  #
  # The Opscode Platform uses HTTPS. Substitute your organization for
  # ORGNAME in the URL and validation key.
  #
  # If you have your own Chef Server, use the appropriate URL, which may be
  # HTTP instead of HTTPS depending on your configuration. Also change the
  # validation key to validation.pem.
  #
  # config.vm.provision :chef_server do |chef|
  #   chef.chef_server_url = "https://api.opscode.com/organizations/ORGNAME"
  #   chef.validation_key_path = "ORGNAME-validator.pem"
  # end
  #
  # If you're using the Opscode platform, your validator client is
  # ORGNAME-validator, replacing ORGNAME with your organization name.
  #
  # IF you have your own Chef Server, the default validation client name is
  # chef-validator, unless you changed the configuration.
  #
  #   chef.validation_client_name = "ORGNAME-validator"
end
