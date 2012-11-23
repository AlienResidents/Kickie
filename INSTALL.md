The following words cover installing Kickie on Ubuntu 12.04.1

You'll need the following software installed:

* bind9
* isc-dhcp-server
* atftpd
* php5-mysql
* mysql-server
* apache2
* libapache2-mod-php5

You should set the following variables in your bourne compatible shell of
choice

* BASE_DIR
* TFTP_DIR
* MEDIA_DIR
* HTML_DIR


Example

    BASEDIR=/app/kickstart
    BASEDIR=$BASE_DIR/pxe_boot
    MEDIA_DIR=$BASE_DIR/media
    HTML_DIR="$BASE_DIR/html
    DHCP_SUBNET=""


Install the required packages

    apt-get install bind9 isc-dhcp-server atftpd php5-mysql mysql-server \
    apache2 libapache2-mod-php5

 
Then configure the atftp server for use in xinetd using the variables you've set

    cp atftp /etc/xinetd.d/


Restart xinetd

    stop xinetd
    start xinetd


Configure apache accordingly from your variables

    Alias /kickstart /app/kickstart/html
    Alias /ubuntu /app/kickstart/media
    <Location /ubuntu>
      Options Indexes
      Order allow,deny
      allow from all
    </Location>


Configure DHCP as needed

    cat dhcpd.conf >> /etc/dhcp/dhcpd.conf



















