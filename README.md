# drrrnl-wp-plugin
News letter user request wordpress plugin
Installation instructions.
We suppose:
1. that the wordpress installation is under /var/www/wordpress/
2. you have a super-user account.

Instructions to execute on wordpress host
1. Download the drrr_nl.tar.gz file
2. Move it in the plugins folder:
    sudo mv drrr-nl.tar.gz /var/www/wordpress/wp-content/plugins/
2. Go to the wordpress plugins directory:
    cd /var/www/wordpress/wp-content/plugins
3. Uncompress and extract files: 
    sudo tar -xvfz drrr-nl.tar.gz
    sudo chown -Rh www-data:www-data drrr-nl
4. delete the tar.gz file
    sudo rm drrr-nl.tar.gz

Plugin Activation
1. Connect on wordpress/wpadmin
2. Activate plugins Drrr News Letter Request
3. Build a public page containing the short code [drrr_form] ( drrr_form )
4. Build an admin page containing the short code [drrr_list] ( drrr_list )
