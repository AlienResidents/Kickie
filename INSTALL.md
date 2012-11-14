The following words cover installing Kickie on Ubuntu 12.04.1

You'll need the following software installed:

* bind9
* isc-dhcp-server
* tftpd
* php5-mysql
* mysql-server
* apache2
* libapache2-mod-php5
* 

You should set the following variables in your bourne compatible shell of
choice

BASEDIR


Example

    BASEDIR=/app/kickstart


Install the required packages

    apt-get install bind9 isc-dhcp-server tftpd php5-mysql mysql-server \
    apache2 libapache2-mod-php5

 
Then configure the atftp server for use in xinetd

    cat > /etc/xinetd.d/atftpd << EOF
    service tftp
    {
       protocol = udp
       port = 69
       socket_type = dgram
       wait = yes
       user = nobody
       server = /usr/sbin/atftpd
       server_args = /app/kickstart/pxe_boot -s
       disable = no
    }
    EOF


Restart xinetd

    stop xinetd
    start xinetd


