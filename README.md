snashpots
=========

image sharing website


## Running from the pre-build virtual machine (Recommended) ##

1. Install [virtualbox](https://www.virtualbox.org/).
1. Add a host-only network adapter to virtualbox
(see [instructions](http://superuser.com/questions/429405/how-can-i-get-virtualbox-to-run-with-a-hosts-only-adapter#answer-429410)).
1. Double click on vboxnet0. Give it the IP address `192.168.56.1` and the
subnet mask `255.255.255.0`, and make sure the DHCP server is disabled.
1. Install [vagrant](http://downloads.vagrantup.com/tags/v1.2.4).
1. Run this:

``` bash
git clone git@github.com:dev-quest/snashpots.git
cd snashpots
vagrant up
```

That's it! You should now be able to navigate to 192.168.56.50 in your browser
and see a "hello world" page served by Slim.

The `vagrant up` command spins up a virtualmachine and configures it to
serve our snashpots website. Having a virtual machine running consumes
resources on your computer, though, so you should shut it down when you're not
using it:

``` bash
vagrant halt
```

`vagrant resume` starts it up again.

### SSH-ing into the virtual machine for development ###

``` bash
cat <<EOF >> ~/.ssh/config
Host snashpots
    HostName 192.168.56.50
    User snashpots
EOF
```

and now ssh into the box:

``` bash
ssh snashpots
# password is snashpots
```


## Running on Mountain Lion ##

You only need to do this if for some reason you don't want to use vagrant.

### Making a directory that apache can access ###

Mountain Lion got rid of the ~/Sites directory so you'll need to
do a bit of monkeying around in order to get this up and running.

``` bash
mkdir ~/Sites
sudo chown :_www ~/Sites
```

Then edit `/etc/apache2/users/$(whoami).conf`, which is the apache
config file that allows sites to be served from the ~/Sites directory:

``` bash
sudo vim /etc/apache2/users/$(whoami).conf
```

Make this file contain the following:

``` config
<Directory "/Users/<your_username>/Sites/">
    Options Indexes MultiViews +FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
```

If the file already existed for you, make sure you update these
2 configurations:

``` config
    Options Indexes MultiViews +FollowSymLinks
    AllowOverride All
```

Restart apache:

``` bash
sudo apachectl restart
```

### Installing the code ###

#### Downloading ####

```bash
cd ~/Sites
git clone git@github.com:dev-quest/snashpots.git
cd snashpots
curl -s https://getcomposer.org/installer | php -d detect_unicode=Off
php composer.phar install
cd app/
cp .htaccess.example .htaccess
```

Then, `vim .htaccess`. There are some instructions in the comments. Follow them.


#### Fixing permissions ####

``` bash
cd ~/Sites/snashpots
sudo chown :_www .
chmod g+r .
sudo chown -R :_www logs cache app vendor templates
chmod g+wr logs cache
chmod -R g-r logs/README.md cache/README.md .git .gitignore README.md
```


#### Testing it out ####

First go to `http://localhost/~<your_username>/snapshots/app/boostrap.php`
in your browser.  If you see a plaintext PHP file, you don't have php
enabled in apache. To enable it:

```bash
sudo vim /etc/apache2/httpd.conf
```

And uncomment this line:

```
#LoadModule php5_module libexec/apache2/libphp5.so  
```

Reload the page in your browser. You should now see a properly rendered 
(albeit simple) html page saying "hello world". Once this works, try going to
`http://localhost/~<your_username>/snashpots/app` and you should once again
see "Hello World".
