snashpots
=========

the learn-to-become-a-dev workshop's imgur clone

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
<Directory "/Users/$your_username/Sites/">
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

If `http://localhost/~<your_username>/snashpots/app/bootstrap.php` works but
`http://localhost/$your_username/snashpots/app` doesn't, then you should
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
