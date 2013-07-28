# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "snashpots"

  # The url from where the 'config.vm.box' box will be fetched if it
  # doesn't already exist on the user's system.
  config.vm.box_url = "/Users/des4maisons/software/veewee/snapshots-maybe.box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network :forwarded_port, guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network :private_network, ip: "192.168.56.50"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network :public_network

  # If true, then any SSH connections made will enable agent forwarding.
  # Default value: false
  # config.ssh.forward_agent = true

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider :virtualbox do |vb|
  #   # Don't boot with headless mode
  #   vb.gui = true
  #
  #   # Use VBoxManage to customize the VM. For example to change memory:
  #   vb.customize ["modifyvm", :id, "--memory", "1024"]
  # end
  #
  # View the documentation for the provider you're using for more
  # information on available options.

  # Enable provisioning with Puppet stand alone.  Puppet manifests
  # are contained in a directory path relative to this Vagrantfile.
  # You will need to create the manifests directory and a manifest in
  # the file snapshots-maybe.pp in the manifests_path directory.
  #
  # An example Puppet manifest to provision the message of the day:
  #
  # # group { "puppet":
  # #   ensure => "present",
  # # }
  # #
  # # File { owner => 0, group => 0, mode => 0644 }
  # #
  # # file { '/etc/motd':
  # #   content => "Welcome to your Vagrant-built virtual machine!
  # #               Managed by Puppet.\n"
  # # }
  #
  # config.vm.provision :puppet do |puppet|
  #   puppet.manifests_path = "manifests"
  #   puppet.manifest_file  = "init.pp"
  # end

  # Enable provisioning with chef solo, specifying a cookbooks path, roles
  # path, and data_bags path (all relative to this Vagrantfile), and adding
  # some recipes and/or roles.
  #
  # config.vm.provision :chef_solo do |chef|
  #   chef.cookbooks_path = "../my-recipes/cookbooks"
  #   chef.roles_path = "../my-recipes/roles"
  #   chef.data_bags_path = "../my-recipes/data_bags"
  #   chef.add_recipe "mysql"
  #   chef.add_role "web"
  #
  #   # You may also specify custom JSON attributes:
  #   chef.json = { :mysql_password => "foo" }
  # end

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
  # config.vm.provision :chef_client do |chef|
  #   chef.chef_server_url = "https://api.opscode.com/organizations/ORGNAME"
  #   chef.validation_key_path = "ORGNAME-validator.pem"
  # end
  #
  # If you're using the Opscode platform, your validator client is
  # ORGNAME-validator, replacing ORGNAME with your organization name.
  #
  # If you have your own Chef Server, the default validation client name is
  # chef-validator, unless you changed the configuration.
  #
  #   chef.validation_client_name = "ORGNAME-validator"

  add_snashpots_user = <<-SCRIPT
    username=snashpots
    /usr/sbin/groupadd $username
    /usr/sbin/useradd $username -g $username -G wheel
    echo "$username" | passwd --stdin $username
  SCRIPT
  config.vm.provision :shell, :inline  => add_snashpots_user

  install_code = <<-SCRIPT
    username=snashpots
    init_script=/home/$username/init
    repo_url=https://github.com/dev-quest/snashpots.git
    echo "
      cd
      chmod go+rx .
      git clone $repo_url
      cd snashpots
      chmod -R o+w logs cache
      chmod o-wr logs/README.md cache/README.md .git .gitignore README.md

      curl -s https://getcomposer.org/installer | php -d detect_unicode=Off
      php composer.phar install
    " >> $init_script

    chown $username:$username $init_script
    su -c "cd && bash $(basename $init_script)" $username
  SCRIPT
  config.vm.provision :shell, :inline  => install_code

  configure_nginx = <<-SCRIPT
    echo '
      server {
          listen       80;
          server_name  snashpots www.snashpots.com snashpots.com;
          root   /home/snashpots/snashpots/app;

          location / {
              index  index.php;
          }

          location ~ \.php$ {
              try_files $uri $uri/ /index.php?$args;
              fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
              fastcgi_index  index.php;
              fastcgi_pass   127.0.0.1:9000;
              include        fastcgi_params;
          }

          location = /favicon.ico {
            log_not_found off;
          }
      }
    ' > /etc/nginx/conf.d/snashpots.conf

    mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf.bak
    service nginx restart
    service php-fpm restart
  SCRIPT
  config.vm.provision :shell, :inline  => configure_nginx

  config.ssh.keep_alive
end
