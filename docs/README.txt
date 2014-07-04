Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/absolute/path/to/public/folder"
   ServerName home24.localhost.com

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "/absolute/path/to/public/folder">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>
