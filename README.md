# drrrnl-wp-plugin
<br/>News letter user request wordpress plugin
<br/>This plugin allows users to let their contact details on a wordpress site.

<bold>Installation instructions:</bold>
</br>We suppose:
1. that the wordpress installation is under /var/www/wordpress/
2. you have a super-user account.

Instructions to execute on wordpress host
1. Download the drrr_nl folder
2. Move it in the plugins folder:
    sudo mv drrr-nl /var/www/wordpress/wp-content/plugins/
2. Go to the wordpress plugins directory:
    cd /var/www/wordpress/wp-content/plugins
3. Change the rights on file: 
    sudo chown -Rh www-data:www-data drrr-nl

Plugin Activation
1. Connect on wordpress/wpadmin
2. Activate plugins Drrr News Letter Request
3. Build a public page containing the short code [drrr_form] ( drrr_form )
4. Build an admin page containing the short code [drrr_list] ( drrr_list )
