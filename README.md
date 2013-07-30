snashpots
=========

image sharing website

Running on Mountain Lion:
========================

Mountain Lion got rid of the ~/Sites directory so you'll need to
do a bit of monkeying around in order to get this up and running.

``` bash
mkdir ~/Sites
sudo chown :_www ~/Sites
```

Then create (or reconfigure) the apache config file that
allows sites to be served from the ~/Sites directory:

``` bash
sudo vim /etc/apache2/users/$(whoami).conf
```

Edit the file to contain this:

``` config
<Directory "/Users/<your_username>/Sites/">
    Options Indexes MultiViews +FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
```

In particular, if the file already exists, these 2 configurations
should be updated:

``` config
    Options Indexes MultiViews +FollowSymLinks
    AllowOverride All
```

Restart apache:
``` bash
sudo apachectl restart
```

Now, before you follow the installation instructions below, first

``` bash
cd ~/Sites
```

Then, when you are done following the installation instructions,
point your browser at `http://localhost/~<your_username>/snashpots/app`
and you should see a "hello world" page with an awesome misspelling of snapshots.

If `http://localhost/~<your_username>/snapshots/app/bootstrap.php` works but
`http://localhost/~<your_username>/snapshots/app` doesn't, then you should
edit app/.htaccess and follow the instructions in the comment there.

Installing
====

```bash
git clone git@github.com:dev-quest/snashpots.git
cd snashpots
curl -s https://getcomposer.org/installer | php -d detect_unicode=Off
php composer.phar install
cd app/
cp .htaccess.example .htaccess
```

Point your browser at the app/ directory.

If you see a plaintext PHP file, you should do: 

```bash
$ sudo vim /etc/apache2/httpd.conf
```

And uncomment this line:

```
#LoadModule php5_module libexec/apache2/libphp5.so  
```


Permissions
====

If you get forbidden errors or 500 errors, you might need to change the permissions
on some of the files. (The following assumes that the user under which apache is running
is `_www`, but that might not be the case, especially if you're trying this on linux.)

``` bash
cd ~/Sites/snashpots
sudo chown :_www .
chmod g+r .
sudo chown -R :_www logs cache app vendor templates
chmod g+wr logs cache
chmod -R g-r logs/README.md cache/README.md .git .gitignore README.md
```

Running from the pre-build virtual machine
===

1. install [vagrant](http://downloads.vagrantup.com/tags/v1.2.4)
1. run this:

``` bash
# run this from the snashpots project directory
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

SSH-ing into the virtual machine to play around
---

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
